    <template>
        <AppLayout title="Historico Entradas">
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

                <MdTextInput
                    id="num_tarjeta"
                    name="num_tarjeta"
                    v-model="form.num_tarjeta"
                    label="Tarjeta viajera"
                    helper="Ingrese el numero de tarjeta"
                />

                <div>
                    <MdButton :loading="IsLoading" class="mt-4" @click="Buscar()">Buscar</MdButton>
                </div>
            </section>

            <section class="mt-4 mx-4  h-[60vh] overflow-auto pdf-imprimible">
                <table class="min-w-full text-sm text-left text-gray-300 border border-gray-600 print-table bg-gray-800">
                    <thead class="bg-gray-700 text-white uppercase text-xs">
                        <tr>
                            <th class="px-4 py-2">Tarjeta</th>
                            <th class="px-4 py-2">Cliente</th>
                            <th class="px-4 py-2">Rollo</th>
                            <th class="px-4 py-2">Cantidad</th>
                            <th class="px-4 py-2">Calidad</th>
                            <th class="px-4 py-2">Fecha de entrada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="entrada in HistoricoEntradas" :key="entrada.id" class="border-t border-gray-700 hover:bg-gray-800">
                            <td class="px-4 py-2">{{ entrada.num_tarjeta }}</td>
                            <td class="px-4 py-2">{{ entrada.cliente?.nombre }}</td>
                            <td class="px-4 py-2">{{ entrada.num_rollo }}</td>
                            <td class="px-4 py-2">{{ entrada.cantidad }} kg</td>
                            <td class="px-4 py-2">
                                <span
                                    v-if="entrada.tipo_calidad_id == 1"
                                    class="inline-block px-3 py-1 text-xs font-semibold text-white bg-teal-500 rounded-full"
                                >Buena</span>
                                <span
                                    v-else
                                    class="inline-block px-3 py-1 text-xs font-semibold text-white bg-red-500 rounded-full"
                                >Regular</span>
                            </td>
                            <td class="px-4 py-2">{{ entrada.fecha_movimiento }}</td>
                        </tr>
                        <tr v-if="!HistoricoEntradas.length">
                            <td colspan="6" class="text-center px-4 py-4 text-gray-400">Sin resultados</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4 mx-4 print-page-break">
                    <h2 class="text-lg font-bold text-white mb-2">Resumen por producto</h2>
                    <table class="min-w-full text-sm text-left text-gray-300 border border-gray-600 print-table bg-gray-800">
                        <thead class="bg-gray-700 text-white uppercase text-xs">
                            <tr>
                                <th class="px-4 py-2">Producto</th>
                                <th class="px-4 py-2"># Rollos</th>
                                <th class="px-4 py-2">Total (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in ResumenEntradas" :key="item.producto" class="border-t border-gray-700">
                                <td class="px-4 py-2">{{ item.producto }}</td>
                                <td class="px-4 py-2">{{ item.rollos }}</td>
                                <td class="px-4 py-2">{{ item.total_kg }} KG</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <div>
                <MdButton class="mt-4" @click="ExportarCSV()">Exportar CSV</MdButton>
            </div>


        </AppLayout>
    </template>

<script setup>
import { ref, inject, defineProps, nextTick  } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MdButton from '@/Components/MaterialDesign/MdButton.vue'
import MdSelectSearchInput from '@/Components/MaterialDesign/MdSelectSearchInput.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import html2pdf from 'html2pdf.js'
import axios from 'axios'

    /* ========================== Props ========================== */
    const props = defineProps({
        Clientes: Object,
        Entradas: Object,
    })

    /* ========================== Refs ========================== */
    const toast = inject('$toast');
    const IsLoading = ref(false);
    const IsEditMode = ref(false);
    const ShowModal = ref(false);
    const HistoricoEntradas = ref(props.Entradas)
    const ResumenEntradas = ref([])

    const form = useForm({
        id: '',
        cliente_id: '',
        num_tarjeta: '',
    });

    const columnas = [
        { headerName: 'Tarjeta', field: 'num_tarjeta' },
        { headerName: 'Cliente', field: 'cliente.nombre' },
        { headerName: 'Rollo', field: 'num_rollo' },
        { headerName: 'Cantidad', field: 'cantidad', valueFormatter: params => `${params.value} kg` },
        {
            headerName: 'Calidad',
            field: 'tipo_calidad_id',
            cellRenderer: ({ value }) => {
                if (value == 1) {
                    return `<span class="inline-block px-6 text-sm font-semibold text-white bg-teal-500 rounded-full">Buena</span>`
                }else{
                    return `<span class="inline-block px-6 text-sm font-semibold text-white bg-red-500 rounded-full">Regular</span>`
                }

            }
        },
        { headerName: 'Fecha de Entrada', field: 'fecha_movimiento' },
    ]

    /* ========================== Funciones ========================== */
    const ToggleModal = () => {
        IsEditMode.value ? IsEditMode.value = false : form.reset();
        ShowModal.value = !ShowModal.value
    }

    const onCellClicked = (event) => {
        const target = event.event.target.closest('button');
        const action = target?.dataset.action;

        if (action) {
            switch (action) {
                case 'Edit':
                    IsEditMode.value = true;
                    Edit(event.data);
                    break;
                case 'Delete':
                    Delete(event.data.id);
                    break;
                default:
                    console.info('Acción desconocida:', action);
            }
        }
    };

    const Buscar = async () => {
        IsLoading.value = true;

        try {
            const response = await axios.post(route('HistoricoEntradas.FiltrarEntradas'), {
                cliente_id: form.cliente_id,
                num_tarjeta: form.num_tarjeta,
            });
            console.log(response.data);

            HistoricoEntradas.value = response.data.entradas || [];
            ResumenEntradas.value = response.data.resumen || [];
        } catch (error) {
            toast('Ocurrió un error al buscar los registros', 'error');
            console.error(error);
        } finally {
            IsLoading.value = false;
        }
    }

    const ExportarCSV = async () => {
        try {
            const response = await axios.post(route('HistoricoEntradas.ExpotarPedido'),{
                    entradas: HistoricoEntradas.value,
                    resumen: ResumenEntradas.value
                },
                {
                    responseType: 'blob',
                }
            );

            const blob = new Blob([response.data], { type: 'text/csv;charset=utf-8;' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            const fecha = new Date().toISOString().slice(0, 10);
            link.setAttribute('download', `HistoricoEntradas-${fecha}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (error) {
            toast('Error al exportar CSV', 'error');
            console.error(error);
        }
    }



    </script>

