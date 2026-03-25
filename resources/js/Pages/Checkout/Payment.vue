<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import CheckoutLayout from '@/Layouts/CheckoutLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    cart: Object,
    items: {
        type: Array as () => any[],
        default: () => []
    },
    totals: {
        type: Object as () => any,
        default: () => ({ total: 0 })
    },
    sessionData: {
        type: Object as () => any,
        default: () => ({})
    },
    shippingMethods: {
        type: Array as () => any[],
        default: () => []
    },
    paymentMethods: {
        type: Array as () => any[],
        default: () => []
    },
});

// @ts-ignore
const route = window.route;

const form = useForm({
    payment_method: props.paymentMethods.length > 0 ? props.paymentMethods[0].code : 'whatsapp',
});

const submit = () => {
    form.post(route('checkout.payment.store'));
};

const currentShippingCost = computed(() => {
    const shippingMethodCode = props.sessionData?.shipping?.shipping_method;
    const method = props.shippingMethods.find(m => m.code === shippingMethodCode);
    return method ? Number(method.base_price) : 0;
});
</script>

<template>
    <CheckoutLayout
        formId="payment-form"
        :items="items"
        :totals="totals"
        :shippingCost="currentShippingCost"
        :buttonText="'Confirmar Pedido en Whatsapp'"
        :buttonLoading="form.processing"
    >
        <Head title="Pago y Confirmación" />

        <template #navigation>
            <nav class="mb-12" aria-label="Progress">
                <ol role="list" class="flex space-x-4 md:space-x-8 text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-400">
                    <li class="flex items-center">
                        <Link href="/checkout/address" class="hover:text-gray-900 transition-colors">1. Dirección</Link>
                        <svg class="h-4 w-4 ml-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg>
                    </li>
                    <li class="flex items-center">
                        <Link href="/checkout/shipping" class="hover:text-gray-900 transition-colors">2. Envío</Link>
                        <svg class="h-4 w-4 ml-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg>
                    </li>
                    <li class="flex items-center text-gray-900 border-b-2 border-gray-900 pb-1">
                        <span>3. Pago</span>
                    </li>
                </ol>
            </nav>
        </template>

        <h2 class="text-2xl font-black text-gray-900 mb-8 tracking-tight">Información de Pago</h2>

        <form id="payment-form" @submit.prevent="submit">
            <!-- Dynamic Payment Methods List -->
            <div class="space-y-4">
                <div v-for="method in paymentMethods" :key="method.id" class="relative block cursor-pointer rounded-lg border bg-white p-5 shadow-sm focus:outline-none transition-all duration-200"
                    :class="{'border-[#f6ab1a] ring-1 ring-[#f6ab1a] bg-yellow-50/20': form.payment_method === method.code, 'border-gray-200 hover:border-gray-300': form.payment_method !== method.code}"
                    @click="form.payment_method = method.code">
                    
                    <label class="flex items-center gap-4 cursor-pointer">
                        <input type="radio" v-model="form.payment_method" :value="method.code" class="sr-only">
                        
                        <div class="flex-shrink-0" :class="form.payment_method === method.code ? 'text-[#f6ab1a]' : 'text-gray-400'">
                            <template v-if="method.code === 'whatsapp'">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </template>
                            <template v-else>
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </template>
                        </div>
                        <div class="flex-1">
                            <span class="block text-sm font-bold text-gray-900">{{ method.name }}</span>
                        </div>
                        
                        <!-- Selected Checkmark -->
                        <div class="w-6 h-6 rounded-full flex items-center justify-center border-2 transition-colors"
                            :class="form.payment_method === method.code ? 'border-[#f6ab1a] bg-[#f6ab1a]' : 'border-gray-300'">
                            <svg v-if="form.payment_method === method.code" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </label>
                    
                    <div v-show="form.payment_method === method.code" class="mt-4 pt-4 border-t border-yellow-100/50 text-sm text-gray-600 transition-all">
                        {{ method.description }}
                    </div>
                </div>

                <div v-if="paymentMethods.length === 0" class="p-4 text-center text-gray-500 border border-gray-200 rounded-lg">
                    No hay métodos de pago disponibles.
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 mt-8 mb-4">
                <h3 class="text-lg font-bold text-gray-900 border-b pb-4 mb-4">Resumen de tus Datos</h3>
                <div class="space-y-3 text-sm text-gray-700">
                    <p><strong class="font-semibold text-gray-900">Contacto:</strong> {{ sessionData?.address?.first_name }} {{ sessionData?.address?.last_name }}</p>
                    <p><strong class="font-semibold text-gray-900">Email:</strong> {{ sessionData?.address?.email }}</p>
                    <p><strong class="font-semibold text-gray-900">Teléfono:</strong> {{ sessionData?.address?.phone }}</p>
                    <p><strong class="font-semibold text-gray-900">Dirección a enviar:</strong> {{ sessionData?.address?.address }}, {{ sessionData?.address?.city }} ({{ sessionData?.address?.postal_code }})</p>
                    <p><strong class="font-semibold text-gray-900">Tipo de entrega:</strong> {{ shippingMethods.find(m => m.code === sessionData?.shipping?.shipping_method)?.name || sessionData?.shipping?.shipping_method }}</p>
                </div>
            </div>
        </form>
    </CheckoutLayout>
</template>
