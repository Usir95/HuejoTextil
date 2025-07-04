<template>
    <div class="w-72 bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 select-none">
        <!-- Encabezado con navegación -->
        <div class="flex items-center justify-between mb-3">
            <button @click="prevMonth" class="text-gray-600 dark:text-gray-300 hover:text-primary">
                <i class="fas fa-chevron-left"></i>
            </button>
            <span class="font-semibold text-sm text-gray-800 dark:text-gray-100">
                {{ meses[mes] }} {{ anio }}
            </span>
            <button @click="nextMonth" class="text-gray-600 dark:text-gray-300 hover:text-primary">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>

        <!-- Días de la semana -->
        <div class="grid grid-cols-7 text-xs font-semibold text-center text-gray-500 dark:text-gray-400 mb-1">
            <div v-for="d in dias" :key="d">{{ d }}</div>
        </div>

        <!-- Días del mes -->
        <div class="grid grid-cols-7 text-sm text-center">
            <div
                v-for="(d, index) in diasCalendario"
                :key="index"
                class="py-1.5"
            >
                <button
                    v-if="d"
                    @click="seleccionarDia(d)"
                    :class="[
                        'w-8 h-8 rounded-full flex items-center justify-center transition duration-200',
                        esSeleccionado(d) ? 'text-white font-semibold ring-2 ring-white' : '',
                        esHoy(d) && !esSeleccionado(d) ? 'text-[var(--color-primary)] font-bold' : '',
                        !esSeleccionado(d) ? 'hover:bg-[var(--color-primary-light)]/20 dark:hover:bg-white/20' : ''
                    ]"
                    :style="esSeleccionado(d) ? { backgroundColor: 'var(--color-primary)' } : {}"

                >
                    {{ d }}
                </button>
                <span v-else></span>
            </div>
        </div>

        <!-- Botón limpiar -->
        <div class="mt-4 flex justify-end">
            <button @click="$emit('clear')" class="text-xs text-gray-500 hover:text-red-500">
                Limpiar
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import dayjs from 'dayjs'

const props = defineProps({
    modelValue: String
})

const emit = defineEmits(['update:modelValue', 'close', 'clear'])

const hoy = dayjs()
const fechaActual = props.modelValue ? dayjs(props.modelValue) : hoy

const anio = ref(fechaActual.year())
const mes = ref(fechaActual.month()) // 0-indexed
const diaSeleccionado = ref(fechaActual.date())

const dias = ['dom', 'lun', 'mar', 'mié', 'jue', 'vie', 'sáb']
const meses = [
    'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
]

const diasCalendario = computed(() => {
    const inicioMes = dayjs(`${anio.value}-${mes.value + 1}-01`)
    const totalDias = inicioMes.daysInMonth()
    const primerDiaSemana = inicioMes.day()

    const diasArray = []

    for (let i = 0; i < primerDiaSemana; i++) {
        diasArray.push(null)
    }

    for (let d = 1; d <= totalDias; d++) {
        diasArray.push(d)
    }

    return diasArray
})

function esHoy(d) {
    return hoy.date() === d && hoy.month() === mes.value && hoy.year() === anio.value
}

function esSeleccionado(d) {
    const actual = dayjs(props.modelValue)
    return (
        actual.date() === Number(d) &&
        actual.month() === mes.value &&
        actual.year() === anio.value
    )
}

function seleccionarDia(d) {
    diaSeleccionado.value = d
    const fechaSeleccionada = dayjs().year(anio.value).month(mes.value).date(d).format('YYYY-MM-DD')
    emit('update:modelValue', fechaSeleccionada)
    emit('close')
}

function prevMonth() {
    const newDate = dayjs().year(anio.value).month(mes.value).date(1).subtract(1, 'month')
    anio.value = newDate.year()
    mes.value = newDate.month()
}

function nextMonth() {
    const newDate = dayjs().year(anio.value).month(mes.value).date(1).add(1, 'month')
    anio.value = newDate.year()
    mes.value = newDate.month()
}
</script>
