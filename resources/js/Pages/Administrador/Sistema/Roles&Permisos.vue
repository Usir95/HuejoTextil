<template>
    <AppLayout title="Roles y permisos del sistema">
        <template #options>
        </template>
    </AppLayout>
</template>

<script setup>
import { ref, defineProps, inject, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

/* ============================================ Props ============================================ */
const props = defineProps({
    Roles: Object,
    Permisos: Object,
});

/* ============================================ Variables ============================================ */
const toast = inject('$toast');
const confirm = inject('$confirm');
const report = inject('$report');
const block = inject('$block');
const notify = inject('$notify');

const items = ref([]);

/* ============================================ Mounted ============================================ */
onMounted(() => {
 notify('Probando Notiflix', 'success');
});

/* ============================================ Functions ============================================ */

</script>
