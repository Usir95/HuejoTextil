    <template>
        <AppLayout title="PedidosClientes">
            <template #header-right>
                <MdButton @click="ToggleModal()">
                    <i class="fa fa-user-plus mr-2"></i>Nuevo pedido
                </MdButton>
            </template>

            <AgGrid
                :initial-row-data="PedidosClientes"
                :initial-column-defs="columnas"
                @cell-clicked="onCellClicked"
                height="80vh"
            />

            <MdDialogModal v-if="ShowModal" :show="ShowModal" @close="ToggleModal">
                <template #title>
                    Crear Pedidos
                </template>

                <template #content>
                    <section ref="FormSection" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <MdDateInput
                            id="fecha_pedido"
                            name="fecha_pedido"
                            label="fecha_pedido"
                            v-model="form.fecha_pedido"
                            required
                            helper="Fecha del pedido"
                            :error="form.errors.fecha_pedido"
                            :success="!form.errors.fecha_pedido"
                        />
                        <MdSelectInput
                            id="estado_pedido"
                            name="estado_pedido"
                            v-model="form.estado_pedido"
                            required
                            label="Selecciona un estatus"
                            helper="Estatus del pedido"
                            :error="form.errors.estado_pedido"
                            :success="!form.errors.estado_pedido"
                            :options="Estatus"
                        />
                        <MdSelectInput
                            id="plazo_pago"
                            name="plazo_pago"
                            class="col-span-2"
                            v-model="form.plazo_pago"
                            required
                            label="Plazo de pago"
                            helper="Selecciona una forma de pago"
                            :error="form.errors.plazo_pago"
                            :success="!form.errors.plazo_pago"
                            :options="FormasPagos"
                        />
                        <MdTextInput
                            id="condiciones"
                            name="condiciones"
                            class="col-span-2"
                            v-model="form.condiciones"
                            required
                            label="Condiciones"
                            helper="Condiciones del pedido"
                            :error="form.errors.condiciones"
                            :success="!form.errors.condiciones"
                        />
                        <MdTextareaInput
                            id="observaciones"
                            name="observaciones"
                            class="col-span-2"
                            v-model="form.observaciones"
                            required
                            label="Observaciones"
                            helper="Observaciones del pedido"
                            :error="form.errors.observaciones"
                            :success="!form.errors.observaciones"
                        />

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

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Artículos</label>
                            <div v-for="(articulo, index) in form.articulos" :key="index" class="flex mt-2 gap-5">
                                <MdSelectInput
                                    :id="'articulo_' + index"
                                    :name="'articulo_' + index"
                                    v-model="form.articulos[index].producto_id"
                                    label="Selecciona un artículo"
                                    :options="Productos"
                                    class="flex-[2]"
                                    :error="form.errors[`articulos.${index}.producto_id`]"
                                />
                                <MdNumberInput
                                    :id="'cantidad_' + index"
                                    :name="'cantidad_' + index"
                                    v-model.number="form.articulos[index].cantidad"
                                    label="Cantidad"
                                    class="flex-1"
                                    :error="form.errors[`articulos.${index}.cantidad`]"
                                />
                                <MdButton variant="danger" size="sm" @click="removeArticulo(index)">
                                    Eliminar
                                </MdButton>
                            </div>
                            <MdButton variant="success" size="sm" @click="addArticulo" class="mt-2">
                                Agregar Artículo
                            </MdButton>
                        </div>

                    </section>
                </template>

                <template #footer>
                    <div class="space-x-2">
                        <MdButton variant="primary" :loading="IsLoading" @click="Upsert()">
                            {{ IsEditMode ? 'Actualizar' : 'Registrar' }}
                        </MdButton>
                        <MdButton variant="dark" @click="ToggleModal()">Cancelar</MdButton>
                    </div>
                </template>
            </MdDialogModal>
        </AppLayout>
    </template>

