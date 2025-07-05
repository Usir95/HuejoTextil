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
                <!-- Día válido -->
                <button
                    v-if="d && !estaDeshabilitado(d)"
                    @click="seleccionarDia(d)"
                    :class="[
                        'w-8 h-8 rounded-full flex items-center justify-center transition duration-200',
                        estaDentroRango(d) ? 'bg-[var(--color-primary)] text-white font-bold' : '',
                        esInicio(d) || esFin(d) ? 'ring-2 ring-white' : '',
                        esHoy(d) && !estaDentroRango(d) ? 'text-[var(--color-primary)] font-bold' : '',
                        !estaDentroRango(d) ? 'hover:bg-[var(--color-primary-light)]/20 dark:hover:bg-white/20' : ''
                    ]"
                >
                    {{ d }}
                </button>

                <!-- Día deshabilitado -->
                <span
                    v-else-if="d"
                    class="w-8 h-8 flex items-center justify-center text-gray-400 opacity-40 cursor-not-allowed"
                >
                    {{ d }}
                </span>

                <!-- Día vacío -->
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
import { ref, computed } from 'vue'
import dayjs from 'dayjs'

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({ start: '', end: '' })
    },
    minDate: String,
    maxDate: String,
    disabledDates: Array,
    disabledWeekdays: Array
})

const emit = defineEmits(['update:modelValue', 'close', 'clear'])

const hoy = dayjs()
const fechaInicio = props.modelValue.start ? dayjs(props.modelValue.start) : hoy

const anio = ref(fechaInicio.year())
const mes = ref(fechaInicio.month())

const dias = ['dom', 'lun', 'mar', 'mié', 'jue', 'vie', 'sáb']
const meses = [
    'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
    'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
]

const seleccion = ref({ ...props.modelValue })

const diasCalendario = computed(() => {
    const inicioMes = dayjs(`${anio.value}-${mes.value + 1}-01`)
    const totalDias = inicioMes.daysInMonth()
    const primerDiaSemana = inicioMes.day()
    const diasArray = []
    for (let i = 0; i < primerDiaSemana; i++) diasArray.push(null)
    for (let d = 1; d <= totalDias; d++) diasArray.push(d)
    return diasArray
})

function esHoy(d) {
    const fecha = dayjs().year(anio.value).month(mes.value).date(d)
    return fecha.isSame(hoy, 'day')
}

function esInicio(d) {
    return dayjs(seleccion.value.start).isSame(dayjs().year(anio.value).month(mes.value).date(d), 'day')
}

function esFin(d) {
    return dayjs(seleccion.value.end).isSame(dayjs().year(anio.value).month(mes.value).date(d), 'day')
}

function estaDentroRango(d) {
    const fecha = dayjs().year(anio.value).month(mes.value).date(d)
    const start = dayjs(seleccion.value.start)
    const end = dayjs(seleccion.value.end)
    return fecha.isAfter(start.subtract(1, 'day')) && fecha.isBefore(end.add(1, 'day'))
}

function seleccionarDia(d) {
    const fecha = dayjs().year(anio.value).month(mes.value).date(d)
    if (!seleccion.value.start || (seleccion.value.start && seleccion.value.end)) {
        seleccion.value = { start: fecha.format('YYYY-MM-DD'), end: '' }
    } else {
        const start = dayjs(seleccion.value.start)
        if (fecha.isBefore(start)) {
            seleccion.value = { start: fecha.format('YYYY-MM-DD'), end: start.format('YYYY-MM-DD') }
        } else {
            seleccion.value.end = fecha.format('YYYY-MM-DD')
        }
        emit('update:modelValue', { ...seleccion.value })
        setTimeout(() => emit('close'), 500)
    }
}

function estaDeshabilitado(d) {
    const fecha = dayjs().year(anio.value).month(mes.value).date(d)
    if (props.minDate && fecha.isBefore(dayjs(props.minDate), 'day')) return true
    if (props.maxDate && fecha.isAfter(dayjs(props.maxDate), 'day')) return true
    if (props.disabledDates?.some(fd => dayjs(fd).isSame(fecha, 'day'))) return true
    if (props.disabledWeekdays?.includes(fecha.day())) return true
    return false
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
