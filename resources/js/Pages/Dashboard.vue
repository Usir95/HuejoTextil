<script setup>
import { ref, inject } from 'vue'
import { useForm } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';


const form = useForm({

});



</script>

<template>
    <AppLayout title="Menú">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Menu principal
            </h2>
        </template>
    </AppLayout>
</template>
