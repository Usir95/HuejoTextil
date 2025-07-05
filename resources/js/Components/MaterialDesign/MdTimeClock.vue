<template>
    <div
        class="w-72 rounded-xl shadow-lg bg-[var(--color-primary)] text-white px-4 py-3 flex items-center gap-2 text-[28px] select-none">
        <!-- Hora -->
        <div class="flex flex-col items-center">
            <button @click="incrementHour" class="text-sm hover:scale-110 transition">▲</button>
            <div class="font-semibold bg-white/20 rounded text-white px-2 w-[48px] text-center">
                {{ bufferMode ? bufferHour : hourPad }}
            </div>
            <button @click="decrementHour" class="text-sm hover:scale-110 transition">▼</button>
        </div>

        <div class="font-semibold">:</div>

        <!-- Minuto -->
        <div class="flex flex-col items-center">
            <button @click="incrementMinute" class="text-sm hover:scale-110 transition">▲</button>
            <div class="font-semibold px-2 w-[48px] text-center">
                {{ bufferMode ? bufferMinute : minutePad }}
            </div>
            <button @click="decrementMinute" class="text-sm hover:scale-110 transition">▼</button>
        </div>

        <!-- AM/PM -->
        <div v-if="!is24" class="ml-4 flex gap-2 text-sm font-semibold">
            <span :class="{ 'opacity-100 underline': ampm === 'AM', 'opacity-50': ampm !== 'AM' }"
                class="cursor-pointer" @click="ampm = 'AM'">AM</span>
            <span :class="{ 'opacity-100 underline': ampm === 'PM', 'opacity-50': ampm !== 'PM' }"
                class="cursor-pointer" @click="ampm = 'PM'">PM</span>
        </div>

        <!-- OK -->
        <div :class="['text-sm font-semibold cursor-pointer', !is24 ? 'ml-auto' : '']" @click="confirm">
            Ok
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, computed, watch } from 'vue'

/* Props */
const props = defineProps({
    is24: { type: Boolean, default: true },
    value: String, // ej. "12:34"
    ampm: { type: String, default: 'AM' }
})

/* Emit */
const emit = defineEmits(['update:value', 'update:ampm', 'confirm'])

/* State */
const hour = ref(12)
const minute = ref(0)
const inputBuffer = ref('')
const ampm = ref(props.ampm)

/* Inicialización desde `value` */
if (props.value?.includes(':')) {
    const [hh, mm] = props.value.split(':')
    hour.value = parseInt(hh)
    minute.value = parseInt(mm)
}

/* Format */
const hourPad = computed(() => hour.value.toString().padStart(2, '0'))
const minutePad = computed(() => minute.value.toString().padStart(2, '0'))

/* Buffer display */
const bufferMode = computed(() => inputBuffer.value.length > 0 && inputBuffer.value.length < 4)
const bufferHour = computed(() =>
    inputBuffer.value.slice(0, 2).padEnd(2, '_')
)
const bufferMinute = computed(() =>
    inputBuffer.value.slice(2).padEnd(2, '_')
)

/* AMPM */
watch(ampm, (val) => emit('update:ampm', val))

onMounted(() => {
    window.addEventListener('keydown', handleKeyInput)
})

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeyInput)
})

/* Confirmar */
function confirm() {
    console.log('Ok');

    if (inputBuffer.value.length < 4) return
    emit('update:value', `${hourPad.value}:${minutePad.value}`)
    inputBuffer.value = ''
    emit('confirm')
}

function handleKeyInput(event) {
    const key = event.key

    if (key === 'Escape') {
        inputBuffer.value = ''
        return
    }

    if (key === 'Enter') {
        confirm()
        return
    }

    if (key === 'Backspace') {
        inputBuffer.value = inputBuffer.value.slice(0, -1)

        if (inputBuffer.value.length === 0) {
            hour.value = 0
            minute.value = 0
        }

        return
    }

    if (/^[0-9]$/.test(key)) {
        if (inputBuffer.value.length < 4) {
            inputBuffer.value += key
        }

        if (inputBuffer.value.length === 4) {
            updateFromBuffer()
        }
    }
}

function updateFromBuffer() {
    const hh = parseInt(inputBuffer.value.slice(0, 2))
    const mm = parseInt(inputBuffer.value.slice(2))

    if (props.is24) {
        hour.value = Math.min(hh, 23)
    } else {
        hour.value = Math.max(1, Math.min(hh, 12))
    }

    minute.value = Math.min(mm, 59)
    // se puede mantener el buffer visible o borrarlo
    // aquí se mantiene para mostrar que fue ingresado
}

/* Increment/decrement con límites */
function incrementHour() {
    inputBuffer.value = ''
    if (props.is24) {
        hour.value = (hour.value + 1) % 24
    } else {
        hour.value = hour.value >= 12 ? 1 : hour.value + 1
    }
}

function decrementHour() {
    inputBuffer.value = ''
    if (props.is24) {
        hour.value = (hour.value - 1 + 24) % 24
    } else {
        hour.value = hour.value <= 1 ? 12 : hour.value - 1
    }
}

function incrementMinute() {
    inputBuffer.value = ''
    minute.value = (minute.value + 1) % 60
}

function decrementMinute() {
    inputBuffer.value = ''
    minute.value = (minute.value - 1 + 60) % 60
}
</script>
