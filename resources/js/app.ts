import './bootstrap';
import '../css/app.css';

import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue);

        app.mixin({
            methods: {
                $formatCurrency(amount: number | string | undefined | null) {
                    if (amount === undefined || amount === null) return '';

                    let numericAmount = typeof amount === 'string' ? parseFloat(amount) : amount;
                    if (isNaN(numericAmount)) return '';

                    const currentCurrency = this.$page.props.currentCurrency || {
                        code: 'USD',
                        symbol: '$',
                        exchange_rate: 1,
                        symbol_position: 'before',
                        decimal_places: 2
                    };

                    const rate = parseFloat(currentCurrency.exchange_rate) || 1;
                    const decimals = parseInt(currentCurrency.decimal_places) || 2;

                    const converted = numericAmount * rate;
                    const formatted = converted.toLocaleString('en-US', {
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals,
                    });

                    if (currentCurrency.symbol_position === 'before' || currentCurrency.code === 'USD' || currentCurrency.code === 'VES') {
                        return `${currentCurrency.symbol} ${formatted}`;
                    }
                    return `${formatted} ${currentCurrency.symbol}`;
                }
            }
        });

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
