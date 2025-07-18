<template>
    <AppLayout title="Entradas">
        <template #header-left>
            <div class=" text-gray-600 dark:text-gray-300 text-lg font-semibold uppercase">
                Registra una entrada seleccionando el producto, almacén y cantidad a ingresar.
            </div>
        </template>

        <section ref="FormSection" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Interfaz de Conexión de Báscula Serial -->
            <div class="col-span-2 flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Conexión de Báscula</h2>
                <button
                    @click="connectSerial"
                    :disabled="isConnecting || isConnected"
                    class="px-6 py-3 text-lg font-semibold text-white bg-green-600 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-75 transition duration-200 ease-in-out disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ isConnecting ? 'Conectando...' : (isConnected ? 'Conectado' : 'Conectar Báscula') }}
                </button>

                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400 text-center">
                    Estado: <span :class="statusClass">{{ status }}</span>
                </p>

                <button
                    v-if="isConnected"
                    @click="disconnectSerial"
                    class="mt-4 px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg shadow-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-75 transition duration-200 ease-in-out"
                >
                    Desconectar
                </button>
            </div>
            <!-- Fin Interfaz de Conexión de Báscula Serial -->

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
                :helper="cantidadHelperText"
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
import { ref, inject, defineProps, nextTick, computed, onMounted, onUnmounted } from 'vue'
import { useForm, usePage  } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import MdSelectInput from '@/Components/MaterialDesign/MdSelectInput.vue'
import MdNumberInput from '@/Components/MaterialDesign/MdNumberInput.vue'
import MdButton from '@/Components/MaterialDesign/MdButton.vue'
import axios from 'axios'
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

// Variables reactivas para el estado de la conexión serial
const status = ref('Desconectado');
const isConnecting = ref(false);
const isConnected = ref(false);
const currentUnit = ref(''); // La bascula manda unidades

// Referencias para el objeto SerialPort y el lector de flujo
let portRef = ref(null);
let readerRef = ref(null);

// Propiedad computada para la clase de estilo del mensaje de estado
const statusClass = computed(() => {
  if (isConnected.value) return 'text-green-600 font-semibold';
  if (isConnecting.value) return 'text-yellow-600 font-semibold';
  return 'text-red-600';
});

// Propiedad computada para el texto de ayuda del MdNumberInput
const cantidadHelperText = computed(() => {
    if (isConnected.value && currentUnit.value) {
        return `Ingrese el peso/cantidad (Unidad: ${currentUnit.value.toUpperCase()})`;
    }
    return 'Ingrese el peso/cantidad';
});


const form = useForm({
    producto_id: '',
    color_id: '',
    tipo_calidad_id: '',
    cantidad: '' // Este valor será actualizado por los datos seriales
})

/**
 * @function connectSerial
 * @description Inicia el proceso de conexión al puerto serial usando la Web Serial API.
 * Solicita al usuario que seleccione un puerto, lo abre y comienza a leer los datos.
 */
const connectSerial = async () => {
  if (!('serial' in navigator)) {
    status.value = 'Tu navegador no soporta la Web Serial API. Usa Chrome o Edge.';
    toast('Tu navegador no soporta la Web Serial API. Usa Chrome o Edge.', 'danger');
    return;
  }

  isConnecting.value = true;
  status.value = 'Buscando puerto serial...';

  try {
    // Solicita al usuario que seleccione un puerto serial
    portRef.value = await navigator.serial.requestPort();

    // Abre el puerto con la configuración de la báscula LP7516
    // El baud rate de 9600 es común para la LP7516.
    await portRef.value.open({ baudRate: 9600 });

    status.value = 'Conectado al puerto serial. Esperando datos...';
    isConnected.value = true;
    isConnecting.value = false;
    toast('Conexión serial establecida', 'success');

    // Configura el TextDecoder para leer los datos como texto
    const textDecoder = new TextDecoderStream();
    const readableStreamClosed = portRef.value.readable.pipeTo(textDecoder.writable);
    readerRef.value = textDecoder.readable.getReader();

    // Inicia la lectura continua de datos
    readSerialData();

  } catch (error) {
    let errorMessage = 'Error desconocido al conectar.'; // Mensaje por defecto

    if (error.message) {
      if (error.message.includes('No port selected by the user')) {
        errorMessage = 'Conexión cancelada: No se ha seleccionado ningún puerto.';
      } else if (error.message.includes('Failed to open serial port')) {
        errorMessage = 'Error al abrir el puerto: El puerto podría estar en uso o no disponible.';
      } else {
        errorMessage = `Error al conectar: ${error.message}`; // Mostrar el mensaje original si no es reconocido
      }
    }

    status.value = errorMessage;
    console.error('Error Web Serial API:', error);
    toast(errorMessage, 'danger');
    isConnecting.value = false;
    isConnected.value = false;
    portRef.value = null;
    readerRef.value = null;
  }
};

