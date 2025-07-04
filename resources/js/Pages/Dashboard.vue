<script setup>
import { ref, inject } from 'vue'
import { useForm } from '@inertiajs/vue3'

import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import TextInput from '@/Components/MaterialDesign/MdTextInput.vue'
import NumberInput from '@/Components/MaterialDesign/MdNumberInput.vue'
import PasswordInput from '@/Components/MaterialDesign/MdPasswordInput.vue'
import EmailInput from '@/Components/MaterialDesign/MdEmailInput.vue'

const FormValidate = inject('FormValidate')
const FormSection  = ref(null)

const form = useForm({
    nombre: '',
    correo: '',
    edad: '',
    telefono: '',
    rfc: '',
    curp: '',
    password: '',
});



function GuardaFormaulario() {
    if (!FormValidate || !FormValidate(FormSection)) return

    form.post(route('example.store'), {
        preserveScroll: true,
        onSuccess: (response) => {
            form.reset()
        },
    })
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>
        </template>

        <section  ref="FormSection">
            <div>
                <TextInput
                    v-model="form.nombre"
                    label="Nombre"
                    name="nombre"
                    id="nombre"
                    :required="true"
                    :maxlength="50"
                    :minlength="1"
                    :inputRestrict="'letters'"
                    :uppercase="false"
                    iconClass="fa fa-user"
                    :error="form.errors.nombre"
                    :success="!form.errors.nombre"
                />

                <EmailInput
                    v-model="form.correo"
                    :required="true"
                    label="Correo electrónico"
                    iconClass="fa fa-envelope"
                    helper="Ejemplo: usuario@dominio.com"
                />

                <NumberInput
                    v-model="form.edad"
                    label="Edad"
                    name="edad"
                    id="edad"
                    :required="true"
                    :maxlength="3"
                    :minlength="1"
                    :inputRestrict="'numbers'"
                    iconClass="fa fa-user"
                    helper="Edad en años"
                    :error="form.errors.edad"
                    :success="!form.errors.edad"
                />


                <TextInput
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

                <TextInput
                    v-model="form.rfc"
                    label="RFC"
                    helper="Ej. ABCD800101XXX"
                    name="rfc"
                    id="rfc"
                    :minlength="12"
                    :maxlength="13"
                    :inputRestrict="'alphanumeric'"
                    :uppercase="true"
                    iconClass="fa fa-id-card"
                    :error="form.errors.rfc"
                    :success="!form.errors.rfc"
                />

                <PasswordInput
                    v-model="form.password"
                    label="Contraseña"
                    id="password"
                    name="password"
                    :required="true"
                    :maxlength="20"
                    :minlength="4"
                    helper="Mínimo 4 caracteres"
                    :error="form.errors.password"
                    :success="!form.errors.curp"
                />

                <TextInput
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




                <button @click="GuardaFormaulario()" class="px-4 py-1 rounded-lg bg-sky-400">Guardar formulario</button>

            </div>
        </section>
    </AppLayout>
</template>
