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
                <div class="mt-4 mx-8">
                    <div v-for="(bloque, bi) in Bloques" :key="bi" class="rounded border p-2">
                    <div class="font-semibold mb-2 text-lg">
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
const MAX_LINEAS_POR_BLOQUE = 10

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
    const out = []
    const src = Lineas.value
    for (let i = 0; i < src.length; i += MAX_LINEAS_POR_BLOQUE) {
        out.push(src.slice(i, i + MAX_LINEAS_POR_BLOQUE))
    }
    return out
})

const ImprimirReporteEtiquetas = () => {
    if (!Bloques.value.length) {
        toast('Sin datos para imprimir', 'warning');
        return
    }

    const estilos = `
        <style>
            @page { size: 60mm 40mm; margin: 0; }
            html, body { margin:0; padding:0; }
            .page { position: relative; width:60mm; height:40mm; overflow:hidden; }
            .rotate-90 { position:absolute; top:0; left:0; width:40mm; height:60mm; transform-origin: top left; transform: rotate(90deg); }

            .etiqueta-print {
                width:60mm; height:40mm; box-sizing:border-box;
                border:0.5mm solid #000; padding:2mm; font-family: ui-monospace, Menlo, monospace;
                display:flex; flex-direction:column;
            }
            .hdr { font-size:3.0mm; font-weight:700; margin-bottom:1mm; white-space:nowrap; }
            .ln  { font-size:2.8mm; line-height:1.15; white-space:nowrap; }
        </style>`;

    const encabezado = `${safe(Encabezado.value.cliente)}${Encabezado.value.tarjeta ? ' • TV ' + Encabezado.value.tarjeta : ''}`;
    const marca = '&lt;1&gt;';

    let paginas = '';
    for (const bloque of Bloques.value) {
        const cuerpo = bloque.map(l => `<div class="ln">${l.replace('<1>', marca)}</div>`).join('');
        paginas += `
        <div class="page">
            <div class="etiqueta-print">
            <div class="hdr">${encabezado}</div>
            ${cuerpo}
            </div>
        </div>`;
    }

    const html = `<!DOCTYPE html><html><head><meta charset="utf-8">${estilos}</head><body>${paginas}</body></html>`;

    const w = window.open('', '', 'width=800,height=600');
    if (!w) { toast('No se pudo abrir la ventana de impresión', 'danger'); return; }
    w.document.open(); w.document.write(html); w.document.close();
    w.focus(); w.print();
    setTimeout(() => { try { w.close(); } catch {} }, 300);
};


</script>


