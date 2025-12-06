    <template>
        <AppLayout title="Historico Salidas">
            <template #header-left>

            </template>

            <section class="flex mx-4">
                <MdSelectSearchInput
                    id="cliente_id"
                    name="cliente_id"
                    class="col-span-2"
                    v-model="form.cliente_id"
                    label="Cliente"
                    helper="Seleccione un cliente"
                    :options="Clientes"
                />

                <div>
                    <MdButton :loading="IsLoading" class="mt-4" @click="ConsultarSalidas()">Buscar</MdButton>
                </div>
            </section>

            <section class="mt-6 mx-4">
                <div
                    v-if="SalidasCliente.length"
                    class="space-y-4"
                >
                    <div v-for="salida in SalidasCliente" :key="salida.id" class="bg-white dark:bg-slate-900 rounded-xl shadow p-4">
                        <!-- Encabezado de la salida -->
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h2 class="text-xl font-bold uppercase">
                                    Estatus ({{ salida.estatus }})
                                </h2>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Cliente: <span class="font-semibold">{{ salida.cliente }}</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" v-if="salida.fecha">
                                    Fecha: {{ salida.fecha || '—' }}
                                </p>
                            </div>

                            <div class="text-right text-xs">
                                <p>
                                    Rollos:
                                    <span class="font-semibold">{{ salida.total_rollos }}</span>
                                </p>
                                <p>
                                    Total kg:
                                    <span class="font-semibold">{{ salida.total_kgs }}</span>
                                </p>
                                <p v-if="salida.usuario">
                                    Usuario:
                                    <span class="font-semibold">{{ salida.usuario }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Tabla de detalle de rollos -->
                        <div class="border rounded-lg overflow-hidden dark:border-slate-700">
                            <table class="w-full text-xs md:text-sm">
                                <thead class="bg-gray-100 dark:bg-slate-800">
                                    <tr>
                                        <th class="px-2 py-1 text-left">Producto</th>
                                        <th class="px-2 py-1 text-left">Color</th>
                                        <th class="px-2 py-1 text-left">Tarjeta</th>
                                        <th class="px-2 py-1 text-left">Rollo</th>
                                        <th class="px-2 py-1 text-right">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="item in salida.detalle"
                                        :key="item.id"
                                        class="border-t dark:border-slate-700"
                                    >
                                        <td class="px-2 py-1">
                                            {{ item.producto }}
                                        </td>
                                        <td class="px-2 py-1">
                                            {{ item.color }}
                                        </td>
                                        <td class="px-2 py-1 font-mono">
                                            {{ item.num_tarjeta }}
                                        </td>
                                        <td class="px-2 py-1 font-mono">
                                            {{ item.num_rollo }}
                                        </td>
                                        <td class="px-2 py-1 text-right font-semibold">
                                            {{ item.cantidad }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3 flex justify-end gap-2" v-if="SalidasCliente.length">
                            <MdButton
                                variant="secondary"
                                size="sm"
                                @click="ExportarSalida(salida)"
                            >
                                Exportar hoja
                            </MdButton>

                            <MdButton
                                variant="success"
                                size="sm"
                                @click="FinalizarPedido(salida)"
                            >
                                Marcar pedido como finalizado
                            </MdButton>
                        </div>
                    </div>
                </div>

                <div
                    v-else
                    class="text-sm text-gray-500 dark:text-gray-400"
                >
                    No hay salidas para el cliente seleccionado.
                </div>

            </section>
        </AppLayout>
    </template>

<script setup>
import { ref, inject, defineProps, nextTick  } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AgGrid from '@/Components/Dependencies/AgGrid.vue'
import MdSelectSearchInput from '@/Components/MaterialDesign/MdSelectSearchInput.vue'
import MdButton from '@/Components/MaterialDesign/MdButton.vue'

const form = useForm({
    cliente_id: null,
})

/* ========================== Props ========================== */
const props = defineProps({
    Clientes: Object,
})

/* ========================== Refs ========================== */
const toast = inject('$toast');
const confirm = inject('$confirm');
const IsLoading = ref(false);
const SalidasCliente = ref([]);
const ExportandoId  = ref(null)
const FinalizandoId = ref(null)

/* ========================== Functions ========================== */
async function ConsultarSalidas() {
    try {
        IsLoading.value = true

        const { data } = await axios.post(route('HistoricoSalidas.FiltrarSalidas'),
            {
                cliente_id: form.cliente_id,
            }
        )
        console.log(data);

        SalidasCliente.value = data.salidas ?? []
    } catch (err) {
        console.error('Error consultando:', err)
    } finally {
        IsLoading.value = false
    }
}

const ExportarSalida = (salida) => {
    if (!salida?.id) return

    ExportandoId.value = salida.id

    const url = route('HistoricoSalidas.GenerarPedido', { salida: salida.id })
    window.open(url, '_blank')

    setTimeout(() => {
        ExportandoId.value = null
    }, 800)
}

const FinalizarPedido = (salida) => {
    console.log(salida);

    confirm(
        'Finalizar pedido',
        `¿Deseas marcar como finalizado el pedido #${salida.pedido_id}?`,
        'Sí, finalizar',
        'Cancelar',
        async () => {
            try {
                FinalizandoId.value = salida.id

                const { data } = await axios.post(
                    route('Pedidos.Finalizar', { pedido: salida.pedido_id })
                )

                toast && toast(data?.message || 'Pedido marcado como finalizado.', 'success')

                salida.estatus = 'finalizado'

            } catch (error) {
                console.error(error)
                toast && toast('Error al finalizar el pedido.', 'danger')
            } finally {
                FinalizandoId.value = null
            }
        },
        () => {
            // cancelado
        }
    )
}
</script>

