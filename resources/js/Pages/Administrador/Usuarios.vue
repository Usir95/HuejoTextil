    <template>
        <AppLayout title="Usuarios">
            <template #header-right>
                <MdButton @click="ToggleModal()">Nuevo</MdButton>
            </template>

            <AgGrid
                :initial-row-data="Usuarios"
                :initial-column-defs="columnas"
                @cell-clicked="onCellClicked"
                :pagination="true"
                height="80vh"
            />

            <MdDialogModal v-if="modal" :show="modal" @close="ToggleModal">
                <template #title>
                    Crear Usuarios
                </template>

                <template #content>
                    <section class="space-y-4">
                        <MdTextInput v-model="form.nombre" label="Nombre" required />
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
    import {
        MdButton,
        MdDialogModal,
        MdTextInput
    } from '@/Components/MaterialDesign'

    /* ========================== Props ========================== */
    const props = defineProps({
        Usuarios: Object
    })

    /* ========================== Refs ========================== */
    const toast = inject('$toast')
    const FormValidate = inject('FormValidate')
    const IsLoading = ref(false);
    const IsEditMode = ref(false)
    const modal = ref(false)
    const FormSection  = ref(null)

    const form = useForm({
        id: '',
        nombre: ''
    })

    const columnas = [
        { headerName: 'Id', field: 'id', filter: 'agTextColumnFilter', minWidth: 100, maxWidth: 100, flex: 1, },
        { headerName: 'Nombre', field: 'nombre', filter: 'agTextColumnFilter', minWidth: 150, flex: 1, },
        { headerName: 'Email', field: 'correo', filter: 'agTextColumnFilter', minWidth: 150, flex: 1, },
        { headerName: 'Usuario', field: 'usuario', filter: 'agTextColumnFilter', minWidth: 150, maxWidth:150, flex: 1, },
        {
            headerName: 'Acciones',
            field: 'acciones',
            pinned: 'right',
            maxWidth: 130,
            cellRenderer: (params) => {
                return `
                <div>
                    <button data-action="Edit" data-id="${params.data.id}" title="Editar usuario" class="text-indigo-500 hover:text-indigo-900">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button data-action="Delete" data-id="${params.data.id}" title="Desactivar usuario" class="text-red-600 hover:text-red-900 ms-4">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <button data-action="ReiniciaContraseña" data-id="${params.data.id}" title="Reinciar contraseña" class="text-yellow-600 hover:text-yellow-900 ms-4">
                        <i class="fas fa-key"></i>
                    </button>
                    <button data-action="RolesUsuario" data-id="${params.data.id}" title="Asignar Rol" class="text-purple-600 hover:text-purple-900 ms-4">
                        <i class="fa-solid fa-user-secret"></i>
                    </button>
                </div>
                `;
            },
            sortable: false,
            filter: false,
        }
    ]

    /* ========================== Funciones ========================== */
    const ToggleModal = () => {
        modal.value = !modal.value
    }

    const onCellClicked = (event) => {
        const target = event.event.target.closest('button');
        const action = target?.dataset.action;

        if (action) {
            switch(action) {
                case 'Edit':
                    IsEditMode.value = true;
                    Upsert(data);
                    break;
                case 'Delete':
                    deleteUser(id);
                    break;
                case 'ReiniciaContraseña':
                    resetPassword(id);
                    break;
                case 'RolesUsuario':
                    RolesUsuario(data);
                    break;
                default:
                    console.info('Acción desconocida:', action);
            }
        }
    };

    const Upsert = () => {
        if (!FormValidate(FormSection)) {
            console.log('[Upsert] Validación fallida');
            return;
        }
        ToggleModal();
        IsLoading.value = true;
        IsEditMode.value ? Update() : Insert();
    };

    const Insert = (startTime) => {
        form.post(route('Usuarios.store'), {
            onStart: () => {
                console.log('[Insert] Inertia empezó la petición');
            },
            onSuccess: () => {
                form.reset();
                IsLoading.value = false;
                toast('Registro guardado correctamente', 'success');
            },
            onError: (e) => {
                IsLoading.value = false;
                toast('Ocurrió un error', 'danger');
            }
        });
    };

    const Update = () => {
        form.put(route('Usuarios.update', form.id), {
            onSuccess: () => {
                form.reset();
                IsLoading.value = false;
                toast('Registro actualizado', 'success');
            },
            onError: () => {
                IsLoading.value = false;
                toast('Ocurrió un error', 'danger');
            }
        });
    };

    const Edit = (locacion) => {
        form.reset();
        Object.assign(form, locacion);
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
                form.delete(route('Usuarios.destroy', id), {
                    onSuccess: () => {
                        toast('Registro eliminado', 'success');
                    },
                    onError: (e) => {
                        console.log('Ocurrió un error', e);
                    }
                });
            },
            () => {
                console.log('Acción cancelada');
            }
        );
    };
    </script>
