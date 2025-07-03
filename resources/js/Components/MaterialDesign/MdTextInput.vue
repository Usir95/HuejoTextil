<template>
    <div class="relative w-full">
        <!-- Label flotante -->
        <label v-if="label" :for="id"
            class="absolute text-sm  transition-all duration-300 ease-in-out px-1 pointer-events-none z-10 flex items-center gap-1 transform"
            :class="[
                isFocused || internalValue
                    ? `text-[0.75rem] -top-2.5 scale-90 ${labelColor}`
                    : 'top-2.5 scale-100 text-gray-500',
                iconLeft || iconClass ? 'left-10' : 'left-3'
            ]" :style="{ backgroundColor }">
            <!-- Asterisco si es requerido -->
            <span v-if="required && !success && !error" class="text-red-500 font-bold text-base leading-none">*</span>

            <!-- Palomita animada -->
            <svg v-show="success && !error" xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4 text-[var(--color-primary-hover)] ml-1 transition-all duration-300 transform scale-100"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
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
            class="w-full h-10 border rounded-xl text-gray-800 dark:text-gray-100 placeholder-transparent focus:outline-none focus:ring-2 transition-all duration-300 ease-in-out shadow-sm focus:shadow-md"
            :class="{
                'opacity-50 cursor-not-allowed': disabled || readonly,
                'pl-10 pr-4': iconLeft || iconClass,
                'px-4': !iconLeft && !iconClass,
                'ring-1 ring-inset': success
            }" :placeholder="placeholder || label" :value="internalValue" :disabled="disabled" :readonly="readonly"
            :minlength="minlength" :maxlength="maxlength" @focus="onFocus" @blur="onBlur"
            @input="updateValue($event.target.value)" :style="{
                backgroundColor,
                borderColor,
                '--tw-ring-color': borderColor
            }" />

        <!-- Error textual -->
        <div v-if="errorText" class="mt-1 text-sm text-[var(--color-complement-2)] px-1">
            {{ errorText }}
        </div>

        <!-- Contador de caracteres -->
        <div v-if="showCharCounter" class="text-right text-xs px-1 mt-0.5" :class="charCountColor">
            {{ internalValue.length }} / {{ maxlength }}
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed, useSlots, onMounted, onBeforeUnmount } from 'vue'
import IMask from 'imask'

/* ======================= Props ========================== */
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
    mask: [String, Object],
    minlength: Number,
    maxlength: Number,
    inputRestrict: {
        type: String,
        validator: (val) => ['letters', 'numbers', 'alphanumeric', 'none'].includes(val),
        default: 'none'
    },
})

/* ======================= Emits ========================== */
const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

/* ======================= Refs ========================== */
const isFocused = ref(false)
const internalValue = ref(props.modelValue ?? '')
const isDark = ref(false)
const slots = useSlots()
const inputRef = ref(null)
let maskRef = null

/* ======================= Watch modelValue ========================== */
watch(() => props.modelValue, (val) => {
    internalValue.value = val
    if (maskRef && maskRef.value !== val) maskRef.value = val
})

/* ======================= Watch mask ========================== */
watch(() => props.mask, (newMask) => {
    if (inputRef.value) {
        if (maskRef) maskRef.destroy()

        maskRef = IMask(
            inputRef.value,
            typeof newMask === 'string' ? { mask: newMask } : newMask
        )

        maskRef.on('accept', () => {
            emit('update:modelValue', maskRef.value)
        })
    }
})

/* ======================= Computed ========================== */
const iconLeft = computed(() => !!slots.iconLeft)

const backgroundColor = computed(() =>
    isDark.value ? '#101828' : '#f3f4f6'
)

const borderColor = computed(() => {
    if (props.error) return 'var(--color-complement-2)'
    if (props.success) return 'var(--color-primary-hover)'
    if (isFocused.value) return 'var(--color-primary)'
    return 'var(--color-primary-light)'
})

const labelColor = computed(() =>
    props.error ? 'text-[var(--color-complement-2)]' : 'text-[var(--color-primary)]'
)

const errorText = computed(() => {
    if (Array.isArray(props.error)) return props.error[0]
    if (typeof props.error === 'string') return props.error
    return null
})

const charCountColor = computed(() => {
    const length = internalValue.value?.length || 0
    const max = props.maxlength
    if (!max) return 'text-gray-500'
    const percentage = (length / max) * 100
    if (percentage >= 100) return 'text-[var(--color-complement-2)]'
    if (percentage >= 90) return 'text-orange-500'
    return 'text-gray-500'
})

const showCharCounter = computed(() =>
    typeof props.maxlength === 'number' && !props.readonly && !props.disabled
)

/* ======================= Functions ========================== */
function updateValue(val) {
    let finalValue = val

    // Aplica restricciones si están habilitadas
    if (props.inputRestrict === 'letters') {
        // Letras con tildes, diéresis, ñ, Ñ, mayúsculas, minúsculas, espacios y guiones
        finalValue = finalValue.replace(/[^a-zA-ZÁÉÍÓÚÜÑáéíóúüñ\s\-]/g, '')
    } else if (props.inputRestrict === 'numbers') {
        // Números, punto decimal, comas, espacios y guiones (para negativos o formatos)
        finalValue = finalValue.replace(/[^0-9.,\s\-]/g, '')
    } else if (props.inputRestrict === 'alphanumeric') {
        // Letras, números, tildes, ñ, espacios, guiones, guion bajo, punto y coma
        finalValue = finalValue.replace(/[^a-zA-Z0-9ÁÉÍÓÚÜÑáéíóúüñ\s\-_.,;]/g, '')
    }else{
        // Letras, números, tildes, ñ, espacios, guiones, guion bajo, punto y coma
        finalValue = finalValue.replace(/[^a-zA-Z0-9ÁÉÍÓÚÜÑáéíóúüñ\s\-_.,;]/g, '')
    }

    if (props.uppercase) {
        finalValue = finalValue.toUpperCase()
    }

    internalValue.value = finalValue
    emit('update:modelValue', finalValue)
}


function onFocus(e) {
    isFocused.value = true
    emit('focus', e)
}

function onBlur(e) {
    isFocused.value = false
    emit('blur', e)
}

/* ======================= Dark Mode ========================== */
onMounted(() => {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)')
    isDark.value = prefersDark.matches
    prefersDark.addEventListener('change', (e) => {
        isDark.value = e.matches
    })

    /* Init mask if provided */
    if (props.mask && inputRef.value) {
        maskRef = IMask(
            inputRef.value,
            typeof props.mask === 'string' ? { mask: props.mask } : props.mask
        )
        maskRef.on('accept', () => {
            emit('update:modelValue', maskRef.value)
        })
    }
})

/* ======================= Cleanup ========================== */
onBeforeUnmount(() => {
    if (maskRef) {
        maskRef.destroy()
        maskRef = null
    }
})
</script>
