import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import UseToastify from './Plugins/toastify';
import UseNotiflix from './Plugins/notiflix';
import UseFilePond from './Plugins/filepond';
import UseTippy from './Plugins/tippy';
import UseLoading from './Plugins/loading';
import '@fortawesome/fontawesome-free/css/all.min.css'

import FormValidatePlugin from './Plugins/validacion'

// import './Plugins/aggrid';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.use(ZiggyVue);
        app.use(UseLoading);
        app.use(FormValidatePlugin);

        UseToastify(app);
        UseNotiflix(app);
        UseFilePond(app);
        UseTippy(app);

        app.mount(el);
    },
    progress: {
        color: '#3b82f6',
        height: '5px',
    },
});
