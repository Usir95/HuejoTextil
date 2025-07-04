<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import MdTextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import { useForm } from '@inertiajs/vue3'

const form = useForm({
    telefono: '',
    email: '',
    rfc: '',
    curp: '',
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
                v-model="form.telefono"
                label="Teléfono celular"
                helper="10 dígitos"
                name="telefono"
                id="telefono"
                :required="true"
                :maxlength="10"
                :minlength="10"
                :inputRestrict="'numbers'"
                :regex="/^\d{10}$/"
                :uppercase="false"
                iconClass="fa fa-phone"
                :error="form.errors.telefono"
                :success="!form.errors.telefono"
            />

                <MdTextInput
                    v-model="form.rfc"
                    label="RFC"
                    helper="Ej. ABCD800101XXX"
                    name="rfc"
                    id="rfc"
                    :required="true"
                    :minlength="12"
                    :maxlength="13"
                    :inputRestrict="'alphanumeric'"
                    :regex="/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/"
                    :uppercase="true"
                    iconClass="fa fa-id-card"
                    :error="form.errors.rfc"
                    :success="!form.errors.rfc"
                />

                <MdTextInput
                    v-model="form.curp"
                    label="CURP"
                    helper="ABCD800101HPLNNR09"
                    name="curp"
                    id="curp"
                    :required="true"
                    :maxlength="18"
                    :minlength="18"
                    :regex="/^[A-Z][AEIOU][A-Z]{2}\d{6}[HM][A-Z]{5}[0-9A-Z]{2}$/"
                    :inputRestrict="'alphanumeric'"
                    :uppercase="true"
                    iconClass="fa fa-id-badge"
                    :error="form.errors.curp"
                    :success="!form.errors.curp"
                />


            </div>
        </section>
    </AppLayout>
</template>
