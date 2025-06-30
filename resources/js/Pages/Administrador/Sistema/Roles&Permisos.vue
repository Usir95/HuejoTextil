<template>
    <AppLayout title="Roles y permisos del sistema">
        <template #options>
        </template>

    <section>
      <AgGrid :rowData="usuarios" />
    </section>
    </AppLayout>
</template>

<script setup>
import { ref, defineProps, inject, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import AgGrid from '@/Components/Dependencies/AgGrid.vue'

/* ============================================ Props ============================================ */
const props = defineProps({
    Roles: Object,
    Permisos: Object,
});

/* ============================================ Variables ============================================ */
const toast = inject('$toast');
const confirm = inject('$confirm');
const notify = inject('$notify');

/* ============================================ Mounted ============================================ */
onMounted(() => {
 notify('Probando Notiflix', 'success');
});

/* ============================================ Functions ============================================ */
const usuarios = [
  { nombre: 'Arianna', correo: 'ari@example.com', edad: 25 },
  { nombre: 'Siomara', correo: 'sio@example.com', edad: 30 },
]
</script>
