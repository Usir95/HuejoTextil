<template>
    <AppLayout title="Examples">
        <template #header-right>
            <MdButton @click="ChangeModal()">Abrir modal</MdButton>
        </template>

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
                            <MdNumberInput v-model="form.edad_aproximada" required label="Edad Aproximada" />
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
                            <MdDateInput v-model="form.fecha_descubrimiento" required label="Fecha de Descubrimiento" />
                        </div>
                        <div>
                            <MdDateRangeInput
                                v-model:start="form.rango_inicio"
                                v-model:end="form.rango_fin"
                                required
                                label="Rango de Fechas"
                            />
                        </div>
                        <div>
                            <MdDateRangeInput
                                v-model:start="form.rango_inicio"
                                v-model:end="form.rango_fin"
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
                            <MdSelectInput v-model="form.genero" required label="Género" :options="['macho', 'hembra', 'otro']" />
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
                            <MdSelectInput v-model="form.estatus" required label="Estatus" :options="['activo', 'inactivo', 'pendiente']" />
                        </div>

                    </div>
                </section>
            </template>

            <template #footer>
                <MdButton @click="ChangeModal()">Cancelar</MdButton>
                <MdButton class="ml-2" color="primary" @click="GuardarModificar()">Guardar</MdButton>
            </template>
        </MdDialogModal>

    </AppLayout>
</template>

<script setup>
/* ========================== Imports ========================== */
import { ref, inject, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
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
    MdCheckbox
} from '@/Components/MaterialDesign'


/* ========================== Layout y Componentes ========================== */
import AppLayout from '@/Layouts/AppLayout.vue';

/* ========================== Props ========================== */
defineProps({
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
const loading = ref(false);
const notify = inject('$notify');
const confirm = inject('$confirm');
const report = inject('$report');
const block  = inject('$block');


const form = useForm({
    nombre: '',
    edad_aproximada: null,
    especie: '',
    color_principal: '#000000',
    foto: null,
    origen: '',
    fecha_descubrimiento: null,
    rango_inicio: null,
    rango_fin: null,
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

/* ========================== Funciones ========================== */
const ChangeModal = () => {
    ShowModal.value = !ShowModal.value;
};


const GuardarModificar = () => {
    if (!FormValidate(FormSection)) return

};

/* ========================== Lifecycle ========================== */
onMounted(() => {

});
</script>
