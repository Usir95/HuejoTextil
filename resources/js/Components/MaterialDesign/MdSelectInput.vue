<template>
    <div ref="wrapperRef" class="relative w-full my-3 px-1" data-md-input="true">
        <!-- Label flotante -->
        <label
            v-if="label"
            class="absolute text-sm transition-all duration-300 ease-in-out px-1 pointer-events-none z-10 flex items-center gap-1 transform"
            :class="[
                isFocused || modelValue?.length || modelValue
                    ? `text-[0.75rem] -top-2.5 scale-90 ${labelColor}`
                    : 'top-2.5 scale-100 text-gray-500',
                iconLeft || iconClass ? 'left-10' : 'left-3'
            ]"
            :style="{ backgroundColor }"
        >
            <span
                v-if="required && (!modelValue || modelValue.length === 0) && !errorText"
                class="text-red-500 font-bold text-base leading-none"
            >*</span>
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
            class="w-full min-h-10 rounded-xl text-sm flex flex-wrap items-center gap-1 cursor-pointer transition-all duration-300 ease-in-out shadow-sm ring-1 ring-inset"
            :class="{ 'pl-10 pr-4': iconLeft || iconClass, 'px-4': !iconLeft && !iconClass }"
            @focus="toggleOpen"
            :style="{
                backgroundColor,
                borderColor,
                '--tw-ring-color': borderColor,
                borderWidth: '1px'
            }"
        >
            <template v-if="multiple">
                <span v-for="(option, i) in selectedOptions" :key="i" class="bg-[var(--color-primary)] text-white px-3 py-1 rounded-full text-xs flex items-center gap-1">
                    {{ option.label }}
                    <button type="button" class="ml-1 text-xs px-1 cursor-pointer" @click.stop="removeOption(option)">×</button>
                </span>
                <span v-if="!selectedOptions.length" class="opacity-50">Selecciona opciones</span>
            </template>
            <template v-else>
                <span class="truncate select-none text-gray-800 dark:text-gray-100"
                    :class="{ 'opacity-50': !modelValue }">
                    {{ options.find(opt => String(opt.value) === String(modelValue))?.label || '' }}
                </span>
            </template>
        </div>

        <!-- Dropdown -->
        <transition name="fade">
            <ul v-if="isOpen && options.length"
                class="absolute z-20 left-2 right-2 mt-1 max-h-60 overflow-auto rounded-xl border border-[var(--color-primary-light)] bg-[#f3f4f6] dark:bg-[#101828] shadow-lg text-sm text-gray-800 dark:text-gray-100"
            >
                <li @click.stop="clearSelection" class="px-4 py-2 italic text-gray-500 hover:bg-[var(--color-primary-light)] cursor-pointer transition-colors">
                    — Limpiar selección —
                </li>
                <li
                    v-for="(option, index) in options"
                    :key="index"
                    @click.stop="(e) => selectOption(option, e)"
                    class="px-4 py-2 hover:bg-[var(--color-primary-light)] cursor-pointer transition-colors"
                    :class="{ 'bg-[var(--color-primary-light)]': isSelected(option) }"
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
import { onClickOutside } from '@vueuse/core'

const props = defineProps({
    modelValue: [String, Number, Object, Array],
    options: { type: Array, default: () => [] },
    label: { type: String, default: 'Selecciona una opción' },
    iconClass: { type: String, default: '' },
    error: [Boolean, String, Array],
    success: Boolean,
    required: Boolean,
    helper: String,
    multiple: Boolean
})

const emit = defineEmits(['update:modelValue'])
const wrapperRef = ref(null)
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

const selectedOptions = computed(() => {
    if (!props.multiple) return []
    return props.options.filter(opt => props.modelValue?.includes(opt.value))
})

function isSelected(option) {
    if (props.multiple) return props.modelValue?.includes(option.value)
    return props.modelValue === option.value
}

function selectOption(option, event) {
    if (props.multiple) {
        const newValue = [...(props.modelValue || [])]
        const index = newValue.indexOf(option.value)
        if (index >= 0) newValue.splice(index, 1)
        else newValue.push(option.value)
        emit('update:modelValue', newValue)

        // ❗️Cerrar solo si NO está presionado Ctrl o Cmd
        if (!(event.ctrlKey || event.metaKey)) {
            isOpen.value = false
        }
    } else {
        emit('update:modelValue', option.value)
        isOpen.value = false
    }
}



function removeOption(option) {
    if (!props.multiple) return
    const newValue = props.modelValue.filter(val => val !== option.value)
    emit('update:modelValue', newValue)
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
    const isEmpty = props.multiple ? !props.modelValue?.length : (props.modelValue === null || props.modelValue === '' || props.modelValue === undefined)
    if (props.required && isEmpty) {
        internalError.value = 'Este campo es obligatorio'
        return false
    }
    internalError.value = ''
    return true
}

function clearSelection() {
    if (props.multiple) {
        emit('update:modelValue', [])
    } else {
        emit('update:modelValue', null)
    }
    isOpen.value = false
}

onMounted(() => {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)')
    isDark.value = prefersDark.matches
    prefersDark.addEventListener('change', (e) => {
        isDark.value = e.matches
    })
})

watch(() => props.modelValue, (val) => {
    const isEmpty = props.multiple ? !val?.length : (val === null || val === '' || val === undefined)
    if (props.required && isEmpty) {
        internalError.value = 'Este campo es obligatorio'
    } else {
        internalError.value = ''
    }
})


onClickOutside(wrapperRef, (event) => {
    isOpen.value = false
})

defineExpose({
    validate,
})
</script>
