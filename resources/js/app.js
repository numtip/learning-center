import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { appUrl } from './utils/urls.js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

['visit', 'get', 'post', 'put', 'patch', 'delete'].forEach((method) => {
    const original = router[method].bind(router);
    router[method] = (url, ...rest) => original(typeof url === 'string' ? appUrl(url) : url, ...rest);
});

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
