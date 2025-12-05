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

            <section class="m-2 h-[60vh] overflow-auto pdf-imprimible">
                <AgGrid
                    ref="gridRef"
                    :initial-row-data="HistoricoSalidas"
                    :initial-column-defs="columnas"
                    @grid-ready="onGridReady"
                    @cell-clicked="onCellClicked"
                    height="50vh"
                />
                <div class="mt-2 mx-4 print-page-break">
                    <h2 class="text-lg text-center font-bold text-sky-600 mb-2">Resumen por producto</h2>
                    <table class="min-w-full text-sm text-left text-gray-300 border border-gray-600 print-table bg-gray-800">
                        <thead class="bg-gray-700 text-white uppercase text-xs">
                            <tr>
                                <th class="px-4 py-2">Producto</th>
                                <th class="px-4 py-2"># Rollos</th>
                                <th class="px-4 py-2">Total (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in ResumenSalidas" :key="item.producto" class="border-t border-gray-700">
                                <td class="px-4 py-2">{{ item.producto }}</td>
                                <td class="px-4 py-2">{{ item.rollos }}</td>
                                <td class="px-4 py-2">{{ item.total_kg }} KG</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="flex justify-center">
                <MdButton class="mt-4" @click="ExportarCSV()">Exportar CSV</MdButton>
            </div>

            <MdDialogModal v-if="ShowModalEdit" :show="ShowModalEdit" @close="ToggleModalEdit" maxWidth="3xl">
                <template #title>
                    Crear Productos
                </template>

                <template #content>
                    <section ref="FormSection" class="grid grid-cols-2 gap-4">
                            <MdSelectSearchInput
                                id="cliente_id"
                                name="cliente_id"
                                class="col-span-2"
                                v-model="formH.cliente_id"
                                required
                                label="Cliente"
                                helper="Seleccione un cliente"
                                :error="formH.errors.cliente_id"
                                :success="!formH.errors.cliente_id"
                                :options="Clientes"
                            />

                            <MdNumberInput
                                id="num_tarjeta"
                                name="num_tarjeta"
                                v-model="formH.num_tarjeta"
                                required
                                label="Tarjeta viajera"
                                :minlength="1"
                                :maxlength="6"
                                helper="Ingrese el numero de tarjeta"
                                :error="formH.errors.num_tarjeta"
                                :success="!formH.errors.num_tarjeta"
                            />



                            <MdSelectSearchInput
                                id="producto_id"
                                name="producto_id"
                                v-model="formH.producto_id"
                                required
                                label="Producto"
                                helper="Seleccione un producto"
                                :error="formH.errors.producto_id"
                                :success="!formH.errors.producto_id"
                                :options="Productos"
                            />

                            <MdSelectSearchInput
                                id="color_id"
                                name="color_id"
                                v-model="formH.color_id"
                                required
                                label="Color"
                                helper="Seleccione un color (opcional)"
                                :error="formH.errors.color_id"
                                :success="!formH.errors.color_id"
                                :options="Colores"
                            />

                            <MdNumberInput
                                id="peso_tara"
                                name="peso_tara"
                                v-model="formH.peso_tara"
                                label="Ingresar Tara"
                                helper="peso_tara"
                                inputRestrict="decimal"
                                :error="formH.errors.peso_tara"
                                :success="!formH.errors.peso_tara"
                            />

                            <MdSelectInput
                                id="tipo_calidad_id"
                                name="tipo_calidad_id"
                                v-model="formH.tipo_calidad_id"
                                required
                                label="Calidad"
                                helper="Seleccione una calidad (opcional)"
                                :error="formH.errors.tipo_calidad_id"
                                :success="!formH.errors.tipo_calidad_id"
                                :options="TiposCalidades"
                            />

                            <MdNumberInput
                                id="num_rollo"
                                name="num_rollo"
                                v-model="formH.num_rollo"
                                label="Num rollo"
                                :error="formH.errors.num_rollo"
                                :success="!formH.errors.num_rollo"
                            />

                            <MdNumberInput
                                id="cantidad"
                                name="cantidad"
                                class="col-span-2"
                                v-model="formH.cantidad"
                                required
                                label="Cantidad"
                                inputRestrict="decimal"
                                :helper="cantidadHelperText"
                                :error="formH.errors.cantidad"
                                :success="!formH.errors.cantidad"
                            />
                    </section>
                </template>

                <template #footer>
                    <div class="space-x-2">
                        <MdButton variant="primary" :loading="IsLoading" @click="Upsert()">
                            {{ IsEditMode ? 'Actualizar' : 'Registrar' }}
                        </MdButton>
                        <MdButton variant="dark" @click="ToggleModalEdit()">Cancelar</MdButton>
                    </div>
                </template>
            </MdDialogModal>

            <MdDialogModal v-if="ShowModal" :show="ShowModal" @close="ToggleModal">
                <template #title>
                    Configurar rollos
                </template>

                <template #content>
                    <div id="Etiqueta"></div>
                </template>

                <template #footer>
                    <div class="space-x-2">
                        <MdButton variant="dark" @click="ToggleModal()">Cerrar</MdButton>
                    </div>
                </template>
            </MdDialogModal>

        </AppLayout>
    </template>

<script setup>
import { ref, inject, defineProps, nextTick  } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AgGrid from '@/Components/Dependencies/AgGrid.vue'
import MdButton from '@/Components/MaterialDesign/MdButton.vue'
import MdSelectSearchInput from '@/Components/MaterialDesign/MdSelectSearchInput.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import MdDialogModal from '@/Components/MaterialDesign/MdDialogModal.vue'
import MdSelectInput from '@/Components/MaterialDesign/MdSelectInput.vue'
import MdNumberInput from '@/Components/MaterialDesign/MdNumberInput.vue'
import axios from 'axios'
import JsBarcode from 'jsbarcode'
import QRCode from 'qrcode'

    /* ========================== Props ========================== */
    const props = defineProps({
        Clientes: Object,
        Salidas: Object,
        Productos: Object,
        Colores: Object,
        TiposCalidades: Object,
        Almacenes: Object,
    })

    /* ========================== Refs ========================== */
    const toast = inject('$toast');
    const IsLoading = ref(false);
    const IsEditMode = ref(false);
    const ShowModal = ref(false);
    const ShowModalEdit = ref(false);
    const FormValidate = inject('FormValidate');
    const FormSection = ref(null);
    const confirm = inject('$confirm');
    const HistoricoSalidas = ref(props.Salidas)
    const ResumenSalidas = ref([])
    const Entrada = ref({})

    const gridRef = ref(null);
    const gridApi = ref(null);

    const formH = useForm({
        cliente_id: null,
        num_tarjeta: null,
        num_rollo: null,
        peso_tara: null,
        producto_id: null,
        color_id: null,
        tipo_calidad_id: 1,
        cantidad: null,
    })

    const onGridReady = (params) => { gridApi.value = params.api; };

    const form = useForm({
        id: '',
        cliente_id: '',
        num_tarjeta: '',
    });

    const columnas = [
        { headerName: 'Tarjeta', field: 'num_tarjeta' },
        { headerName: 'Cliente', field: 'cliente.nombre' },
        { headerName: 'Rollo', field: 'num_rollo' },
        { headerName: 'Producto', field: 'producto.nombre' },
        {
            headerName: 'Cantidad',
            field: 'cantidad',
            valueFormatter: params => {
                if (params.value == null) return '';
                let num = parseFloat(params.value);
                const str = num % 1 === 0 ? num.toString() : parseFloat(num.toFixed(3)).toString();
                return `${str} kg`;
            }
        },

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

    const ToggleModalEdit = () => {
        ShowModalEdit.value = !ShowModalEdit.value
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
                case 'Reimprimir':
                    Reimprimir(event.data.id);
                    break;
                default:
                    console.info('Acción desconocida:', action);
            }
        }
    };

    const Buscar = async () => {
        IsLoading.value = true;

        try {
            const response = await axios.post(route('HistoricoSalidas.FiltrarSalidas'), {
                cliente_id: form.cliente_id,
                num_tarjeta: form.num_tarjeta,
            });
            console.log(response.data);

            HistoricoSalidas.value = response.data.salidas || [];
            ResumenSalidas.value = response.data.resumen || [];
        } catch (error) {
            toast('Ocurrió un error al buscar los registros', 'error');
            console.error(error);
        } finally {
            IsLoading.value = false;
        }
    }

    const ExportarCSV = async () => {
        try {
            const response = await axios.post(route('HistoricoSalidas.ExpotarPedido'),{
                    entradas: HistoricoSalidas.value,
                    resumen: ResumenSalidas.value
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
            link.setAttribute('download', `HistoricoSalidas-${fecha}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (error) {
            toast('Error al exportar CSV', 'error');
            console.error(error);
        }
    }

    const upsertLocal = (row) => {
        const i = HistoricoSalidas.value.findIndex(r => r.id === row.id);
        if (i === -1) HistoricoSalidas.value.unshift(row);
        else HistoricoSalidas.value[i] = row;
        gridApi.value?.applyTransaction({ update: [row] });
    };

    const removeLocal = (id) => {
        const row = HistoricoSalidas.value.find(r => r.id === id);
        if (!row) return;
        HistoricoSalidas.value = HistoricoSalidas.value.filter(r => r.id !== id);
        gridApi.value?.applyTransaction({ remove: [row] });
    };

    const Edit = (data) => {
        console.log(data);
        IsLoading.value = false;
        Object.assign(formH, data);
        formH.num_rollo = parseInt(data.num_rollo);
        IsEditMode.value = true;
        ShowModalEdit.value = true;
    };

    const Upsert = () => {
        if (!FormValidate(FormSection)) return
        IsLoading.value = true;
        if (IsEditMode.value) {
            formH.put(route('HistoricoSalidas.update', formH.id), {
            onSuccess: ({ props }) => {
                const updated = props?.registro ?? { ...formH };
                upsertLocal(updated);
                ToggleModalEdit();
                formH.reset(); IsLoading.value = false;
                toast('Registro actualizado', 'success');
            },
            onError(){
                    IsLoading.value = false; toast('Ocurrió un error','danger');
                }
            });
        } else {
            formH.post(route('HistoricoSalidas.store'), {
                onSuccess: (response) => {
                    console.log(response);
                    ToggleModalEdit();
                    formH.reset();
                    IsLoading.value = false;
                    toast('Registro guardado correctamente', 'success');
                    location.reload();
                },
                onError: (e) => {
                    IsLoading.value = false;
                    console.error(e);
                    toast('Ocurrió un error', 'danger');
                }
            });
        }
    };

    const Delete = (id) => {
        console.log(id);

        confirm(
            '¿Estás seguro?',
            'Esta acción no se puede deshacer.',
            'Sí, eliminar',
            'Cancelar',
            () => {
            formH.delete(route('HistoricoSalidas.destroy', id), {
                onSuccess: () => {
                    removeLocal(id);
                    toast('Registro eliminado', 'success');
                },
                onError(e){
                    toast('Ocurrió un error al eliminar','danger');
                    console.error(e);
                }
            });
            },
            () => {
            console.log('Acción cancelada');
            }
        );
    };

    const Reimprimir = async (id) => {
        try {
            const { data } = await axios.post(route('HistoricoSalidas.ObtenerEntrada'), { id })
            ShowModal.value = !ShowModal.value
            Entrada.value = data;
            ImprimirEtiqueta(data);
        } catch (error) {
            toast('Error al obtener datos de la entrada', 'danger')
            console.error(error)
        }
    }

    const ImprimirEtiqueta = async (data) => {
        const producto = data.producto_label;
        const color = data.color_label;
        const tipo_calidad = data.tipo_calidad_id;
        const cantidad = Number(data.cantidad || 0).toFixed(2);

        const codigo = `PROD-${data.producto_id}-COL-${data.color_id}-CAL-${data.tipo_calidad_id}-MOV-${data.id}`;
        const qrDataUrl = await QRCode.toDataURL(codigo, { width: 20 * 3.78, margin: 0 });

        const html = `
            <div style="font-family:sans-serif; width:160mm; height:83mm; border:0.1mm solid #000; padding:5mm; box-sizing:border-box; text-align:left;">
            <!-- Código de barras -->
            <div style="display:flex; justify-content:flex-start; margin-bottom:4mm;">
                <svg id="barcode" style="width:75mm; height:80mm;"></svg>
            </div>

            <!-- Info + QR -->
                <div style="display:flex; align-items:flex-start; justify-content:flex-start; gap:4mm;">
                    <div style="flex:1; font-size:6mm; font-weight:bold; line-height:1.4; text-align:left;">
                        <div style="text-align:left; font-size:4mm;">${codigo}</div>
                        <div style="text-align:left; font-size:7mm;">TV: ${data.num_tarjeta} # ROLLO: ${data.num_rollo}</div>
                        <div style="text-align:left; font-size:6mm;">${producto} ${color} (${tipo_calidad})</div>
                        <div style="text-align:left; font-size:8mm;">PESO NETO: ${cantidad}</div>
                    </div>

                <img src="${qrDataUrl}" style="width:30mm; height:30mm; padding-right: 10mm;" />
                </div>

            </div>
        `;

        const etiquetaDiv = document.getElementById('Etiqueta');
        etiquetaDiv.innerHTML = html;

        await nextTick();

        JsBarcode("#barcode", codigo, {
            format: "CODE128",
            width: 1.6,
            height: 10 * 3.78,
            displayValue: false
        });

        ImprimirElemento(etiquetaDiv);

        await nextTick();
        // const input = document.getElementById('cantidad');
        // if (input) input.focus();
    };

    const ImprimirElemento = (elementoOriginal) => {
        const win = window.open('', '', 'width=900,height=700');
        if (!win) { toast('Error al abrir la ventana de impresión.', 'danger'); return; }

        const estilos = `
            <style>
            @page { size: 102mm 51mm; margin: 0; }

            html, body { margin:0; padding:0; }
            img, svg { display:block; max-width:100%; }

            /* Contenedor de UNA etiqueta exacta */
            .sheet {
                box-sizing: border-box;
                width: 102mm;
                height: 51mm;
                padding: 0mm !important;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                gap: 1mm;
                font-family: system-ui, sans-serif;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                page-break-after: always;     /* garantiza 1 etiqueta = 1 hoja */
            }

            /* Vista previa en pantalla */
            @media screen {
                .sheet { margin: 8px auto; outline: 1px dashed #ccc; }
            }

            /* Fuerza el canvas de impresión al tamaño real */
            @media print {
                html, body { width: 102mm; height: 51mm; }
            }
            </style>
        `;

        const htmlEtiqueta = `
            <!DOCTYPE html>
            <html>
            <head><meta charset="utf-8" /><title>Etiqueta</title>${estilos}</head>
            <body>
                <div class="sheet">
                ${elementoOriginal.innerHTML}
                </div>
            </body>
            </html>
        `;

        win.document.open();
        win.document.write(htmlEtiqueta);
        win.document.close();

        const imprimir = () => {
            win.focus();
            win.print();
            setTimeout(() => { try { win.close(); } catch {} }, 200);
            toast('Etiqueta enviada a impresión', 'info');
        };

        const imgs = win.document.images;
        if (!imgs.length) return imprimir();

        let cargadas = 0;
        for (const img of imgs) {
            if (img.complete) cargadas++;
            else img.addEventListener('load', () => { if (++cargadas === imgs.length) imprimir(); });
        }
        if (cargadas === imgs.length) imprimir();
    };

    </script>

