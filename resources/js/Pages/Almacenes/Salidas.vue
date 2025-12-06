<template>
    <AppLayout title="Salidas">
        <section class="max-w-4xl mx-auto space-y-6">
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow p-4">
                <MdSelectSearchInput
                    id="cliente_id"
                    name="cliente_id"
                    class="col-span-2"
                    v-model="form.cliente_id"
                    required
                    label="Cliente"
                    helper="Seleccione un cliente"
                    :error="form.errors.cliente_id"
                    :success="!form.errors.cliente_id"
                    :options="Clientes"
                />

                <div class="text-sm text-sky-500 dark:text-sky-400 mb-4" @click="FocusScanInput">
                    <div v-if="form.pedido_id"
                    class="mt-2 inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full
                        bg-teal/10 text-teal border border-teal/30 dark:bg-teal/20 dark:text-teal-400 dark:border-teal-400"
                    >
                        Pedido seleccionado: #{{ form.pedido_id }}
                    </div> Escanea el código QR con el TC27.
                </div>

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

                </div>

                <!-- LISTA DE ROLLOS SELECCIONADOS -->
                <div v-if="rollosSeleccionados.length" class="mt-4 bg-white dark:bg-slate-900 rounded-xl shadow p-4 space-y-3">
                    <h2 class="text-lg font-bold">
                        Rollos seleccionados para salida ({{ rollosSeleccionados.length }})
                    </h2>

                    <div class="max-h-64 overflow-y-auto text-sm">
                        <table class="w-full text-left text-xs md:text-sm">
                            <thead>
                                <tr class="border-b dark:border-slate-700">
                                    <th class="py-1">Prod.</th>
                                    <th class="py-1">Tarjeta</th>
                                    <th class="py-1">Rollo</th>
                                    <th class="py-1 text-right">Cantidad</th>
                                    <th class="py-1 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="r in rollosSeleccionados"
                                    :key="r.id"
                                    class="border-b last:border-0 dark:border-slate-800"
                                >
                                    <td class="py-1">
                                        {{ r.producto?.nombre }}
                                    </td>
                                    <td class="py-1 font-mono">
                                        {{ r.num_tarjeta }}
                                    </td>
                                    <td class="py-1 font-mono">
                                        {{ r.num_rollo }}
                                    </td>
                                    <td class="py-1 text-right font-semibold">
                                        {{ r.cantidad }} kg
                                    </td>
                                    <td class="py-1 text-right">
                                        <MdButton
                                            variant="danger"
                                            size="xs"
                                            @click="QuitarRollo(r.id)"
                                        >
                                            Quitar
                                        </MdButton>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="pt-3">
                        <MdButton
                            variant="primary"
                            size="large"
                            class="w-full py-3 text-base font-semibold"
                            :loading="IsLoading"
                            :disabled="!rollosSeleccionados.length"
                            @click="ConfirmarSalidaLote"
                        >
                            Confirmar salida de {{ rollosSeleccionados.length }} rollos
                        </MdButton>
                    </div>
                </div>


            </div>
        </section>

        <MdDialogModal v-if="ShowModal" :show="ShowModal" @close="ToggleModal">
            <template #title>
                Pedidos del cliente
            </template>

            <template #content>
                <div class="space-y-4 max-h-[60vh] overflow-y-auto">
                    <div
                        v-for="pedido in detalle_pedido"
                        :key="pedido.id"
                        class="border dark:border-gray-700 rounded-md p-4 bg-gray-50 dark:bg-gray-800"
                    >
                        <div class="font-bold text-lg">
                            Pedido #{{ pedido.id }}
                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            <p><strong>Fecha:</strong> {{ pedido.fecha_pedido }}</p>
                            <p><strong>Estado:</strong> {{ pedido.estado_pedido }}</p>
                            <p><strong>Plazo pago:</strong> {{ pedido.plazo_pago }}</p>
                            <p><strong>Condiciones:</strong> {{ pedido.condiciones }}</p>
                            <p><strong>Observaciones:</strong> {{ pedido.observaciones }}</p>
                        </div>

                        <div class="mt-3">
                            <p class="font-semibold">Productos solicitados:</p>
                            <ul class="list-disc ml-6 text-sm">
                                <li v-for="item in pedido.detalle" :key="item.producto_id">
                                    {{ item.producto }} — {{ item.cantidad }}
                                </li>
                            </ul>
                        </div>

                        <div class="mt-4">
                            <MdButton
                                variant="primary"
                                size="sm"
                                @click="SeleccionarPedido(pedido)"
                            >
                                Seleccionar este pedido
                            </MdButton>
                        </div>
                    </div>
                </div>
            </template>

            <template #footer>
                <MdButton variant="dark" @click="ToggleModal()">Cerrar</MdButton>
            </template>
        </MdDialogModal>

    </AppLayout>
