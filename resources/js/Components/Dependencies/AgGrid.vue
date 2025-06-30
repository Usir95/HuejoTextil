<template>
    <AgGridVue
        class="ag-theme-quartz w-full h-[400px]"
        :rowData="rowData"
        :columnDefs="columnDefs"
        :theme="'legacy'"
    />
</template>

<script setup>
import { AgGridVue } from 'ag-grid-vue3'
import { ref } from 'vue'

const props = defineProps({
    rowData: Array,
})

const columnDefs = ref([
    { field: 'nombre' },
    { field: 'correo' },
    { field: 'edad' },
])
</script>
