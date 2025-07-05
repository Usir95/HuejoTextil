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
            <!-- Asterisco si es requerido y campo vacío -->
            <span
                v-if="required && !internalValue && !errorText"
                class="text-red-500 font-bold text-base leading-none"
            >*</span>

            <!-- Palomita -->
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

            <!-- X -->
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

        <!-- Input de tipo date -->
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
            @input="onUserInput"
            :style="{
                backgroundColor,
                borderColor,
                '--tw-ring-color': borderColor,
                transition: 'border-color 0.3s ease, background-color 0.3s ease'
            }"
        />

        <!-- Mensaje de error o ayuda -->
        <div class="flex items-center justify-between text-xs px-1 mt-1 leading-tight">
            <div class="ml-1" :class="errorText ? 'text-[var(--color-complement-2)] text-sm' : 'text-gray-400'">
                {{ errorText || helper }}
            </div>
        </div>

        <transition name="calendar-fade">
            <div
                v-if="showCalendar"
                ref="calendarRef"
                class="absolute z-50 mt-2"
                :style="{ top: '100%', left: '0' }"
            >
                <MdDateCalendar
                    v-model="internalValue"
                    @close="closeCalendar"
                    @clear="() => updateValue('')"
                    :minDate="props.minDate"
                    :maxDate="props.maxDate"
                    :disabledDates="props.disabledDates"
                    :disabledWeekdays="props.disabledWeekdays"
                />
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, watch, computed, useSlots, onMounted, onBeforeUnmount } from 'vue'
import MdDateCalendar from './MdDateCalendar.vue'
import dayjs from 'dayjs'
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
    helper: { type: String, default: '' },
    minDate: String,
    maxDate: String,
    disabledDates: Array,
    disabledWeekdays: Array
})


/* ======================= Emits ========================== */
const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

/* ======================= Refs ========================== */
const showCalendar = ref(false)
const isFocused = ref(false)
const internalValue = ref(props.modelValue ?? '')
const internalError = ref('')
const isDark = ref(false)
const slots = useSlots()
const inputRef = ref(null)
const calendarRef = ref(null)

/* ======================= Watchers ========================== */
watch(internalValue, (val) => {
    internalError.value = ''

    if (props.required && val === '') {
        internalError.value = 'Este campo es obligatorio'
    }

    // Mostrar en el input el valor formateado
    if (val && val.includes('-')) {
        const [yyyy, mm, dd] = val.split('-')
        if (inputRef.value) {
            inputRef.value.value = `${dd}/${mm}/${yyyy}`
        }
    }

    emit('update:modelValue', val)
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

const formattedValue = computed(() => {
    const [yyyy, mm, dd] = internalValue.value?.split('-') || []
    return dd && mm && yyyy ? `${dd}/${mm}/${yyyy}` : ''
})

/* ======================= Funciones ========================== */
function onUserInput(e) {
    let raw = e.target.value.replace(/\D/g, '').slice(0, 8)
    let formatted = ''

    if (raw.length >= 1) formatted += raw.slice(0, 2)
    if (raw.length >= 3) formatted += '/' + raw.slice(2, 4)
    if (raw.length >= 5) formatted += '/' + raw.slice(4)

    e.target.value = formatted

    if (raw.length === 8) {
        const dd = raw.slice(0, 2)
        const mm = raw.slice(2, 4)
        const yyyy = raw.slice(4)
        const parsed = `${yyyy}-${mm}-${dd}`
        const date = dayjs(parsed)

        if (isDateDisabled(date)) {
            internalError.value = 'Fecha no permitida'
            updateValue('')
        } else {
            updateValue(parsed)
        }
    } else {
        updateValue('')
    }
}

function isDateDisabled(date) {
    if (!date.isValid()) return true

    if (props.minDate && date.isBefore(dayjs(props.minDate), 'day')) return true
    if (props.maxDate && date.isAfter(dayjs(props.maxDate), 'day')) return true
    if (props.disabledDates?.some(fd => dayjs(fd).isSame(date, 'day'))) return true
    if (props.disabledWeekdays?.includes(date.day())) return true

    return false
}


function onKeydown(event) {
    if (event.key === 'Tab') {
        const valid = validate()
        if (!valid) {
            event.preventDefault()
        }
    }
}


function toggleCalendar() {
    if (!props.disabled && !props.readonly) {
        showCalendar.value = !showCalendar.value
    }
}

function handleClickOutside(event) {
    if (calendarRef.value && !calendarRef.value.contains(event.target) && inputRef.value && !inputRef.value.contains(event.target)) {
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
    if (props.required && internalValue.value === '') {
        internalError.value = 'Este campo es obligatorio'
    }
    emit('blur', e)
}

function validate() {
    let message = ''

    if (props.required && internalValue.value === '') {
        message = 'Este campo es obligatorio'
    }

    internalError.value = message
    return message === ''
}

defineExpose({ validate })

/* ======================= Dark Mode ========================== */
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

<style scoped>
.calendar-fade-enter-active,
.calendar-fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.calendar-fade-enter-from {
  opacity: 0;
  transform: translateY(-6px);
}

.calendar-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}

</style>
