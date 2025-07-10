<template>
    <AppLayout title="Examples">
        <template #header-right>
            <MdButton @click="ChangeModal()">Abrir modal</MdButton>
        </template>

        <section>
            <AgGrid
                :initial-row-data="Examples"
                :initial-column-defs="columnas"
                :pagination="true"
                height="70vh"
                @cell-clicked="onCellClicked"
            />
        </section>

        <MdDialogModal v-if="ShowModal" :show="ShowModal" @close="ChangeModal">
            <template #title>
                Crear Ejemplo
            </template>

            <template #content>
                <section ref="FormSection" class="space-y-4">
                    <div class="grid grid-cols-1 my-2 md:grid-cols-2 gap-x-4">
                        <div class="col-span-2">
                            <MdTextInput v-model="form.nombre" required label="Nombre" />
                        </div>
                        <div>
                            <MdNumberInput v-model="form.edad_aproximada" required label="Edad Aproximada" />
                        </div>
                        <div>
                            <MdTextInput v-model="form.especie" required label="Especie" />
                        </div>
                        <div>
                            <MdColorPicker v-model="form.color_principal" required label="Color Principal" />
                        </div>
                        <div>
                            <MdTextInput v-model="form.origen" required label="Origen" />
                        </div>
                        <div>
                            <MdDateInput v-model="form.fecha_descubrimiento" required label="Fecha de Descubrimiento" />
                        </div>
                        <div class="col-span-2">
                            <MdDateRangeInput
                                v-model="form.rango"
                                required
                                label="Rango de Fechas"
                            />
                        </div>
                        <div class="col-span-2">
                            <MdSelectInput v-model="form.nivel_peligro" required label="Nivel de Peligro" :options="NivelesPeligro" />
                        </div>

                        <div class="col-span-2">
                            <MdTextareaInput v-model="form.descripcion" required label="Descripción" />
                        </div>

                        <div class="col-span-1 flex justify-around">
                            <MdCheckbox v-model="form.es_invisible" required label="Es invisible" />
                            <MdCheckbox v-model="form.tiene_alas" required label="Tiene alas" />
                        </div>

                        <div class="col-span-1">
                            <MdSelectInput v-model="form.genero" required label="Género" :options="Generos" />
                        </div>

                        <div>
                            <MdTextInput v-model="form.clave_identificacion" required label="Clave de Identificación" />
                        </div>

                        <div>
                            <MdTextInput v-model="form.correo_contacto" required label="Correo de contacto" type="email" />
                        </div>

                        <div class="col-span-2 flex justify-center">
                            <MdCheckbox v-model="form.confirmacion" required label="Confirmación" />
                        </div>

                        <div class="col-span-2">
                            <MdSelectInput v-model="form.estatus" required label="Estatus" :options="Estatus" />
                        </div>

                        <div class="col-span-2">
                            <MdFileUploader v-model="form.foto" label="Foto" />
                        </div>

                    </div>
                </section>
            </template>

            <template #footer>
                <MdButton @click="ChangeModal()">Cancelar</MdButton>
                <MdButton class="ml-2" color="primary" :loading="IsLoading" @click="GuardarModificar()">Guardar</MdButton>
            </template>
        </MdDialogModal>

    </AppLayout>
</template>

<script setup>
/* ========================== Imports ========================== */
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, inject, onMounted } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import AgGrid from '@/Components/Dependencies/AgGrid.vue';
import {
    MdDialogModal,
    MdButton,
    MdTextInput,
    MdNumberInput,
    MdColorPicker,
    MdDateInput,
    MdDateRangeInput,
    MdSelectInput,
    MdTextareaInput,
    MdCheckbox,
    MdFileUploader
} from '@/Components/MaterialDesign'



/* ========================== Layout y Componentes ========================== */


/* ========================== Props ========================== */
const props = defineProps({
    Examples: Object,
    NivelesPeligro: Object,
    Generos: Object,
    Estatus: Object
});

/* ========================== Refs y Variables ========================== */
const ShowModal = ref(false);
const FormSection = ref(null);
const FormValidate = inject('FormValidate');
const toast = inject('$toast')
const editMode = ref(false);
const confirm = inject('$confirm');
const notify = inject('$notify');
const IsLoading = ref(false);


const form = useForm({
    nombre: '',
    edad_aproximada: null,
    especie: '',
    color_principal: '#000000',
    foto: null,
    origen: '',
    fecha_descubrimiento: null,
    rango: { start: '', end: '' },
    nivel_peligro: 'medio',
    descripcion: '',
    es_invisible: false,
    tiene_alas: false,
    genero: null,
    clave_identificacion: '',
    correo_contacto: '',
    confirmacion: false,
    estatus: 'activo',
});

/* --------------------------  Aggrid --------------------------- */
const columnas = [
    { headerName: 'Nombre', field: 'nombre' },
    { headerName: 'Edad', field: 'edad_aproximada' },
    { headerName: 'Especie', field: 'especie' },
    { headerName: 'Color', field: 'color_principal' },
    { headerName: 'Nivel de Peligro', field: 'nivel_peligro' },
    { headerName: 'Tiene Alas', field: 'tiene_alas' },
    { headerName: 'Invisible', field: 'es_invisible' },
    { headerName: 'Estatus', field: 'estatus' },
    {
        headerName: 'Acciones',
        field: 'acciones',
        pinned: 'right',
        maxWidth: 130,
        cellRenderer: (params) => {
            return `
                <div class="flex gap-2">
                    <button data-action="Editar" data-id="${params.data}" title="Editar">
                        ✏️
                    </button>
                    <button data-action="Eliminar" data-id="${params.data}" title="Eliminar">
                        🗑️
                    </button>
                </div>
            `;
        },
        sortable: false,
        filter: false,
    }
]


/* ========================== Funciones ========================== */
const ChangeModal = () => {
    ShowModal.value = !ShowModal.value;
};

const onCellClicked = (event) => {
    const target = event.event.target.closest('button');
    const action = target?.dataset.action;

    if (action) {
        switch (action) {
            case 'Editar':
                Editar(event.data);
                break;
            case 'Eliminar':
                Eliminar(event.data.id);
                break;
            default:
                console.warn('Acción no reconocida:', action);
        }
    }
};

const GuardarModificar = () => {
    if (!FormValidate(FormSection)) return

    ChangeModal();
    IsLoading.value = true;
    if (editMode.value) {
        form.put(route('Example.update', form.id), {
            onSuccess: () => {
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
        form.post(route('Example.store'), {
            onSuccess: () => {
                form.reset();
                IsLoading.value = false
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

const Editar = (locacion) => {
    form.reset();
    Object.assign(form, locacion);
    editMode.value = true;
    ShowModal.value = true;
};

const Eliminar = (id) => {
    confirm(
        '¿Estás seguro?',
        'Esta acción no se puede deshacer.',
        'Sí, eliminar',
        'Cancelar',
        () => {
            form.delete(route('Example.destroy', id), {
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

/* ========================== Ciclo de vida ========================== */
onMounted(() => {

});
</script>
