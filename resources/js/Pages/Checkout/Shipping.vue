<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

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
</script>

<template>
    <AppLayout>
        <Head title="Método de Envío" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 bg-white min-h-screen">
            <!-- Navigation steps -->
            <nav class="mb-8" aria-label="Progress">
                <ol role="list" class="flex space-x-8 justify-center mb-10">
                    <li class="flex items-center">
                        <Link href="/checkout/address" class="text-[#040054] hover:text-[#F41D27] font-bold text-lg transition">1. Dirección</Link>
                        <svg class="h-5 w-5 ml-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg>
                    </li>
                    <li class="flex items-center">
                        <span class="text-[#F41D27] font-bold text-lg">2. Envío</span>
                        <svg class="h-5 w-5 ml-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg>
                    </li>
                    <li class="flex items-center">
                        <span class="text-gray-400 font-bold text-lg">3. Pago</span>
                    </li>
                </ol>
            </nav>

            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 xl:gap-x-16">
                <!-- Shipping Selection -->
                <div class="lg:col-span-7">
                    <h2 class="text-2xl font-bold text-[#040054] mb-8">Método de Envío</h2>

                    <form @submit.prevent="submit">
                        <div class="space-y-4">
                            <!-- Dynamic Shipping Methods -->
                            <label 
                                v-for="method in shippingMethods" 
                                :key="method.id"
                                class="relative flex cursor-pointer rounded-lg border bg-white p-5 shadow-sm focus:outline-none transition-all duration-200"
                                :class="{'border-[#F41D27] ring-1 ring-[#F41D27] bg-red-50/10': form.shipping_method === method.code, 'border-gray-200 hover:border-gray-300': form.shipping_method !== method.code}"
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
                                        :class="form.shipping_method === method.code ? 'border-[#F41D27] bg-[#F41D27]' : 'border-gray-300'">
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
                        
                        <div class="mt-8 flex justify-between items-center">
                            <Link href="/checkout/address" class="text-sm font-semibold text-gray-600 hover:text-[#040054] flex flex-row items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                Volver a Dirección
                            </Link>
                            <button type="submit" :disabled="form.processing" class="bg-[#F41D27] hover:bg-red-700 border border-transparent rounded-lg shadow-md py-4 px-8 text-lg font-bold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F41D27] transition">
                                Continuar a Pagos
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Order Preview -->
                <div class="mt-10 lg:mt-0 lg:col-span-5 bg-gray-50 border border-gray-200 rounded-xl p-6 lg:p-8 h-fit">
                    <h2 class="text-xl font-bold text-[#040054] mb-6">Resumen del Pedido</h2>
                    <dl class="space-y-4">
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-600">Subtotal</dt>
                            <dd class="font-bold text-gray-900">{{ $formatCurrency(totals.subtotal) }}</dd>
                        </div>
                        <div v-if="totals.discount_amount > 0" class="flex items-center justify-between text-sm">
                            <dt class="text-gray-600 flex items-center gap-1">
                                Descuentos
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            </dt>
                            <dd class="font-bold text-green-600">-{{ $formatCurrency(totals.discount_amount) }}</dd>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-600">Cargo de envío</dt>
                            <dd class="font-bold text-gray-900">
                                <template v-if="shippingMethods.find(m => m.code === form.shipping_method)?.base_price > 0">
                                    {{ $formatCurrency(shippingMethods.find(m => m.code === form.shipping_method)?.base_price) }}
                                </template>
                                <template v-else>
                                    <span class="text-[#1CA862]">Gratis</span>
                                </template>
                            </dd>
                        </div>
                        <div class="border-t border-gray-200 pt-4 flex items-center justify-between font-bold text-lg mt-4">
                            <dt class="text-[#040054]">Total a Pagar</dt>
                            <dd class="text-[#F41D27]">
                                {{ $formatCurrency(totals.total + Number(shippingMethods.find(m => m.code === form.shipping_method)?.base_price || 0)) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
