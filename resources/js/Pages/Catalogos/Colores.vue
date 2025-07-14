    <template>
        <AppLayout title="Catálogo de Colores">
            <template #header-right>
                <MdButton @click="ToggleModal()">Nuevo Color</MdButton>
            </template>

            <AgGrid
                :initial-row-data="Colores"
                :initial-column-defs="columnas"
                @cell-clicked="onCellClicked"
                height="80vh"
            />

            <MdDialogModal v-if="ShowModal" :show="ShowModal" @close="ToggleModal">
                <template #title>
                    Crear Productos
                </template>

                <template #content>
                    <section class="space-y-4">
                        <MdTextInput
                            id="nombre"
                            name="nombre"
                            label="Nombre"
                            class="col-span-2"
                            v-model="form.nombre"
                            :uppercase="true"
                            required
                            :maxlength="85"
                            helper="Nombre del color"
                            :error="form.errors.nombre"
                            :success="!form.errors.nombre"
                        />
                    </section>
                </template>

                <template #footer>
                    <div class="space-x-2">
                        <MdButton variant="primary" :loading="IsLoading" @click="Upsert()">
                            {{ IsEditMode ? 'Actualizar Color' : 'Registrar Color' }}
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
import MdButton from '@/Components/MaterialDesign/MdButton.vue'
import MdDialogModal from '@/Components/MaterialDesign/MdDialogModal.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'

    /* ========================== Props ========================== */
    const props = defineProps({
        Colores: Object
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
        nombre: ''
    })

    const columnas = [
        { headerName: 'Nombre', field: 'nombre' },
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
            form.put(route('Colores.update', form.id), {
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
            form.post(route('Colores.store'), {
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
            form.delete(route('Colores.destroy', id), {
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
    </script>
