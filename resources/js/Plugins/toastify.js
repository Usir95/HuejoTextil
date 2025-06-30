import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

export default function UseToastify(app) {
    const toastFn = (texto, tipo = 'info', posicion = 'top-right', tema = 'auto') => {
        if (tipo === 'danger') tipo = 'error';
        toast(texto, {
            type: tipo,
            position: posicion,
            autoClose: 1500,
            closeButton: true,
            hideProgressBar: false,
            draggable: true,
            pauseOnHover: true,
            theme: tema,
        });
    };

    app.provide('$toast', toastFn);
    app.config.globalProperties.$toast = toastFn;
}
