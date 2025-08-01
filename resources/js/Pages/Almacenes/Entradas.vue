<template>
    <AppLayout title="Entradas">
        <template #header-left>
            <div class=" text-gray-600 dark:text-gray-300 text-lg font-semibold uppercase">
                Registra una entrada seleccionando el producto, almacén y cantidad a ingresar.
            </div>
        </template>

        <section ref="FormSection" class="grid grid-cols-1 md:grid-cols-2 gap-4 mx-6 my-4">
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

            <MdTextInput
                id="num_tarjeta"
                name="num_tarjeta"
                v-model="form.num_tarjeta"
                required
                label="Tarjeta viajera"
                helper="Ingrese el numero de tarjeta"
                :error="form.errors.num_tarjeta"
                :success="!form.errors.num_tarjeta"
            />

            <MdTextInput
                id="num_rollo"
                name="num_rollo"
                v-model="form.num_rollo"
                label="Número de rollo"
                helper="Ingrese el numero de rollo"
                :error="form.errors.num_rollo"
                :success="!form.errors.num_rollo"
            />

            <MdSelectSearchInput
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

            <MdSelectSearchInput
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
import MdSelectSearchInput from '@/Components/MaterialDesign/MdSelectSearchInput.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'

const props = defineProps({
    Productos: Object,
    Colores: Object,
    TiposCalidades: Object,
    Almacenes: Object,
    Clientes: Object
})

const toast = inject('$toast');
const FormValidate = inject('FormValidate');
const IsLoading = ref(false);
const FormSection = ref(null);

const form = useForm({
    cliente_id: '',
    num_tarjeta: '',
    num_rollo: '',
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