/**
 * @function readSerialData
 * @description Lee datos continuamente del puerto serial y actualiza el peso.
 * Maneja el formato de salida de la báscula LP7516.
 */
const readSerialData = async () => {
  while (true) {
    try {
      if (!readerRef.value) { // Asegurarse de que el lector exista
        break;
      }
      const { value, done } = await readerRef.value.read();

      if (done) {
        // El lector ha sido cancelado o el puerto se cerró
        console.log('Lector de puerto serial cerrado.');
        break;
      }

      const rawData = value.trim(); // Limpiar espacios en blanco

      // Solo procesar lecturas estables (ST) de la báscula
      if (rawData.startsWith('ST,')) {
        // Expresión regular para extraer el valor numérico y la unidad
        // Soporta signos (+/-), números con decimales y unidades (kg/lb)
        const weightMatch = rawData.match(/([+-]?\d+\.\d+)\s*(kg|lb)/i);

        if (weightMatch && weightMatch[1]) {
          const weightValue = parseFloat(weightMatch[1]); // Obtener el número flotante
          const unit = weightMatch[2] ? weightMatch[2].toLowerCase() : '';

          form.cantidad = weightValue; // Asignar el valor numérico directamente al v-model
          currentUnit.value = unit; // Actualizar la unidad para el helper text
          status.value = `Recibiendo peso: ${weightValue} ${unit}`;
        } else {
          // Si no se pudo extraer el peso pero es una lectura estable
          console.warn('Datos estables sin formato de peso reconocido:', rawData);
          status.value = `Datos estables (formato desconocido): ${rawData}`;
        }
      } else {
        // Datos inestables (US) o de otro tipo, se pueden ignorar o registrar
        console.log('Datos inestables o no reconocidos:', rawData);
        status.value = `Recibiendo datos inestables/otros: ${rawData}`;
      }
    } catch (error) {
      status.value = `Error de lectura: ${error.message}`;
      console.error('Error al leer del puerto serial:', error);
      toast(`Error de lectura: ${error.message}`, 'danger');
      // Si hay un error fatal, intentar desconectar
      disconnectSerial();
      break; // Salir del bucle de lectura
    }
  }
};

/**
 * @function disconnectSerial
 * @description Desconecta el puerto serial y reinicia el estado.
 */
const disconnectSerial = async () => {
  if (readerRef.value) {
    try {
      await readerRef.value.cancel(); // Cancela cualquier lectura pendiente
      readerRef.value.releaseLock(); // Libera el bloqueo del lector
    } catch (error) {
      console.error('Error al cancelar/liberar lector:', error);
    }
    readerRef.value = null;
  }
  if (portRef.value && portRef.value.opened) {
    try {
      await portRef.value.close();
    } catch (error) {
      console.error('Error al cerrar el puerto:', error);
    }
  }
  portRef.value = null;
  isConnected.value = false;
  isConnecting.value = false;
  status.value = 'Desconectado';
  form.cantidad = ''; // Limpiar la cantidad al desconectar
  currentUnit.value = '';
  toast('Puerto serial desconectado', 'info');
  console.log('Puerto serial desconectado.');
};

