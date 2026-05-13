import './bootstrap';
import '../sass/app.scss';

import { createApp, h } from 'vue';
import { createInertiaApp, Link, Head } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Plugins
import * as bootstrap from 'bootstrap';
import Swal from 'sweetalert2';
import flatpickr from 'flatpickr';
import TomSelect from 'tom-select';

// Translation composable + plugin
import { i18nPlugin } from './composables/useI18n.js';

// Expose globals
window.bootstrap = bootstrap;
window.Swal = Swal;
window.flatpickr = flatpickr;
window.TomSelect = TomSelect;

const appName = import.meta.env.VITE_APP_NAME || 'Coffee POS';

createInertiaApp({
    title: (title) => (title ? `${title} | ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18nPlugin)
            .component('Link', Link)
            .component('Head', Head);

        app.config.globalProperties.$swal = Swal;

        app.mount(el);
        return app;
    },
    progress: {
        color: '#0d6efd',
        showSpinner: true,
    },
});
