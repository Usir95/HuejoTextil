import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const IsLoading = ref(false);

const startLoading = () => (IsLoading.value = true);
const stopLoading = () => (IsLoading.value = false);

export default {
    install(app) {
        // Provee para usar manualmente
        app.provide('IsLoading', IsLoading);
        app.provide('startLoading', startLoading);
        app.provide('stopLoading', stopLoading);

        // Activa automáticamente junto con la barra de progreso
        router.on('start', startLoading);
        router.on('finish', stopLoading);
        router.on('error', stopLoading);
        router.on('invalid', stopLoading);
    },
    IsLoading,
};
