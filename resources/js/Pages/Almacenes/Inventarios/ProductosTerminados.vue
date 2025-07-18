    <template>
        <AppLayout title="ProductosTerminados">
            <template #header-right>
            </template>

            <AgGrid
                :initial-row-data="Inventarios"
                :initial-column-defs="columnas"
                height="85vh"
            />
        </AppLayout>
    </template>

<script setup>
import { ref, inject, defineProps } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AgGrid from '@/Components/Dependencies/AgGrid.vue'
import MdButton from '@/Components/MaterialDesign/MdButton.vue'


/* ========================== Props ========================== */
const props = defineProps({
    Inventarios: Object
})

/* ========================== Refs ========================== */
const toast = inject('$toast')
const FormValidate = inject('FormValidate')
const IsLoading = ref(false);
const IsEditMode = ref(false)
const modal = ref(false)
const FormSection = ref(null)

const form = useForm({
    id: '',
    nombre: ''
})

const columnas = [
    { headerName: 'Codigo', field: 'producto.codigo' },
    { headerName: 'Nombre', field: 'producto.nombre'},
    { headerName: 'Color', field: 'color.nombre', },
    { headerName: 'Calidad', field: 'tipo_calidad.nombre'},
    {
        headerName: 'Cantidad',
        field: 'cantidad',
        cellRenderer: ({ value }) => {
            return `<span class="inline-block px-6 text-sm font-semibold text-white bg-pink-500 rounded-full">${value}</span>`
        }
    }
]

const acciones = [
]


/* ========================== Funciones ========================== */
const ToggleModal = () => {
    modal.value = !modal.value
}
</script>