// Maneja la desconexión inesperada del dispositivo
onMounted(() => {
  if ('serial' in navigator) {
    navigator.serial.addEventListener('disconnect', (event) => {
      if (portRef.value && event.target === portRef.value) {
        status.value = 'Báscula desconectada inesperadamente.';
        toast('Báscula desconectada inesperadamente.', 'danger');
        disconnectSerial(); // Llama a la función de desconexión para limpiar el estado
      }
    });
  }
});

// Limpieza al desmontar el componente
onUnmounted(() => {
  disconnectSerial();
});


const Insert = async () => {
    if (!FormValidate(FormSection)) return
    IsLoading.value = true

    try {
        const response = await axios.post(route('Entradas.store'), form)

        const id = response?.data?.movimiento_id
        if (id) ImprimirEtiqueta(id)

        toast('Entrada registrada correctamente', 'success')
    } catch (error) {
        toast('Error al registrar la entrada', 'danger')
        console.error(error)
    } finally {
        IsLoading.value = false
    }
}

const ImprimirEtiqueta = async (Id) => {
    const producto = props.Productos.find(p => p.value === form.producto_id)?.label || '';
    const color = props.Colores.find(c => c.value === form.color_id)?.label || '';
    const calidad = props.TiposCalidades.find(c => c.value === form.tipo_calidad_id)?.label || '';
    const cantidad = form.cantidad;
    const fecha = new Date().toLocaleDateString();

    const codigo = `PROD-${form.producto_id}-COL-${form.color_id}-CAL-${form.tipo_calidad_id}-MOV-${Id}`;

    const qrDataUrl = await QRCode.toDataURL(codigo, {
        width: 20 * 3.78, // 20mm en px
        margin: 0
    });

    const html = `
        <div style="font-family: sans-serif; width: 60mm; border: 0.5mm solid #000; padding: 2mm; box-sizing: border-box;">
            <!-- Código de barras -->
            <div style="display: flex; justify-content: center; margin-bottom: 2mm;">
                <svg id="barcode" style="width: 50mm; height: 10mm;"></svg>
            </div>

            <!-- Info + QR -->
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="font-size: 3mm; font-weight: bold; line-height: 1.4;">
                    <div text-align: center;> ${codigo} </div>
                    <div>Producto: ${producto}</div>
                    <div>Color: ${color}</div>
                    <div>Cantidad: ${cantidad}</div>
                </div>

                <!-- QR como imagen -->
                <img src="${qrDataUrl}" width="60mm" height="60mm" style="object-fit: contain;" />
            </div>
        </div>
    `;

    const etiquetaDiv = document.getElementById('Etiqueta');
    etiquetaDiv.innerHTML = html;

    await nextTick();

    JsBarcode("#barcode", codigo, {
        format: "CODE128",
        width: 2,
        height: 10 * 2.78,
        displayValue: false
    });

    ImprimirElemento(etiquetaDiv);

    form.cantidad = '';
    await nextTick();

    const input = document.getElementById('cantidad');
    if (input) input.focus();
};

const ImprimirElemento = (elementoOriginal) => {
    const ventana = window.open('', '', 'width=600,height=400');

    ventana.document.write(`
        <html>
            <head><title>Etiqueta</title></head>
            <body style="margin:0; padding:0;"></body>
        </html>
    `);
    ventana.document.close();

    // Clonar el nodo con todos sus hijos y canvas incluidos
    const clon = elementoOriginal.cloneNode(true);

    // Esperar a que la ventana esté lista
    ventana.onload = () => {
        ventana.document.body.appendChild(clon);
        ventana.focus();
        ventana.print();
        ventana.close();
        console.log('Simulación: orden de impresión enviada');
        toast('Etiqueta lista para imprimir (simulado)', 'info');
    };
};
</script>
