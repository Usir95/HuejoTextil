    <template>
        <AppLayout title="Impresión de entradas">
            <template #header-left>
            </template>

            <section class="flex mx-4">
                <MdSelectSearchInput
                    id="cliente_id"
                    name="cliente_id"
                    class="col-span-2"
                    v-model="form.cliente_id"
                    label="Cliente"
                    helper="Seleccione un cliente"
                    :options="Clientes"
                />

                <MdTextInput
                    id="num_tarjeta"
                    name="num_tarjeta"
                    v-model="form.num_tarjeta"
                    label="Tarjeta viajera"
                    helper="Ingrese el numero de tarjeta"
                />

                <div>
                    <MdButton :loading="IsLoading" class="mt-4" @click="Buscar()">Buscar</MdButton>
                </div>
            </section>

            <!-- Agrega bajo tu <section> principal -->
            <section class="mt-6">
                <div class="flex items-center gap-3">
                    <MdButton :loading="IsLoading" @click="ImprimirReporteEtiquetas">Imprimir reporte etiquetas</MdButton>
                    <span v-if="HistoricoEntradas.length" class="text-sm">
                    {{ Encabezado.cliente }} · Tarjeta {{ Encabezado.tarjeta }} · {{ HistoricoEntradas.length }} rollos
                    </span>
                </div>

                <!-- Vista previa simple -->
                <div class="mt-4 mx-8 h-[50vh] overflow-auto">
                    <div v-for="(bloque, bi) in Bloques" :key="bi" class="rounded border p-2">
                        <div class="font-semibold mb-2 text-lg" v-if="bi == 0">
                            {{ Encabezado.cliente }} • TV {{ Encabezado.tarjeta }}
                        </div>
                    <div class="space-y-1">
                        <div v-for="(ln,i) in bloque" :key="i" class="font-mono text-md tracking-tight">
                            {{ ln }}
                        </div>
                    </div>
                    </div>
                </div>
            </section>


        </AppLayout>
    </template>

<script setup>
import { ref, inject, defineProps, nextTick, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AgGrid from '@/Components/Dependencies/AgGrid.vue'
import MdButton from '@/Components/MaterialDesign/MdButton.vue'
import MdSelectSearchInput from '@/Components/MaterialDesign/MdSelectSearchInput.vue'
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import axios from 'axios'

/* ========================== Props ========================== */
const props = defineProps({
    Clientes: Object,
})

/* ========================== Refs ========================== */
const toast = inject('$toast')
const IsLoading = ref(false)
const IsEditMode = ref(false)
const HistoricoEntradas = ref([])     // listado de rollos
const ResumenEntradas = ref([])       // si tu API lo envía

const form = useForm({
    id: '',
    cliente_id: '',
    num_tarjeta: '',
})

/* ========================== Búsqueda ========================== */
const Buscar = async () => {
    IsLoading.value = true
    try {
        const { data } = await axios.post(route('HistoricoEntradas.FiltrarEntradas'), {
        cliente_id: form.cliente_id,
        num_tarjeta: form.num_tarjeta,
        })
        HistoricoEntradas.value = data.entradas || []
        ResumenEntradas.value = data.resumen || []
    } catch (error) {
        toast('Ocurrió un error al buscar los registros', 'error')
        console.error(error)
    } finally {
        IsLoading.value = false
    }
}

/* ========================== Reporte en “etiquetas” ========================== */
const MAX_LINEAS_TITULO = 6;
const MAX_LINEAS_NORMAL = 7;

const toKgm = v => {
    const n = Number(v)
    return Number.isFinite(n) ? n.toFixed(2) : String(v ?? '')
}
const safe = s => (s ?? '').toString().trim()

const Encabezado = computed(() => ({
    cliente: safe(HistoricoEntradas.value?.[0]?.cliente?.nombre || ''),
    tarjeta: safe(HistoricoEntradas.value?.[0]?.num_tarjeta || form.num_tarjeta || ''),
}))

const Lineas = computed(() => {
    return (HistoricoEntradas.value || []).map(r => {
        const rollo = safe(r.num_rollo || r.id)
        const kgm   = toKgm(r.cantidad) + ' KGM'
        const prod  = safe(r.producto?.nombre || '')
        const color = safe(r.color?.nombre || '')
        return `ROLLO ${rollo} <1>  ${kgm} ${prod}${color ? ' ' + color : ''}`
    })
})

const Bloques = computed(() => {
  const out = [];
  const src = Lineas.value;
  let i = 0;

  // Primer bloque con título: 6 líneas
  if (src.length > 0) {
    out.push(src.slice(i, i + MAX_LINEAS_TITULO));
    i += MAX_LINEAS_TITULO;
  }

  // Resto de bloques: 7 líneas
  for (; i < src.length; i += MAX_LINEAS_NORMAL) {
    out.push(src.slice(i, i + MAX_LINEAS_NORMAL));
  }

  return out;
});

const ImprimirReporteEtiquetas = () => {
  if (!Bloques.value.length) {
    toast('Sin datos para imprimir', 'warning');
    return;
  }

  const estilos = `
  <style>
    @page { size: 102mm 51mm; margin: 0; }
    html, body { margin:0; padding:0; }
    img, svg { display:block; }
    .page { width:102mm; height:51mm; page-break-after:always; }
    .etiqueta-print { box-sizing:border-box; width:102mm; height:51mm; padding:2mm; display:flex; flex-direction:column; gap:1mm; font-family:system-ui,sans-serif; -webkit-print-color-adjust:exact; print-color-adjust:exact; }
    .hdr { font-weight:700; font-size:12pt; }
    .ln { font-size:11pt; white-space:nowrap; }
    @media screen { .page { margin:8px auto; outline:1px dashed #ccc; } }
    @media print { html, body { width:102mm; height:51mm; } }
  </style>`;

  const encabezado = `${safe(Encabezado.value.cliente)}${Encabezado.value.tarjeta ? ' • TV ' + Encabezado.value.tarjeta : ''}`;
  const marca = '&lt;1&gt;';
  let tamanio = Bloques.value.length;
  // Solo para imprimir: invertir el orden de las páginas
  const toPrint = Bloques.value.slice().reverse();

  let paginas = '';
  toPrint.forEach((bloque, idx) => {
    const cuerpo = bloque.map(l => `<div class="ln">${l.replace('<1>', marca)}</div>`).join('');
    const headerHtml = idx === tamanio-1 ? `<div class="hdr">${encabezado}</div>` : '';
    paginas += `
      <div class="page">
        <div class="etiqueta-print">
          ${headerHtml}
          ${cuerpo}
        </div>
      </div>`;
  });

  const html = `<!DOCTYPE html><html><head><meta charset="utf-8">${estilos}</head><body>${paginas}</body></html>`;

  const w = window.open('', '', 'width=900,height=700');
  if (!w) { toast('No se pudo abrir la ventana de impresión', 'danger'); return; }
  w.document.open(); w.document.write(html); w.document.close();
  w.focus(); w.print();
  setTimeout(() => { try { w.close(); } catch {} }, 300);
};




</script>


