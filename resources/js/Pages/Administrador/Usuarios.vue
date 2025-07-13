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

        <MdDialogModal v-if="ShowModal" :show="ShowModal" @close="ToggleModal">
            <template #title> Crear Usuarios </template>

            <template #content>
                <section ref="FormSection" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <MdTextInput
                        id="nombre"
                        name="nombre"
                        label="Nombre"
                        v-model="form.nombre"
                        :uppercase="true"
                        required
                        helper="Nombre completo"
                        :error="form.errors.nombre"
                        :success="!form.errors.nombre"
                    />
                    <MdTextInput
                        id="apellido_paterno"
                        name="apellido_paterno"
                        label="Apellido paterno"
                        v-model="form.apellido_paterno"
                        :uppercase="true"
                        required
                        helper="Apellido paterno"
                        :error="form.errors.apellido_paterno"
                        :success="!form.errors.apellido_paterno"
                    />
                    <MdTextInput
                        id="apellido_materno"
                        name="apellido_materno"
                        v-model="form.apellido_materno"
                        label="Apellido materno"
                        :uppercase="true"
                        helper="Apellido materno"
                        :error="form.errors.apellido_materno"
                        :success="!form.errors.apellido_materno"
                    />
                    <MdNumberInput
                        id="telefono"
                        name="telefono"
                        v-model="form.telefono"
                        label="Teléfono"
                        helper="10 dígitos"
                        :error="form.errors.telefono"
                        :success="!form.errors.telefono"
                    />

                    <MdEmailInput
                        v-model="form.correo"
                        id="telefono"
                        name="telefono"
                        label="Correo"
                        helper="Ejemplo: usuario@dominio.com"
                        :error="form.errors.telefono"
                        :success="!form.errors.telefono"
                    />
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
    import { ref, inject, defineProps } from 'vue';
    import { useForm } from '@inertiajs/vue3';
    import AppLayout from '@/Layouts/AppLayout.vue';
    import AgGrid from '@/Components/Dependencies/AgGrid.vue';
    import MdEmailInput from '@/Components/MaterialDesign/MdEmailInput.vue';
    import MdNumberInput from '@/Components/MaterialDesign/MdNumberInput.vue';
    import MdButton from '@/Components/MaterialDesign/MdButton.vue';
    import MdDialogModal from '@/Components/MaterialDesign/MdDialogModal.vue';
    import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue';

    /* ========================== Props ========================== */
    const props = defineProps({
        Usuarios: Object,
    });

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
        nombre: '',
        apellido_paterno: '',
        apellido_materno: '',
        telefono: '',
        correo: '',
    });

    const columnas = [
        {
            headerName: 'Nombre',
            field: 'empleado.nombre',
            filter: 'agTextColumnFilter',
            minWidth: 150,
            flex: 1,
        },
        {
            headerName: 'Apellido paterno',
            field: 'empleado.apellido_paterno',
            filter: 'agTextColumnFilter',
            minWidth: 150,
            flex: 1,
        },
        {
            headerName: 'Apellido materno',
            field: 'empleado.apellido_materno',
            filter: 'agTextColumnFilter',
            minWidth: 150,
            flex: 1,
        },
        {
            headerName: 'Email',
            field: 'correo',
            filter: 'agTextColumnFilter',
            minWidth: 150,
            flex: 1,
        },
        {
            headerName: 'Usuario',
            field: 'usuario',
            filter: 'agTextColumnFilter',
            maxWidth: 150,
            flex: 1,
        },
        {
            headerName: 'Acciones',
            field: 'acciones',
            pinned: 'right',
            minWidth: 150,
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
        },
    ];

    /* ========================== Funciones ========================== */
    const ToggleModal = () => {
        ShowModal.value = !ShowModal.value;
    };

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
                case 'ReiniciaContraseña':
                    resetPassword(event.data.id);
                    break;
                case 'RolesUsuario':
                    RolesUsuario(event.data);
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
            form.put(route('Usuarios.update', form.id), {
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
            form.post(route('Usuarios.store'), {
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
        form.id = data.id;
        form.nombre = data.empleado.nombre;
        form.apellido_paterno = data.empleado.apellido_paterno;
        form.apellido_materno = data.empleado.apellido_materno;
        form.correo = data.correo;
        form.usuario = data.usuario;
        console.log(form);
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
