<template>
    <div class="relative w-full my-3 px-1" data-md-input="true" ref="rootRef">
        <!-- Label flotante -->
        <label v-if="label" :for="id"
            class="absolute text-sm transition-all duration-300 ease-in-out px-1 pointer-events-none z-10 flex items-center gap-1 transform"
            :class="[
                isFocused || internalValue
                    ? `text-[0.75rem] -top-2.5 scale-90 ${labelColor}`
                    : 'top-2.5 scale-100 text-gray-500',
                iconLeft || iconClass ? 'left-10' : 'left-3'
            ]"
            :style="{ backgroundColor }"
        >
            <!-- Asterisco si es requerido y aún no hay valor -->
            <span
                v-if="required && !internalValue && !errorText"
                class="text-red-500 font-bold text-base leading-none"
            >*</span>

            <!-- Palomita si success y sin error -->
            <svg
                v-else-if="success && !errorText"
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4 text-[var(--color-primary-hover)] ml-1 transition-all duration-300 transform scale-100"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>

            <!-- Equis si hay error -->
            <svg
                v-else-if="errorText"
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4 text-[var(--color-complement-2)] ml-1 transition-all duration-300 transform scale-100"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>

            {{ label }}
        </label>


        <!-- Icono izquierdo (slot o clase) -->
        <div
            v-if="iconLeft || iconClass"
            class="absolute left-3 h-10 flex items-center pointer-events-none"
            :style="{ color: borderColor }"
        >
            <slot name="iconLeft" v-if="iconLeft" />
            <i v-else :class="iconClass" class="text-base leading-none"></i>
        </div>

        <!-- Input principal -->
        <input :id="id" :type="type" :name="name" ref="inputRef"
            class="w-full h-10 border rounded-xl text-gray-800 dark:text-gray-100 placeholder-white focus:outline-none focus:ring-2 transition-all duration-300 ease-in-out shadow-sm focus:shadow-md"
            :class="{
                'opacity-50 cursor-not-allowed': disabled || readonly,
                'pl-10 pr-4': iconLeft || iconClass,
                'px-4': !iconLeft && !iconClass,
                'ring-1 ring-inset': success
            }" :placeholder="showRealPlaceholder ? placeholder : ' '" :value="internalValue" :disabled="disabled" :readonly="readonly"
            :minlength="minlength" :maxlength="maxlength" @focus="onFocus" @blur="onBlur"   @keydown="onKeydown"
            @input="updateValue($event.target.value)" :style="{
                backgroundColor,
                borderColor,
                '--tw-ring-color': borderColor,
                transition: 'border-color 0.3s ease, background-color 0.3s ease'
            }" />

        <!-- Error o ayuda + contador -->
        <div class="flex items-center justify-between text-xs px-1 mt-1 leading-tight">
            <!-- Muestra error si existe, si no muestra helper -->
            <div class="ml-1" :class="errorText ? 'text-[var(--color-complement-2)] text-sm' : 'text-gray-400'">
                {{ errorText || helper }}
            </div>

            <!-- Contador de caracteres -->
            <div v-if="showCharCounter" :class="charCountColor" class="transition-all duration-300">
                {{ internalValue.length }} / {{ maxlength }}
            </div>
        </div>

        <!-- Botón clear -->
        <button
            v-if="internalValue && !readonly && !disabled"
            class="absolute right-3 transform -translate-y-1/2 cursor-pointer flex items-center justify-center transition-colors duration-200 text-[var(--color-primary)] hover:text-[var(--color-primary-light)]"
            style="top: calc(1/2 * 73%);"
            @click.prevent="limpiar"
            tabindex="-1"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                viewBox="0 0 28 28" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>

<script setup>
import { ref, watch, computed, useSlots, onMounted } from 'vue'

