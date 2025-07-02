<template>
    <AgGridVue
        :class="`${isDarkMode ? 'ag-theme-quartz-dark' : 'ag-theme-quartz'} w-full px-3 py-2`"
        :style="`height: ${height}`"
        :rowData="rowData"
        :columnDefs="columnDefs"
        :defaultColDef="defaultColDef"
        :animateRows="true"
        :pagination="pagination"
        :paginationPageSize="paginationPageSize"
        :suppressMenuHide="true"
        :rowClassRules="rowClassRules"
        @cellClicked="emitCellClicked"
        @grid-ready="onGridReady"
    />
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { AgGridVue } from 'ag-grid-vue3'
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
    NumberEditorModule,
    TextEditorModule ,
} from 'ag-grid-community'

import '../../../css/ag-grid-theme.css'

ModuleRegistry.registerModules([
    ClientSideRowModelModule,
    ValidationModule,
    TextFilterModule,
    NumberFilterModule,
    DateFilterModule,
    PaginationModule,
    RowSelectionModule,
    ColumnAutoSizeModule,
    RowStyleModule,
    NumberEditorModule,
    TextEditorModule,
])

const props = defineProps({
    initialRowData: { type: Array, required: true },
    initialColumnDefs: { type: Array, default: () => [] },
    pagination: { type: Boolean, default: true },
    paginationPageSize: { type: Number, default: 50 },
    height: { type: String, default: '80vh' },
    headerColor: { type: String, default: 'bg-sky-800' }
})

const emit = defineEmits(['cell-clicked'])

const isDarkMode = ref(false)
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
                  sortable: false
              }
          ]
)

const rowClassRules = ref({
    'row-error': (params) => params.data?.estado === 'error'
})

const defaultColDef = {
    editable: false,
    sortable: true,
    resizable: true,
    filter: true,
    floatingFilter: true,
    flex: 1
}

watch(() => props.initialRowData, (val) => (rowData.value = val))
watch(() => props.initialColumnDefs, (val) => {
    if (val.length) columnDefs.value = val
})

const emitCellClicked = (event) => {
    emit('cell-clicked', event)
}

const onGridReady = (params) => {
    params.api.sizeColumnsToFit()
}

onMounted(() => {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)')
    isDarkMode.value = prefersDark.matches
    prefersDark.addEventListener('change', (e) => {
        isDarkMode.value = e.matches
    })
})
</script>
