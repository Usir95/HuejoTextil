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
            <!-- Asterisco si es requerido y no tiene valor aún -->
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
        <input :id="id"  :name="name" ref="inputRef"
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
            :style="{ top: errorText ? 'calc(1/2 * 55%)' : 'calc(1/2 * 73%)' }"
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

/* ======================= Props ========================== */
const props = defineProps({
    modelValue: String,
    required: Boolean,
    label: String,
    placeholder: String,
    name: String,
    id: String,
    disabled: Boolean,
    readonly: Boolean,
    error: [Boolean, String, Array],
    success: { type: Boolean, default: false },
    iconClass: { type: String, default: '' },
    minlength: Number,
    maxlength: Number,
    helper: { type: String, default: '' }
})

/* ======================= Emits ========================== */
const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

/* ======================= Refs ========================== */
const isFocused = ref(false)
const internalValue = ref(props.modelValue ?? '')
const internalError = ref('')
const isDark = ref(false)
const slots = useSlots()
const inputRef = ref(null)
const showRealPlaceholder = false

/* ======================= Watchers ========================== */
watch(() => props.modelValue, (val) => {
    internalValue.value = val
})

watch(internalValue, (val) => {
    internalError.value = ''

    if (props.required && !val) {
        internalError.value = 'Este campo es obligatorio'
    } else if (!isEmail(val)) {
        internalError.value = 'Correo no válido'
    }
})

/* ======================= Computed ========================== */
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

/* ======================= Funciones ========================== */
function isEmail(val) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)
}

function updateValue(val) {
    internalValue.value = val
    emit('update:modelValue', val)
}

function onKeydown(event) {
    if (event.key === 'Tab') {
        const valid = validate()
        if (!valid) event.preventDefault()
    }
}

function onFocus(e) {
    isFocused.value = true
    emit('focus', e)
}

function onBlur(e) {
    isFocused.value = false
    if (internalValue.value && !isEmail(internalValue.value)) {
        internalError.value = 'Correo no válido'
    }
    emit('blur', e)
}

function limpiar() {
    internalValue.value = ''
    emit('update:modelValue', '')
}

defineExpose({
    validate
})

function validate() {
    let message = ''

    if (props.required && !internalValue.value) {
        message = 'Este campo es obligatorio'
    } else if (!isEmail(internalValue.value)) {
        message = 'Correo no válido'
    }

    internalError.value = message
    return message === ''
}

/* ======================= Dark Mode ========================== */
onMounted(() => {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)')
    isDark.value = prefersDark.matches
    prefersDark.addEventListener('change', (e) => {
        isDark.value = e.matches
    })
})
</script>

