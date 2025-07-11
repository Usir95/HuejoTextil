<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
    import DinoPirata from '@/Components/SVG/DinoPirata.vue'
/* Props */
defineProps({
  canResetPassword: Boolean,
  status: String,
})

/* Variables */
const form = useForm({
  usuario: '',
  password: '',
  remember: false,
})

/* Functions */
const submit = () => {
  form.transform(data => ({
    ...data,
    remember: form.remember ? 'on' : '',
  })).post(route('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>

<template>
    <Head title="Iniciar sesión" />

    <div
        class="min-h-screen bg-cover bg-center flex items-center justify-center px-4" style="background-image: url('/Img/Fondo1.png');">
        <div class="w-full max-w-md bg-white/80 dark:bg-gray-800/80 backdrop-blur-md rounded-xl shadow-lg p-6 space-y-6">
            <!-- Logo -->
            <div class="flex justify-center w-36 mx-auto">
                <img src="/Img/Logo.png" alt="Logo" />
            </div>

            <!-- Título -->
            <h1 class="text-center text-4xl font-bold text-gray-800 dark:text-white">
                Iniciar sesión
            </h1>

            <!-- Estado -->
            <div v-if="status" class="text-sm text-green-600 dark:text-green-400 text-center">
                {{ status }}
            </div>

            <!-- Errores generales -->
            <div v-if="form.errors.usuario || form.errors.password" class="text-sm text-red-600">
                {{ form.errors.usuario || form.errors.password }}
            </div>

            <!-- Formulario -->
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300">Usuario</label>
                    <input
                        type="text"
                        v-model="form.usuario"
                        required
                        autofocus
                        autocomplete="username"
                        class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                </div>

                <div>
                    <label class="block text-sm text-gray-700 dark:text-gray-300">Contraseña</label>
                    <input
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                    />
                </div>

                <div class="flex items-center justify-between">
                    <!-- <label class="inline-flex items-center text-sm text-gray-700 dark:text-gray-300">
                        <input type="checkbox" v-model="form.remember" class="rounded border-gray-300 dark:border-gray-600" />
                        <span class="ml-2">Recordarme</span>
                    </label> -->

                    <!-- <div v-if="canResetPassword">
                        <Link
                            :href="route('password.request')"
                            class="text-sm text-pink-600 hover:underline"
                        >
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div> -->
                </div>

                <button
                    type="submit"
                    class="w-full bg-sky-600 border-2 border-indigo-500 text-white py-2 rounded-xl cursor-pointer hover:bg-sky-700"
                    :disabled="form.processing"
                >
                    Iniciar sesión
                </button>
            </form>
        </div>
    </div>
</template>
