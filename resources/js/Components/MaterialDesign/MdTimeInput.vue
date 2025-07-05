<template>
    <div class="relative w-full my-3 px-1" data-md-input="true" ref="rootRef">
        <!-- Label flotante -->
        <label
            v-if="label"
            :for="id"
            class="absolute text-sm transition-all duration-300 ease-in-out px-1 pointer-events-none z-10 flex items-center gap-1 transform"
            :class="[
                isFocused || internalValue
                    ? `text-[0.75rem] -top-2.5 scale-90 ${labelColor}`
                    : 'top-2.5 scale-100 text-gray-500',
                iconLeft || iconClass ? 'left-10' : 'left-3'
            ]"
            :style="{ backgroundColor }"
        >
            <span
                v-if="required && !internalValue && !errorText"
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

        <!-- Icono izquierdo -->
        <div
            v-if="iconLeft || iconClass"
            class="absolute left-3 h-10 flex items-center pointer-events-none"
            :style="{ color: borderColor }"
        >
            <slot name="iconLeft" v-if="iconLeft" />
            <i v-else :class="iconClass" class="text-base leading-none"></i>
        </div>

        <!-- Input principal (readonly para forzar uso de reloj futuro) -->
        <input
            type="text"
            :id="id"
            :name="name"
            ref="inputRef"
            class="w-full border rounded-xl text-gray-800 dark:text-gray-100 placeholder-white focus:outline-none focus:ring-2 transition-all duration-300 ease-in-out shadow-sm focus:shadow-md"
            :class="{
                'opacity-50 cursor-not-allowed': disabled || readonly,
                'pl-10 pr-10': iconLeft || iconClass,
                'px-4 pr-10': !iconLeft && !iconClass,
                'ring-1 ring-inset': success
            }"
            :value="internalValue"
            :placeholder="showRealPlaceholder ? placeholder : ' '"
            :readonly="true"
            :disabled="disabled"
            @focus.prevent="openClock"
            @click.prevent="openClock"
            :style="{
                backgroundColor,
                borderColor,
                '--tw-ring-color': borderColor
            }"
        />

        <!-- Clear -->
        <button
            v-if="internalValue && !readonly && !disabled"
            class="absolute right-3 transform -translate-y-1/2 text-[var(--color-primary)] hover:text-[var(--color-primary-light)]"
            :style="{ top: errorText ? 'calc(1/2 * 55%)' : 'calc(1/2 * 73%)' }"
            @click.prevent="limpiar"
            tabindex="-1"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                viewBox="0 0 28 28" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <!-- Error / Helper -->
        <div class="text-xs px-1 mt-1 leading-tight ml-1" :class="errorText ? 'text-[var(--color-complement-2)] text-sm' : 'text-gray-400'">
            {{ errorText || helper }}
        </div>

        <!-- Popover del reloj -->
        <div v-if="showClock" class="absolute top-full left-0 w-[300px] z-50 -mt-2 -ml-2 bg-white dark:bg-[var(--color-surface)] rounded-xl shadow-xl p-3">
            <MdTimeClock
                :value="`${(selectedHour ?? 0).toString().padStart(2, '0')}:${(selectedMinute ?? 0).toString().padStart(2, '0')}`"
                :ampm="ampm"
                @update:value="val => {
                    const [hh, mm] = val.split(':').map(Number)
                    selectedHour = hh
                    selectedMinute = mm
                }"
                @update:ampm="ampm = $event"
                @confirm="confirmTime"
            />

        </div>


    </div>
</template>

<script setup>
import { ref, watch, computed, useSlots, onMounted } from 'vue'
import MdTimeClock from './MdTimeClock.vue'
import { onClickOutside } from '@vueuse/core'

/* Props */
const props = defineProps({
    modelValue: String,
    required: Boolean,
    label: String,
    name: String,
    id: String,
    placeholder: String,
    disabled: Boolean,
    readonly: Boolean,
    success: { type: Boolean, default: false },
    iconClass: { type: String, default: '' },
    helper: { type: String, default: '' },
    regex: RegExp,
    error: [Boolean, String, Array]
})

/* Emits */
const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

/* Refs */
const rootRef = ref(null)
const isFocused = ref(false)
const internalValue = ref(props.modelValue ?? '')
const internalError = ref('')
const inputRef = ref(null)
const isDark = ref(false)
const showRealPlaceholder = false
const slots = useSlots()

/* Reloj */
const showClock = ref(false)
const mode = ref('hour')
const selectedHour = ref(null)
const selectedMinute = ref(null)
const ampm = ref('AM')
const is24 = true // cambia a false si quieres usar AM/PM

/* Watchers */
watch(() => props.modelValue, (val) => {
    internalValue.value = val
    if (val && /^\d{2}:\d{2}$/.test(val)) {
        const [h, m] = val.split(':').map(Number)
        selectedHour.value = h
        selectedMinute.value = m
        ampm.value = h >= 12 ? 'PM' : 'AM'
    }
})

watch(internalValue, (val) => {
    internalError.value = ''
    if (props.required && !val) internalError.value = 'Este campo es obligatorio'
    else if (props.regex && val && !props.regex.test(val)) internalError.value = 'Formato incorrecto'
})

/* Computed */
const iconLeft = computed(() => !!slots.iconLeft)

const backgroundColor = computed(() => isDark.value ? '#101828' : '#f3f4f6')

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

/* Métodos */

function updateValue(val) {
    internalValue.value = val
    emit('update:modelValue', val)
}

function onFocus(e) {
    isFocused.value = true
    emit('focus', e)
}

function onBlur(e) {
    isFocused.value = false
    emit('blur', e)
}

function limpiar() {
    internalValue.value = ''
    emit('update:modelValue', '')
}

/* Abrir popover del reloj */
function openClock() {
    if (!props.readonly && !props.disabled) {
        showClock.value = true
        mode.value = 'hour'
    }
}

function clockValueUpdate(val) {
    if (mode.value === 'hour') {
        selectedHour.value = val
        mode.value = 'minute'
    } else {
        selectedMinute.value = val
    }
}

function confirmTime() {
    let h = selectedHour.value ?? 0
    const m = selectedMinute.value ?? 0

    if (!is24) {
        if (ampm.value === 'PM' && h < 12) h += 12
        if (ampm.value === 'AM' && h === 12) h = 0
    }

    const formatted = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}`
    updateValue(formatted)
    showClock.value = false
}


onClickOutside(rootRef, () => {
    showClock.value = false
})

/* Dark mode */
onMounted(() => {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)')
    isDark.value = prefersDark.matches
    prefersDark.addEventListener('change', (e) => {
        isDark.value = e.matches
    })
})
</script>
