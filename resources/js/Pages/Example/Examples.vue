<template>
    <AppLayout title="Examples">
        <template #header-right>
            <MdButton @click="ChangeModal()">Abrir modal</MdButton>
        </template>

        <DialogModal v-if="ShowModal" :show="ShowModal" @close="ChangeModal">
            <template #title>
                Crear Ejemplo
            </template>

            <template #content>
                <section ref="FormSection" class="space-y-4">
                    <MdTextInput v-model="form.nombre" required label="Nombre" />
                    <MdNumberInput v-model="form.edad_aproximada" required label="Edad Aproximada" />
                    <MdTextInput v-model="form.especie" required label="Especie" />
                    <MdColorPicker v-model="form.color_principal" required label="Color Principal" />
                    <MdDateInput v-model="form.fecha_descubrimiento" required label="Fecha de Descubrimiento" />
                    <MdDateRangeInput
                        v-model:start="form.rango_inicio"
                        v-model:end="form.rango_fin"
                        required
                        label="Rango de Fechas"
                    />
                    <MdSelectInput v-model="form.nivel_peligro" required label="Nivel de Peligro" :options="NivelesPeligro" />
                    <MdTextarea v-model="form.descripcion" required label="Descripción" />
                    <div class="flex items-center justify-center gap-4 mx-auto">
                        <MdCheckbox v-model="form.es_invisible" required label="Es invisible" />
                        <MdCheckbox v-model="form.tiene_alas" required label="Tiene alas" />
                    </div>
                    <MdSelectInput v-model="form.genero" required label="Género" :options="['macho', 'hembra', 'otro']" />
                    <MdTextInput v-model="form.clave_identificacion" required label="Clave de Identificación" />
                    <MdTextInput v-model="form.correo_contacto" required label="Correo de contacto" type="email" />
                    <MdCheckbox v-model="form.confirmacion" required label="Confirmación" />
                    <MdSelectInput v-model="form.estatus" required label="Estatus" :options="['activo', 'inactivo', 'pendiente']" />
                </section>
            </template>

            <template #footer>
                <MdButton @click="ChangeModal()">Cancelar</MdButton>
                <MdButton class="ml-2" color="primary" @click="GuardarModificar()">Guardar</MdButton>
            </template>
        </DialogModal>

    </AppLayout>
</template>

<script setup>
/* ========================== Imports ========================== */
import { ref, inject, onMounted } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

/* ========================== Layout y Componentes ========================== */
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/MaterialDesign/MdDialogModal.vue';
import MdButton from '@/Components/MaterialDesign/MdButton.vue';
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue';
import MdNumberInput from '@/Components/MaterialDesign/MdNumberInput.vue';
import MdColorPicker from '@/Components/MaterialDesign/MdColorPicker.vue';
import MdDateInput from '@/Components/MaterialDesign/MdDateInput.vue';
import MdDateRangeInput from '@/Components/MaterialDesign/MdDateRangeInput.vue';
import MdSelectInput from '@/Components/MaterialDesign/MdSelectInput.vue';
import MdTextarea from '@/Components/MaterialDesign/MdTextareaInput.vue';
import MdCheckbox from '@/Components/MaterialDesign/MdCheckbox.vue';

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