</template>

<script setup>
import { ref, inject, onMounted, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MdDialogModal from '@/Components/MaterialDesign/MdDialogModal.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import MdButton from '@/Components/MaterialDesign/MdButton.vue'
import MdSelectSearchInput from '@/Components/MaterialDesign/MdSelectSearchInput.vue'
import axios from 'axios'

const props = defineProps({
    Clientes: Object
})

const soundSuccess = new Audio('/Sounds/success.mp3')
const soundWarning = new Audio('/Sounds/message.mp3')
const soundError   = new Audio('/Sounds/crumple.mp3')
const soundBeep  = new Audio('/Sounds/store.mp3')

soundSuccess.volume = 1.0;
soundWarning.volume = 1.0;
soundError.volume = 1.0;
soundBeep.volume = 1.0;

/* ========================== Refs ========================== */
const toast = inject('$toast');
const confirm = inject('$confirm');
const IsLoading = ref(false);
const IsEditMode = ref(false);
const ShowModal = ref(false);
const rolloActual = ref(null)
const ultimoCodigo = ref('')
const detalle_pedido = ref([]);
const rollosSeleccionados = ref([])
const ScanSection = ref(null)


const form = useForm({
    codigo_escaneado: '',
    cantidad_salida_parcial: '',
    observaciones: '',
    pedido_id: ''
})

onMounted(() => {
    FocusScanInput()
})

/* ========================== Funciones ========================== */
const ToggleModal = () => {
    IsEditMode.value ? IsEditMode.value = false : form.reset();
    ShowModal.value = !ShowModal.value
}

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
    if (!parsed) {
        toast && toast('Formato de código inválido.', 'danger');
        soundError.play();
        return;
    }

    IsLoading.value = true;

    try {
        const { data } = await axios.get(route('Salidas.BuscarMovimiento', { movimiento: parsed.movimiento_id }));

        // Evitar duplicados
        const existe = rollosSeleccionados.value.some(r => r.id === data.id);
        if (existe) {
            toast && toast('Este rollo ya está en la lista.', 'warning');
            soundWarning.play();
        } else {
            rollosSeleccionados.value.push(data);
            toast && toast('Rollo agregado a la lista.', 'success');
            soundBeep.play();
        }

        rolloActual.value = data; // opcional, para mostrar detalle del último
        form.codigo_escaneado = '';

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
        FocusScanInput();
    }
};

function QuitarRollo(id) {
    rollosSeleccionados.value = rollosSeleccionados.value.filter(r => r.id !== id);
}

const ConfirmarSalidaLote = () => {
    if (!rollosSeleccionados.value.length) return;

    confirm(
        'Confirmar salida',
        `Se dará salida a ${rollosSeleccionados.value.length} rollos. ¿Continuar?`,
        'Sí, confirmar',
        'Cancelar',
        () => RegistrarSalidaLote(),
        () => {}
    );
};

const RegistrarSalidaLote = async () => {
    IsLoading.value = true;

    try {
        const ids = rollosSeleccionados.value.map(r => r.id);

        await axios.post(route('Salidas.RegistrarSalidaLote'), {
            movimiento_ids: ids,
            pedido_id: form.pedido_id || null,
        });

        toast && toast('Salidas registradas correctamente.', 'success');
        rollosSeleccionados.value = [];
        rolloActual.value = null;
        form.codigo_escaneado = '';
        FocusScanInput();
    } catch (error) {
        toast && toast('Error al registrar salidas.', 'danger');
        console.error(error);
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

function AbrirModalPedidos(data) {
    detalle_pedido.value = data.pedidos || []
    ShowModal.value = true
}

function SeleccionarPedido(pedido) {
    form.pedido_id = pedido.id
    ShowModal.value = false
}

async function Consultarpedidos(cliente_id) {
    try {
        IsLoading.value = true
        const { data } = await axios.post(route('Salidas.BuscarPedidos'), {
            cliente_id: form.cliente_id,
        })
        console.log(data);

        detalle_pedido.value = data
        AbrirModalPedidos(data);
    } catch (err) {
        console.error('Error consultando:', err)
    } finally {
        IsLoading.value = false
    }
}

watch(() => form.cliente_id, async (nuevoCliente) => {
    if (!nuevoCliente) {
        form.pedido_id = ''
        detalle_pedido.value = []
        return
    }

    const { data } = await axios.post(route('Salidas.BuscarPedidos'), {
        cliente_id: nuevoCliente,
    })

    detalle_pedido.value = data.pedidos || []

    if (detalle_pedido.value.length === 1) {
        form.pedido_id = detalle_pedido.value[0].id
    } else if (detalle_pedido.value.length > 1) {
        ShowModal.value = true
    } else {
        form.pedido_id = null
    }
})


</script>
