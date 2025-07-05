<template>
    <div class="relative w-full my-3 px-1" data-md-input="true" ref="rootRef">
        <!-- Label flotante -->
        <label v-if="label" :for="id"
            class="absolute text-sm transition-all duration-300 ease-in-out px-1 pointer-events-none z-10 flex items-center gap-1 transform"
            :class="[
                isFocused || (internalValue?.start && internalValue?.end)
                    ? `text-[0.75rem] -top-2.5 scale-90 ${labelColor}`
                    : 'top-2.5 scale-100 text-gray-500',
                iconLeft || iconClass ? 'left-10' : 'left-3'
            ]"
            :style="{ backgroundColor }"
        >
            <span
                v-if="required && (!internalValue?.start || !internalValue?.end) && !errorText"
                class="text-red-500 font-bold text-base leading-none"
            >*</span>

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

        <!-- Icono izquierdo -->
        <div
            v-if="iconLeft || iconClass"
            class="absolute left-3 h-10 flex items-center pointer-events-none"
            :style="{ color: borderColor }"
        >
            <slot name="iconLeft" v-if="iconLeft" />
            <i v-else :class="iconClass" class="text-base leading-none"></i>
        </div>

        <!-- Input -->
        <input :id="id" type="text" :name="name" ref="inputRef"
            class="w-full h-10 border rounded-xl text-gray-800 dark:text-gray-100 placeholder-transparent focus:outline-none focus:ring-2 transition-all duration-300 ease-in-out shadow-sm focus:shadow-md"
            :class="{
                'opacity-50 cursor-not-allowed': disabled || readonly,
                'pl-10 pr-4': iconLeft || iconClass,
                'px-4': !iconLeft && !iconClass,
                'ring-1 ring-inset': success
            }"
            :value="formattedValue"
            :disabled="disabled"
            :readonly="readonly"
            @click="toggleCalendar"
            @keydown="onKeydown"
            @focus="onFocus"
            @blur="onBlur"
            :style="{
                backgroundColor,
                borderColor,
                '--tw-ring-color': borderColor,
                transition: 'border-color 0.3s ease, background-color 0.3s ease'
            }"
        />

        <!-- Calendario -->
        <div v-if="showCalendar" ref="calendarRef" class="absolute z-50 mt-2">
            <MdDateRangeCalendar
                v-model="internalValue"
                :min-date="minDate"
                :max-date="maxDate"
                :disabled-dates="disabledDates"
                :disabled-weekdays="disabledWeekdays"
                @update:modelValue="updateValue"
                @close="closeCalendar"
                @clear="() => updateValue({ start: '', end: '' })"
            />
        </div>

        <!-- Mensaje de error o ayuda -->
        <div class="flex items-center justify-between text-xs px-1 mt-1 leading-tight">
            <div class="ml-1" :class="errorText ? 'text-[var(--color-complement-2)] text-sm' : 'text-gray-400'">
                {{ errorText || helper }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed, useSlots, onMounted, onBeforeUnmount } from 'vue'
import MdDateRangeCalendar from './MdDateRangeCalendar.vue'
import dayjs from 'dayjs'

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({ start: '', end: '' })
    },
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
    helper: { type: String, default: '' },
    minDate: String,
    maxDate: String,
    disabledDates: Array,
    disabledWeekdays: Array
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

const showCalendar = ref(false)
const isFocused = ref(false)
const internalError = ref('')
const isDark = ref(false)
const slots = useSlots()
const inputRef = ref(null)
const calendarRef = ref(null)

const internalValue = ref(
    props.modelValue && typeof props.modelValue === 'object'
        ? props.modelValue
        : { start: '', end: '' }
)

watch(() => props.modelValue, (val) => {
    if (val?.start && val?.end) {
        internalValue.value = val
    }
})

watch(internalValue, (val) => {
    internalError.value = ''

    if (props.required && (!val.start || !val.end)) {
        internalError.value = 'Este campo es obligatorio'
    }
})

const iconLeft = computed(() => !!slots.iconLeft)

const backgroundColor = computed(() => isDark.value ? '#101828' : '#f3f4f6')

const borderColor = computed(() => {
    if (props.error || internalError.value) return 'var(--color-complement-2)'
    if (props.success) return 'var(--color-primary-hover)'
    if (isFocused.value) return 'var(--color-primary)'
    return 'var(--color-primary-light)'
})

const labelColor = computed(() => props.error ? 'text-[var(--color-complement-2)]' : 'text-[var(--color-primary)]')

const errorText = computed(() => {
    if (internalError.value) return internalError.value
    if (Array.isArray(props.error)) return props.error[0]
    if (typeof props.error === 'string') return props.error
    return null
})

const formattedValue = computed(() => {
    const format = (dateStr) => {
        if (!dateStr || typeof dateStr !== 'string') return ''
        const [yyyy, mm, dd] = dateStr.split('-')
        return dd && mm && yyyy ? `${dd}/${mm}/${yyyy}` : ''
    }
    const start = format(internalValue.value.start)
    const end = format(internalValue.value.end)
    return start && end ? `${start} - ${end}` : ''
})

function toggleCalendar() {
    if (!props.disabled && !props.readonly) {
        showCalendar.value = !showCalendar.value
    }
}

function handleClickOutside(event) {
    if (
        calendarRef.value &&
        !calendarRef.value.contains(event.target) &&
        inputRef.value &&
        !inputRef.value.contains(event.target)
    ) {
        showCalendar.value = false
    }
}

function closeCalendar() {
    showCalendar.value = false
}

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
    if (props.required && (!internalValue.value.start || !internalValue.value.end)) {
        internalError.value = 'Este campo es obligatorio'
    }
    emit('blur', e)
}

function onKeydown(event) {
    if (event.key === 'Tab') {
        const valid = validate()
        if (!valid) {
            event.preventDefault()
        }
    }
}

function validate() {
    let message = ''
    if (props.required && (!internalValue.value.start || !internalValue.value.end)) {
        message = 'Este campo es obligatorio'
    }
    internalError.value = message
    return message === ''
}

defineExpose({ validate })

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)')
    isDark.value = prefersDark.matches
    prefersDark.addEventListener('change', (e) => {
        isDark.value = e.matches
    })
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>