<script setup>
import { ref, inject, defineProps } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AgGrid from '@/Components/Dependencies/AgGrid.vue'
import MdDateInput from '@/Components/MaterialDesign/MdDateInput.vue'
import MdButton from '@/Components/MaterialDesign/MdButton.vue'
import MdDialogModal from '@/Components/MaterialDesign/MdDialogModal.vue'
import MdTextareaInput from '@/Components/MaterialDesign/MdTextareaInput.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import MdSelectInput from '@/Components/MaterialDesign/MdSelectInput.vue'
import MdNumberInput from '@/Components/MaterialDesign/MdNumberInput.vue'
import MdSelectSearchInput from '@/Components/MaterialDesign/MdSelectSearchInput.vue'

    /* ========================== Props ========================== */
    const props = defineProps({
        PedidosClientes: Object,
        Estatus: Array,
        FormasPagos: Array,
        Productos: Array,
        Clientes: Array,
    })

    /* ========================== Refs ========================== */
    const toast = inject('$toast');
    const confirm = inject('$confirm');
    const FormValidate = inject('FormValidate');
    const IsLoading = ref(false);
    const IsEditMode = ref(false);
    const ShowModal = ref(false);
    const FormSection = ref(null);


    const form = useForm({
        id: '',
        fecha_pedido: '',
        estado_pedido: '',
        plazo_pago: '',
        condiciones: '',
        observaciones: '',
        cliente_id: '',
        articulos: [{ producto_id: null, cantidad: 1 }],
    });

    const columnas = [
        { headerName: 'Fecha de Pedido', field: 'fecha_pedido' },
        { headerName: 'Cliente', field: 'cliente.nombre' },
        // { headerName: 'Detalle pedido', field: 'detalle_pedido' },
        { headerName: 'Estatus', field: 'estado_pedido' },
        { headerName: 'Plazo de Pago', field: 'plazo_pago' },
        { headerName: 'Condiciones', field: 'condiciones' },
        { headerName: 'Observaciones', field: 'observaciones' },
        {
            headerName: 'Acciones',
            field: 'acciones',
            pinned: 'right',
            maxWidth: 200,
            cellRenderer: (params) => {
                return `
                <div>
                    <button data-action="Edit" data-id="${params.data.id}" title="Editar usuario" class="text-indigo-500 hover:text-indigo-900">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button data-action="Delete" data-id="${params.data.id}" title="Desactivar usuario" class="text-red-600 hover:text-red-900 ms-4">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                `;
            },
            sortable: false,
            filter: false,
        },
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

    const Upsert = () => {
        if (!FormValidate(FormSection)) return
        IsLoading.value = true;
        if (IsEditMode.value) {
            form.put(route('Pedidos.update', form.id), {
                onSuccess: () => {
                    ToggleModal();
                    form.reset();
                    IsLoading.value = false;
                    toast('Registro actualizado', 'success');
                },
                onError: () => {
                    toast('Ocurrió un error', 'danger');
                    IsLoading.value = false;
                }
            });
        } else {
            form.post(route('Pedidos.store'), {
                onSuccess: () => {
                    ToggleModal();
                    form.reset();
                    IsLoading.value = false;
                    toast('Registro guardado correctamente', 'success');
                },
                onError: (e) => {
                    IsLoading.value = false;
                    console.error(e);
                    toast('Ocurrió un error', 'danger');
                }
            });
        }
    };

    const Edit = (data) => {
        IsLoading.value = false;
        Object.assign(form, data);
        form.articulos = data.detalle_pedido || [{ producto_id: null, cantidad: 1 }];
        IsEditMode.value = true;
        ShowModal.value = true;
    };

    const Delete = (id) => {
        confirm(
            '¿Estás seguro?',
            'Esta acción no se puede deshacer.',
            'Sí, eliminar',
            'Cancelar',
            () => {
            form.delete(route('Pedidos.destroy', id), {
                onSuccess: () => {
                    toast('Registro eliminado', 'success');
                },
                onError: (e) => {
                    toast('Ocurrió un error al eliminar', 'danger');
                console.error(e);
                },
            });
            },
            () => {
            console.log('Acción cancelada');
            }
        );
    };

    const addArticulo = () => {
        form.articulos.push({ producto_id: null, cantidad: 1 });
    };

    const removeArticulo = (index) => {
        form.articulos.splice(index, 1);
    };

    </script>
