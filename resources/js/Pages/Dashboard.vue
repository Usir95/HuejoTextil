<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    nombre: '',
})


</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>
        </template>

        <section class="m-8">
            <div>
                <MdTextInput
                    v-model="form.nombre"
                    label="Nombre completo"
                    :required="true"
                    :maxlength="50"
                    :minlength="5"
                    :uppercase="false"
                    iconClass="fa fa-user"
                    :error="form.errors.nombre"
                />
            </div>
        </section>
    </AppLayout>
</template>
