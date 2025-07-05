<template>
    <div class="relative w-full my-3 px-1" data-md-input="true">
        <!-- Label flotante -->
        <label
            v-if="label"
            class="absolute text-sm transition-all duration-300 ease-in-out px-1 pointer-events-none z-10 flex items-center gap-1 transform"
            :class="[
                isFocused || modelValue
                    ? `text-[0.75rem] -top-2.5 scale-90 ${labelColor}`
                    : 'top-2.5 scale-100 text-gray-500',
                iconLeft || iconClass ? 'left-10' : 'left-3'
            ]"
            :style="{ backgroundColor }"
        >
            <!-- Asterisco si es requerido -->
            <span
                v-if="required && !modelValue && !errorText"
                class="text-red-500 font-bold text-base leading-none"
            >*</span>

            <!-- Palomita si success -->
            <svg
                v-else-if="success && !errorText"
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4 text-[var(--color-primary-hover)] ml-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>

            <!-- Equis si error -->
            <svg
                v-else-if="errorText"
                xmlns="http://www.w3.org/2000/svg"
                class="w-4 h-4 text-[var(--color-complement-2)] ml-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>

            {{ label }}
        </label>

        <!-- Icono izquierdo -->
        <div
            v-if="iconLeft || iconClass"
            class="absolute left-3 h-10 flex items-center pointer-events-none"
            :style="{ color: borderColor }"
        >
            <slot name="iconLeft" v-if="iconLeft" />
            <i v-else :class="iconClass" class="text-base leading-none"></i>
        </div>

        <!-- Input visual -->
        <div
            tabindex="0"
            class="w-full h-10 rounded-xl text-sm flex items-center cursor-pointer transition-all duration-300 ease-in-out shadow-sm ring-1 ring-inset"
            :class="{ 'pl-10 pr-4': iconLeft || iconClass, 'px-4': !iconLeft && !iconClass }"
            @click="toggleOpen"
            @focus="isFocused = true"
            @blur="handleBlur"
            :style="{
                backgroundColor,
                borderColor,
                '--tw-ring-color': borderColor,
                borderWidth: '1px'
            }"
        >
            <span class="truncate select-none text-gray-800 dark:text-gray-100"
                :class="{ 'opacity-50': !modelValue }">
                {{
                    props.options.find(opt => opt.value === props.modelValue)?.label || ''
                }}
            </span>
        </div>

        <!-- Dropdown -->
        <transition name="fade">
            <ul v-if="isOpen && options.length"
                class="absolute z-20 left-2 right-2 mt-1 max-h-60 overflow-auto rounded-xl border border-[var(--color-primary-light)] bg-[#f3f4f6] dark:bg-[#101828] shadow-lg text-sm text-gray-800 dark:text-gray-100"
            >
                <li v-for="(option, index) in options" :key="index" @click="selectOption(option)"
                    class="px-4 py-2 hover:bg-[var(--color-primary-light)] cursor-pointer transition-colors"
                >
                    {{ option.label }}
                </li>
            </ul>
        </transition>

        <!-- Helper o error -->
        <div class="flex items-center justify-between text-xs px-1 mt-1 leading-tight">
            <div
                class="ml-1"
                :class="errorText ? 'text-[var(--color-complement-2)] text-sm' : 'text-gray-400'"
            >
                {{ errorText || helper }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, useSlots } from 'vue'

const props = defineProps({
    modelValue: [String, Number, Object],
    options: { type: Array, default: () => [] },
    label: { type: String, default: 'Selecciona una opción' },
    iconClass: { type: String, default: '' },
    error: [Boolean, String, Array],
    success: Boolean,
    required: Boolean,
    helper: String
})

const emit = defineEmits(['update:modelValue'])

const isOpen = ref(false)
const iconLeft = computed(() => !!useSlots().iconLeft)
const isFocused = ref(false)
const isDark = ref(false)
const internalError = ref('')

const backgroundColor = computed(() => isDark.value ? '#101828' : '#f3f4f6')

const borderColor = computed(() => {
    if (internalError.value || props.error) return 'var(--color-complement-2)'
    if (props.success) return 'var(--color-primary-hover)'
    if (isFocused.value) return 'var(--color-primary)'
    return 'var(--color-primary-light)'
})

const labelColor = computed(() =>
    internalError.value || props.error
        ? 'text-[var(--color-complement-2)]'
        : 'text-[var(--color-primary)]'
)

const errorText = computed(() => {
    if (internalError.value) return internalError.value
    if (Array.isArray(props.error)) return props.error[0]
    if (typeof props.error === 'string') return props.error
    return null
})

function selectOption(option) {
    emit('update:modelValue', option.value)
    isOpen.value = false
}

function toggleOpen() {
    isOpen.value = !isOpen.value
}

function handleBlur() {
    setTimeout(() => {
        isOpen.value = false
        isFocused.value = false
    }, 100)
}

function validate() {
    if (props.required && (props.modelValue === null || props.modelValue === '' || props.modelValue === undefined)) {
        internalError.value = 'Este campo es obligatorio'
        return false
    }

    internalError.value = ''
    return true
}

onMounted(() => {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)')
    isDark.value = prefersDark.matches
    prefersDark.addEventListener('change', (e) => {
        isDark.value = e.matches
    })
})

watch(() => props.modelValue, (val) => {
    if (props.required && (val === null || val === '' || val === undefined)) {
        internalError.value = 'Este campo es obligatorio'
    } else {
        internalError.value = ''
    }
})

defineExpose({
    validate,
})
</script>
