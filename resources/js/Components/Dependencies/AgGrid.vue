<template>
    <AgGridVue
        class="ag-theme-quartz w-full"
        :style="`height: ${height}`"
        :rowData="rowData"
        :columnDefs="columnDefs"
        :defaultColDef="defaultColDef"
        :pagination="pagination"
        :paginationPageSize="paginationPageSize"
        :suppressMenuHide="true"
        @cellClicked="emitCellClicked"
        @grid-ready="onGridReady"
    />
</template>

<script setup>
import { AgGridVue } from 'ag-grid-vue3'
import { ref, watch } from 'vue'
import { ModuleRegistry } from 'ag-grid-community'
import {
    ClientSideRowModelModule,
    ValidationModule,
    TextFilterModule,
    NumberFilterModule,
    DateFilterModule,
    PaginationModule,
    RowSelectionModule,
    ColumnAutoSizeModule,
} from 'ag-grid-community'

ModuleRegistry.registerModules([
    ClientSideRowModelModule,
    ValidationModule,
    TextFilterModule,
    NumberFilterModule,
    DateFilterModule,
    PaginationModule,
    RowSelectionModule,
    ColumnAutoSizeModule,
])

// Props
const props = defineProps({
    initialRowData: { type: Array, required: true },
    initialColumnDefs: { type: Array, default: () => [] },
    pagination: { type: Boolean, default: true },
    paginationPageSize: { type: Number, default: 50 },
    height: { type: String, default: '80vh' },
})

// Emits
const emit = defineEmits(['cell-clicked'])

// Estado
const rowData = ref(props.initialRowData)
const columnDefs = ref(
    props.initialColumnDefs.length
    ? props.initialColumnDefs
    : [
            {
                headerName: 'Datos',
                field: 'json',
                valueGetter: (params) => JSON.stringify(params.data),
                flex: 1,
                filter: false,
                sortable: false,
            },
        ]
)

// Columnas comunes
const defaultColDef = {
    sortable: true,
    resizable: true,
    filter: true,
    floatingFilter: true,
    flex: 1,
}

// Reactividad
watch(() => props.initialRowData, (val) => (rowData.value = val))
watch(() => props.initialColumnDefs, (val) => {
    if (val.length) columnDefs.value = val
})

// Evento
function emitCellClicked(event) {
    emit('cell-clicked', event)
}

// Autoajuste
function onGridReady(params) {
    params.api.sizeColumnsToFit()
}
</script>
