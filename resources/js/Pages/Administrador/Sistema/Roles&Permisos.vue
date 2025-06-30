<template>
    <AppLayout title="Roles y permisos del sistema">
        <template #options>
            <!-- Opciones si las necesitas -->
        </template>

        <section>
            <AgGrid
                :initialRowData="Usuarios"
                :initialColumnDefs="columnDefs"
                :pagination="true"
                :paginationPageSize="50"
                :height="'70vh'"
                @cell-clicked="onCellClicked"
            />
        </section>
    </AppLayout>
</template>

<script setup>
import { ref, defineProps, inject, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import AgGrid from '@/Components/Dependencies/AgGrid.vue'

/* ============================================ Props ============================================ */
const props = defineProps({
    Roles: Object,
    Permisos: Object,
    Usuarios: Object,
})

/* ============================================ Columnas AG Grid ============================================ */
const columnDefs = [
    { headerName: 'ID', field: 'id', filter: 'agNumberColumnFilter', minWidth: 100 },
    { headerName: 'Usuario', field: 'usuario', filter: 'agTextColumnFilter', minWidth: 150 },
    { headerName: 'Nombre', field: 'nombre', filter: 'agTextColumnFilter', minWidth: 150 },
    { headerName: 'Email', field: 'correo', filter: 'agTextColumnFilter', minWidth: 150 },
    {
        headerName: 'Acciones',
        field: 'actions',
        pinned: 'right',
        maxWidth: 130,
        cellRenderer: (params) => {
            const row = encodeURIComponent(JSON.stringify(params.data))
            return `
                <div>
                    <button data-action="Editar" data-row="${row}" class="text-indigo-500 hover:text-indigo-900" title="Editar registro">
                        Editar
                    </button>
                    <button data-action="Eliminar" data-row="${row}" class="text-red-600 hover:text-red-900 ms-4" title="Eliminar registro">
                        Eliminar
                    </button>
                </div>
            `
        },
        filter: false,
        sortable: false,
        resizable: false
    }
]

// Acción al dar clic
const onCellClicked = ({event}) => {
    const action = event.target.dataset.action
    const rowData = event.target.dataset.row

    if (!action || !rowData) return

    const row = JSON.parse(decodeURIComponent(rowData))

    if (action === 'Editar') {
        console.log('Editar', row)
    } else if (action === 'Eliminar') {
        console.log('Eliminar', row)
    }
}


/* ============================================ Utilidades ============================================ */
const toast = inject('$toast')
const confirm = inject('$confirm')
const notify = inject('$notify')

onMounted(() => {
    notify('Lista de usuarios cargada correctamente', 'success')
})
</script>
