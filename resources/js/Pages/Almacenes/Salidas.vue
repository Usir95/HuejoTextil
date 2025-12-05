<template>
    <AppLayout title="Salidas">
        <section class="max-w-4xl mx-auto space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-4">
                <h2 class="text-lg font-semibold mb-1">
                    Escanear etiqueta
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Escanea el código QR con el TC27.
                </p>

                <section ref="ScanSection" class="space-y-3">
                    <MdTextInput
                        id="codigo_escaneado"
                        name="codigo_escaneado"
                        label="Código escaneado"
                        v-model="form.codigo_escaneado"
                        :uppercase="true"
                        :autofocus="true"
                        :maxlength="150"
                        helper="Formato: PROD-11-COL-2-CAL-1-MOV-1"
                        :error="form.errors.codigo_escaneado"
                        :success="!form.errors.codigo_escaneado && !!form.codigo_escaneado"
                        @keyup.enter="onScanEnter"
                        @focus="selectScanInput"
                    />

                    <div class="flex justify-between items-center text-xs text-gray-500 dark:text-gray-400">
                        <span>
                            Último escaneo:
                            <span class="font-mono">
                                {{ ultimoCodigo || '—' }}
                            </span>
                        </span>
                        <button
                            type="button"
                            class="underline hover:text-indigo-600"
                            @click="FocusScanInput"
                        >
                            Enfocar campo de escaneo
                        </button>
                    </div>
                </section>

                <!-- ======= DETALLE DEL ROLLO ESCANEADO ======= -->
                <div v-if="rolloActual" class="bg-white dark:bg-slate-900 rounded-xl shadow p-4 space-y-4">
                    <h2 class="text-lg font-bold mb-1">
                        Rollo encontrado
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm">

                        <div>
                            <span class="font-semibold">Producto:</span>
                            <span>{{ rolloActual.producto?.nombre }}</span>
                        </div>

                        <div>
                            <span class="font-semibold">Código producto:</span>
                            <span class="font-mono">{{ rolloActual.producto?.codigo }}</span>
                        </div>

                        <div>
                            <span class="font-semibold">Color:</span>
                            <span>{{ rolloActual.color?.nombre }}</span>
                        </div>

                        <div>
                            <span class="font-semibold">Calidad:</span>
                            <span>{{ rolloActual.tipo_calidad?.nombre }}</span>
                        </div>

                        <div>
                            <span class="font-semibold">Tarjeta:</span>
                            <span class="font-mono">{{ rolloActual.num_tarjeta }}</span>
                        </div>

                        <div>
                            <span class="font-semibold">Rollo:</span>
                            <span class="font-mono">{{ rolloActual.num_rollo }}</span>
                        </div>

                        <div>
                            <span class="font-semibold">Peso del rollo:</span>
                            <span class="text-indigo-600 font-bold">
                                {{ rolloActual.cantidad }} kg
                            </span>
                        </div>

                        <div>
                            <span class="font-semibold">Disponible total en inventario:</span>
                            <span
                                :class="rolloActual.cantidad_disponible > 0
                                    ? 'text-emerald-600 font-bold'
                                    : 'text-red-600 font-bold'"
                            >
                                {{ rolloActual.cantidad_disponible }} kg
                            </span>
                        </div>

                        <div class="md:col-span-2">
                            <span class="font-semibold">Fecha de entrada:</span>
                            <span>{{ rolloActual.fecha_movimiento }}</span>
                        </div>

                    </div>

                    <!-- ======= BOTÓN DE SALIDA ======= -->
                    <div class="pt-3">
                        <MdButton
                            variant="primary"
                            size="large"
                            class="w-full py-4 text-lg font-semibold"
                            :loading="IsLoading"
                            @click="ConfirmarSalida"
                        >
                            DAR SALIDA DEL ROLLO
                        </MdButton>
                    </div>
                </div>

            </div>
        </section>
    </AppLayout>
</template>

<script setup>
import { ref, inject, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import MdButton from '@/Components/MaterialDesign/MdButton.vue'
import axios from 'axios'

const soundSuccess = new Audio('/Sounds/success.mp3')
const soundWarning = new Audio('/Sounds/message.mp3')
const soundError   = new Audio('/Sounds/crumple.mp3')
const soundBeep  = new Audio('/Sounds/store.mp3')

soundSuccess.volume = 1.0;
soundWarning.volume = 1.0;
soundError.volume = 1.0;
soundBeep.volume = 1.0;

const toast = inject('$toast')
const confirm = inject('$confirm');
const IsLoading = ref(false)
const rolloActual = ref(null)
const ultimoCodigo = ref('')

const ScanSection = ref(null)

const form = useForm({
    codigo_escaneado: '',
    cantidad_salida_parcial: '',
    observaciones: ''
})

const FormatearCodigoEtiqueta = (codigo) => {
    const pattern = /^PROD-(\d+)-COL-(\d+)-CAL-(\d+)-MOV-(\d+)$/i
    const match = codigo.trim().toUpperCase().match(pattern)

    if (!match) return null

    return {
        producto_id: Number(match[1]),
        color_id: Number(match[2]),
        tipo_calidad_id: Number(match[3]),
        movimiento_id: Number(match[4]),
    }
}

onMounted(() => {
    FocusScanInput()
})

const FocusScanInput = () => {
    const el = document.getElementById('codigo_escaneado')
    if (el) el.focus()
}

const selectScanInput = (event) => {
    if (event?.target) event.target.select()
}

const onScanEnter = async () => {
    ultimoCodigo.value = form.codigo_escaneado;

    const parsed = FormatearCodigoEtiqueta(form.codigo_escaneado);

    IsLoading.value = true;

    try {
        const response  = await axios.get(route('Salidas.BuscarMovimiento', { movimiento: parsed.movimiento_id }));
        console.log(response.data);

        rolloActual.value = response.data;
        soundBeep.play();

    } catch (error) {

        soundError.play();

        if (error.response?.status === 404) {
            toast && toast('No se encontró el rollo.', 'danger');
        } else {
            toast && toast('Error al buscar el rollo.', 'danger');
        }

        rolloActual.value = null;

    } finally {
        IsLoading.value = false;
    }
};

const ConfirmarSalida = () => {
    confirm(
        'Confirmar salida',
        `¿Deseas dar salida al rollo ${rolloActual.value.num_rollo} (${rolloActual.value.cantidad} kg)?`,
        'Sí, dar salida',
        'Cancelar',
        () => {
            RegistrarSalida();
        },
        () => {
            console.log('Acción cancelada');
        }
    );
};

const RegistrarSalida = () => {
    IsLoading.value = true;

    form.post(route('Salidas.RegistrarSalida', { movimiento: rolloActual.value.id }),{
            onSuccess: () => {
                toast && toast('Salida registrada correctamente.', 'success');
                rolloActual.value = null;
                form.codigo_escaneado = '';
                FocusScanInput();
            },
            onError: () => {
                toast && toast('Error al registrar salida.', 'danger')
            },
            onFinish: () => {
                soundBeep.play();
                IsLoading.value = false;
            }
        }
    );
};

</script>
