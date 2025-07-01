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
        :rowClassRules="rowClassRules"
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
    RowStyleModule,
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
    RowStyleModule
])

// Props
const props = defineProps({
    initialRowData: { type: Array, required: true },
    initialColumnDefs: { type: Array, default: () => [] },
    pagination: { type: Boolean, default: true },
    paginationPageSize: { type: Number, default: 50 },
    height: { type: String, default: '80vh' },
    // headerColor: { type: String, default: 'bg-sky-800' },
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
    // headerClass: `${props.headerColor} text-white uppercase`,
}

// Reglas visuales de filas alternadas (estilo zebra)
const rowClassRules = {
    // 'ag-row-odd': (params) => params.node.rowIndex % 2 === 1,
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

<style>
.ag-theme-quartz {
    --ag-background-color: #0F172A;
    --ag-header-cell-font-family: 'Patrick Hand', sans-serif;
    --ag-header-background-color: #075985;
    --ag-header-cell-hover-background-color: #08679b;
    --ag-header-foreground-color: #075985;

    --ag-header-row-height: 42px;
    --ag-header-font-size: 14px;
    --ag-header-font-weight: 600;
    --ag-foreground-color: #F8FAFC;

    --ag-accent-color: #f838ef;
    --ag-odd-row-background-color: #1C2C37F0;
    --ag-border-color: #2c3e50;
    --ag-row-border-width: 1px;
    --ag-font-family: 'Fredoka', sans-serif;
}



</style>