const props = defineProps({
    modelValue: [String, Number],
    required: Boolean,
    label: String,
    placeholder: String,
    type: { type: String, default: 'text' },
    name: String,
    id: String,
    disabled: Boolean,
    readonly: Boolean,
    uppercase: Boolean,
    error: [Boolean, String, Array],
    success: { type: Boolean, default: false },
    iconClass: { type: String, default: '' },
    minlength: Number,
    maxlength: Number,
    regex: RegExp,
    inputRestrict: {
        type: String,
        validator: (val) => ['numbers', 'decimal'].includes(val),
        default: 'numbers'
    },
    helper: { type: String, default: '' }
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

const isFocused = ref(false)
const internalValue = ref(props.modelValue?.toString() ?? '')
const internalError = ref('')
const isDark = ref(false)
const slots = useSlots()
const inputRef = ref(null)
const showRealPlaceholder = false

watch(() => props.modelValue, (val) => {
    internalValue.value = val?.toString() ?? ''
})

watch(internalValue, (val) => {
    internalError.value = ''

    if (props.required && val === '') {
        internalError.value = 'Este campo es obligatorio'
    } else if (props.minlength && val.length < props.minlength) {
        internalError.value = `Debe tener al menos ${props.minlength} caracteres`
    } else if (props.maxlength && val.length > props.maxlength) {
        internalError.value = `Debe tener máximo ${props.maxlength} caracteres`
    } else if (props.regex && !props.regex.test(val)) {
        internalError.value = 'El formato no es válido'
    }
})

const iconLeft = computed(() => !!slots.iconLeft)

const backgroundColor = computed(() =>
    isDark.value ? '#101828' : '#f3f4f6'
)

const borderColor = computed(() => {
    if (props.error || internalError.value) return 'var(--color-complement-2)'
    if (props.success) return 'var(--color-primary-hover)'
    if (isFocused.value) return 'var(--color-primary)'
    return 'var(--color-primary-light)'
})

const labelColor = computed(() =>
    props.error ? 'text-[var(--color-complement-2)]' : 'text-[var(--color-primary)]'
)

const errorText = computed(() => {
    if (internalError.value) return internalError.value
    if (Array.isArray(props.error)) return props.error[0]
    if (typeof props.error === 'string') return props.error
    return null
})

const charCountColor = computed(() => {
    const currentLength = internalValue.value?.length || 0
    const maxLength = props.maxlength
    if (!maxLength) return 'text-gray-500'

    const usedPercentage = (currentLength / maxLength) * 100
    if (currentLength > maxLength) return 'text-[var(--color-complement-2)]'
    if (usedPercentage >= 90) return 'text-[var(--color-primary)]'
    return 'text-gray-500'
})

const showCharCounter = computed(() =>
    typeof props.maxlength === 'number' && !props.readonly && !props.disabled
)

function updateValue(val) {
    let finalValue = val

    if (props.inputRestrict === 'numbers') {
        finalValue = finalValue.replace(/[^0-9]/g, '')
    } else if (props.inputRestrict === 'decimal') {
        finalValue = finalValue.replace(/[^0-9.]/g, '')
        finalValue = finalValue.replace(/^(\d*\.\d*).*$/, '$1')
    }

    if (props.uppercase) {
        finalValue = finalValue.toUpperCase()
    }

    internalValue.value = finalValue
    emit('update:modelValue', Number(finalValue))
}

function onKeydown(event) {
    if (event.key === 'Tab') {
    const valid = validate()
    if (!valid) {
        event.preventDefault()
    }
}

    const navKeys = ['Backspace', 'Tab', 'ArrowLeft', 'ArrowRight', 'Delete']

    if (navKeys.includes(event.key)) return

    if (props.inputRestrict === 'numbers' && !/[0-9]/.test(event.key)) {
        event.preventDefault()
    }

    if (props.inputRestrict === 'decimal') {
        const current = internalValue.value
        const nextChar = event.key
        if (!/[0-9.]/.test(nextChar)) {
            event.preventDefault()
        }
        if (nextChar === '.' && current.includes('.')) {
            event.preventDefault()
        }
    }
}

function onFocus(e) {
    isFocused.value = true
    emit('focus', e)
}

function onBlur(e) {
    isFocused.value = false
    if (props.regex && internalValue.value && !props.regex.test(internalValue.value)) {
        internalError.value = 'El formato no es válido'
    }
    emit('blur', e)
}

function limpiar() {
    internalValue.value = ''
    emit('update:modelValue', '')
}

defineExpose({ validate })

function validate() {
    let message = ''

    if (props.required && internalValue.value === '') {
        message = 'Este campo es obligatorio'
    } else if (props.minlength && internalValue.value.length < props.minlength) {
        message = `Debe tener al menos ${props.minlength} caracteres`
    } else if (props.maxlength && internalValue.value.length > props.maxlength) {
        message = `Debe tener máximo ${props.maxlength} caracteres`
    } else if (props.regex && !props.regex.test(internalValue.value)) {
        message = 'El formato no es válido'
    }

    internalError.value = message
    return message === ''
}

onMounted(() => {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)')
    isDark.value = prefersDark.matches
    prefersDark.addEventListener('change', (e) => {
        isDark.value = e.matches
    })
})
</script>

