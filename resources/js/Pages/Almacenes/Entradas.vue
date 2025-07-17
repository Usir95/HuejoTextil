<template>
    <AppLayout title="Entradas">
        <template #header-left>
            <div class=" text-gray-600 dark:text-gray-300 text-lg font-semibold uppercase">
                Registra una entrada seleccionando el producto, almacén y cantidad a ingresar.
            </div>
        </template>

        <section ref="FormSection" class="grid grid-cols-1 md:grid-cols-2">
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

        <section class="w-full flex flex-row justify-center my-4">
            <div id="Etiqueta">
            </div>
        </section>
    </AppLayout>
</template>


<script setup>
import { ref, inject, defineProps, nextTick } from 'vue'
import { useForm, } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MdSelectInput from '@/Components/MaterialDesign/MdSelectInput.vue'
import MdNumberInput from '@/Components/MaterialDesign/MdNumberInput.vue'
import MdButton from '@/Components/MaterialDesign/MdButton.vue'
import JsBarcode from 'jsbarcode'
import QRCode from 'qrcode'


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
            ImprimirEtiqueta()
            toast('Entrada registrada correctamente', 'success')
        },
        onError: () => {
            IsLoading.value = false;
            toast('Ocurrió un error al registrar la entrada', 'danger')
        }
    })
}

const ImprimirEtiqueta = async () => {
    const producto = props.Productos.find(p => p.value === form.producto_id)?.label || '';
    const color = props.Colores.find(c => c.value === form.color_id)?.label || '';
    const calidad = props.TiposCalidades.find(c => c.value === form.tipo_calidad_id)?.label || '';
    const cantidad = form.cantidad;
    const fecha = new Date().toLocaleDateString();

    const codigo = `PROD-${form.producto_id}-COL-${form.color_id}-CAL-${form.tipo_calidad_id}`;

    const html = `
        <div style="font-family: sans-serif; width: 90mm; border: 0.5mm solid #000; padding: 2mm; box-sizing: border-box;">
            <!-- Código de barras arriba -->
            <div style="display: flex; justify-content: center; margin-bottom: 2mm;">
                <svg id="barcode" style="width: 50mm; height: 10mm;"></svg>
            </div>

            <!-- Info + QR -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="font-size: 3mm; font-weight: bold; line-height: 1.4;">
                    <div>Producto: ${producto}</div>
                    <div>Color: ${color}</div>
                    <div>Calidad: ${calidad}</div>
                    <div>Cantidad: ${cantidad}</div>
                    <div>Fecha: ${fecha}</div>
                </div>

                <canvas id="qrcode" width="20mm" height="20mm" style="width: 20mm; height: 20mm;"></canvas>
            </div>
        </div>
    `;

    const etiquetaDiv = document.getElementById('Etiqueta');
    etiquetaDiv.innerHTML = html;

    await nextTick();

    JsBarcode("#barcode", codigo, {
        format: "CODE128",
        width: 2, // mm → se verá más delgado
        height: 10 * 2.78, // mm → píxeles (1mm ≈ 3.78px)
        displayValue: false
    });

    const canvasQR = document.getElementById("qrcode");
    await QRCode.toCanvas(canvasQR, codigo, {
        width: 20 * 3.78, // mm → píxeles
        margin: 0
    });

    ImprimirElemento(etiquetaDiv);

    form.cantidad = '';
    await nextTick();

    const input = document.getElementById('cantidad');
    if (input) input.focus();
};

const ImprimirElemento = (elemento) => {
    const ventana = window.open('', '', 'width=600,height=400');
    ventana.document.write(`
        <html>
            <head><title>Etiqueta</title></head>
            <body style="margin:0; padding:0;">
                ${elemento.innerHTML}
            </body>
        </html>
    `);
    ventana.document.close();
    ventana.focus();
    ventana.print();
    ventana.close();
    console.log('Simulación: orden de impresión enviada');
    toast('Etiqueta lista para imprimir (simulado)', 'info');
};


</script>
