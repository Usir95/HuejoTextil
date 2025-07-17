<template>
    <AppLayout title="Entradas">
        <template #header-left>
            <div class=" text-gray-600 dark:text-gray-300 text-lg font-semibold uppercase">
                Registra una entrada seleccionando el producto, almacén y cantidad a ingresar.
            </div>
        </template>

        <section ref="FormSection" class="grid grid-cols-1 md:grid-cols-2 gap-4 mx-6 my-4">
            <MdSelectInput
                id="producto_id"
                name="producto_id"
                class="col-span-2"
                v-model="form.producto_id"
                required
                label="Producto"
                helper="Seleccione un producto"
                :error="form.errors.producto_id"
                :success="!form.errors.producto_id"
                :options="Productos"
            />

            <MdSelectInput
                id="color_id"
                name="color_id"
                v-model="form.color_id"
                required
                label="Color"
                helper="Seleccione un color (opcional)"
                :error="form.errors.color_id"
                :success="!form.errors.color_id"
                :options="Colores"
            />

            <MdSelectInput
                id="tipo_calidad_id"
                name="tipo_calidad_id"
                v-model="form.tipo_calidad_id"
                required
                label="Calidad"
                helper="Seleccione una calidad (opcional)"
                :error="form.errors.tipo_calidad_id"
                :success="!form.errors.tipo_calidad_id"
                :options="TiposCalidades"
            />

            <MdNumberInput
                id="cantidad"
                name="cantidad"
                class="col-span-2"
                v-model="form.cantidad"
                required
                label="Cantidad"
                helper="Ingrese el peso/cantidad"
                :error="form.errors.cantidad"
                :success="!form.errors.cantidad"
            />

            <div class="flex justify-center col-span-2 w-full mt-4">
                <MdButton variant="primary" :loading="IsLoading" class="w-full py-2" @click="Insert()">
                    Regitrar material
                </MdButton>
            </div>
        </section>
    </AppLayout>
</template>


<script setup>
import { ref, inject, defineProps } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MdSelectInput from '@/Components/MaterialDesign/MdSelectInput.vue'
import MdNumberInput from '@/Components/MaterialDesign/MdNumberInput.vue'
import MdButton from '@/Components/MaterialDesign/MdButton.vue'

const props = defineProps({
    Productos: Object,
    Colores: Object,
    TiposCalidades: Object,
    Almacenes: Object,
})

const toast = inject('$toast');
const FormValidate = inject('FormValidate');
const IsLoading = ref(false);
const FormSection = ref(null);

const form = useForm({
    producto_id: '',
    color_id: '',
    tipo_calidad_id: '',
    cantidad: ''
})

const Insert = () => {
    if (!FormValidate(FormSection)) return
    IsLoading.value = true;
    form.post(route('Entradas.store'), {
        onSuccess: (response) => {
            console.log(response.data);

            IsLoading.value = false;
            ImprimirEtiqueta(FormSection)
            toast('Entrada registrada correctamente', 'success')
        },
        onError: () => {
            IsLoading.value = false;
            toast('Ocurrió un error al registrar la entrada', 'danger')
        }
    })
}
</script>
