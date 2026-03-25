<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import CheckoutLayout from '@/Layouts/CheckoutLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    cart: Object,
    items: Array as () => any[],
    totals: {
        type: Object as () => any,
        default: () => ({ total: 0 })
    },
    shipping: {
        type: Object as () => any,
        default: () => ({})
    },
    shippingMethods: {
        type: Array as () => any[],
        default: () => []
    },
});

// @ts-ignore
const route = window.route;

const form = useForm({
    shipping_method: props.shipping?.shipping_method || (props.shippingMethods.length > 0 ? props.shippingMethods[0].code : ''),
});

const submit = () => {
    form.post(route('checkout.shipping.store'));
};

const currentShippingCost = computed(() => {
    const method = props.shippingMethods.find(m => m.code === form.shipping_method);
    return method ? Number(method.base_price) : 0;
});
</script>

<template>
    <CheckoutLayout
        formId="shipping-form"
        :items="items"
        :totals="totals"
        :shippingCost="currentShippingCost"
        buttonText="Continuar a Pagos"
        :buttonLoading="form.processing"
    >
        <Head title="Método de Envío" />

        <template #navigation>
            <nav class="mb-12" aria-label="Progress">
                <ol role="list" class="flex space-x-4 md:space-x-8 text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-400">
                    <li class="flex items-center">
                        <Link href="/checkout/address" class="hover:text-gray-900 transition-colors">1. Dirección</Link>
                        <svg class="h-4 w-4 ml-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg>
                    </li>
                    <li class="flex items-center text-gray-900 border-b-2 border-gray-900 pb-1">
                        <span>2. Envío</span>
                        <svg class="h-4 w-4 ml-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg>
                    </li>
                    <li class="flex items-center">
                        <span>3. Pago</span>
                    </li>
                </ol>
            </nav>
        </template>

        <h2 class="text-2xl font-black text-gray-900 mb-8 tracking-tight">Método de Envío</h2>

        <form id="shipping-form" @submit.prevent="submit">
            <div class="space-y-4">
                <!-- Dynamic Shipping Methods -->
                <label 
                    v-for="method in shippingMethods" 
                    :key="method.id"
                    class="relative flex cursor-pointer rounded-lg border bg-white p-5 shadow-sm focus:outline-none transition-all duration-200"
                    :class="{'border-[#f6ab1a] ring-1 ring-[#f6ab1a] bg-yellow-50/20': form.shipping_method === method.code, 'border-gray-200 hover:border-gray-300': form.shipping_method !== method.code}"
                >
                    <input type="radio" v-model="form.shipping_method" :value="method.code" class="sr-only">
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-bold text-gray-900">{{ method.name }}</span>
                            <span class="mt-1 flex items-center text-sm text-gray-500">
                                {{ method.description || `Tiempo estimado: ${method.estimated_delivery}` }}
                            </span>
                        </span>
                    </span>
                    <!-- Delivery Cost Status -->
                    <div class="flex items-center gap-4">
                        <span v-if="method.base_price > 0" class="text-sm font-bold text-gray-900">{{ $formatCurrency(method.base_price) }}</span>
                        <span v-else class="text-sm font-extrabold text-[#1CA862]">Gratis</span>
                        
                        <!-- Selected Checkmark -->
                        <div class="w-6 h-6 rounded-full flex items-center justify-center border-2 transition-colors"
                            :class="form.shipping_method === method.code ? 'border-[#f6ab1a] bg-[#f6ab1a]' : 'border-gray-300'">
                            <svg v-if="form.shipping_method === method.code" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                </label>
                
                <div v-if="shippingMethods.length === 0" class="p-4 text-center text-gray-500 border border-gray-200 rounded-lg">
                    No hay métodos de envío disponibles.
                </div>
            </div>
        </form>
    </CheckoutLayout>
</template>
