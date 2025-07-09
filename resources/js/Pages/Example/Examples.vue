<template>
    <AppLayout title="Examples">
        <template #header>
            <MdButton @click="ChangeModal()">Abrir modal</MdButton>
        </template>



        <DialogModal :show="ShowModal" @close="ChangeModal">
            <template #title>
                Crear Ejemplo
            </template>

            <template #content>
                <div class="space-y-4">
                    <MdTextInput v-model="form.nombre" label="Nombre" />
                    <MdNumberInput v-model="form.edad_aproximada" label="Edad Aproximada" />
                    <MdTextInput v-model="form.especie" label="Especie" />
                    <MdColorPicker v-model="form.color_principal" label="Color Principal" />
                    <MdDateInput v-model="form.fecha_descubrimiento" label="Fecha de Descubrimiento" />
                    <MdDateRangeInput
                        v-model:start="form.rango_inicio"
                        v-model:end="form.rango_fin"
                        label="Rango de Fechas"
                    />
                    <MdTimeInput v-model="form.hora_avistamiento" label="Hora de Avistamiento" />
                    <MdSelectInput v-model="form.nivel_peligro" label="Nivel de Peligro" :options="['bajo', 'medio', 'alto']" />
                    <MdTextarea v-model="form.descripcion" label="Descripción" />
                    <div class="flex items-center gap-4">
                        <MdCheckbox v-model="form.es_invisible" label="Es invisible" />
                        <MdCheckbox v-model="form.tiene_alas" label="Tiene alas" />
                    </div>
                    <MdSelectInput v-model="form.genero" label="Género" :options="['macho', 'hembra', 'otro']" />
                    <MdTextInput v-model="form.clave_identificacion" label="Clave de Identificación" />
                    <MdTextInput v-model="form.correo_contacto" label="Correo de contacto" type="email" />
                    <MdCheckbox v-model="form.confirmacion" label="Confirmación" />
                    <MdSelectInput v-model="form.estatus" label="Estatus" :options="['activo', 'inactivo', 'pendiente']" />
                </div>
            </template>

            <template #footer>
                <MdButton @click="ChangeModal()">Cancelar</MdButton>
                <MdButton class="ml-2" color="primary">Guardar</MdButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import DialogModal from '@/Components/MaterialDesign/MdDialogModal.vue';
import MdButton from '@/Components/MaterialDesign/MdButton.vue';
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue';
import MdNumberInput from '@/Components/MaterialDesign/MdNumberInput.vue';
import MdColorPicker from '@/Components/MaterialDesign/MdColorPicker.vue';
import MdDateInput from '@/Components/MaterialDesign/MdDateInput.vue';
import MdDateRangeInput from '@/Components/MaterialDesign/MdDateRangeInput.vue';
import MdTimeInput from '@/Components/MaterialDesign/MdTimeInput.vue';
import MdSelectInput from '@/Components/MaterialDesign/MdSelectInput.vue';
import MdTextarea from '@/Components/MaterialDesign/MdTextareaInput.vue';
import MdCheckbox from '@/Components/MaterialDesign/MdCheckbox.vue';

/* ============================================ Variables ============================================ */
const ShowModal = ref(false);

const form = ref({
    nombre: '',
    edad_aproximada: null,
    especie: '',
    color_principal: '#000000',
    foto: null,
    origen: '',
    fecha_descubrimiento: null,
    rango_inicio: null,
    rango_fin: null,
    hora_avistamiento: null,
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

/* ============================================ Functions ============================================ */
const ChangeModal = () => {
    ShowModal.value = !ShowModal.value;
};
</script>
