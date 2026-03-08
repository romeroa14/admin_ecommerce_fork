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
});

// @ts-ignore
const route = window.route;

const form = useForm({
    shipping_method: 'standard', // default
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
                            <!-- Standard Shipping -->
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-5 shadow-sm focus:outline-none"
                                :class="{'border-[#F41D27] ring-1 ring-[#F41D27]': form.shipping_method === 'standard', 'border-gray-300': form.shipping_method !== 'standard'}"
                            >
                                <input type="radio" v-model="form.shipping_method" value="standard" class="sr-only">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-bold text-gray-900">Envío Standard</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">2 - 4 días hábiles dependiendo la zona</span>
                                    </span>
                                </span>
                                <circle cx="12" cy="12" r="12" fill="#fff" class="text-white h-5 w-5" />
                                <span v-if="form.shipping_method === 'standard'" class="text-[#F41D27]">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </label>

                            <!-- Express Shipping (Example) -->
                            <label class="relative flex cursor-pointer rounded-lg border bg-white p-5 shadow-sm focus:outline-none"
                                :class="{'border-[#F41D27] ring-1 ring-[#F41D27]': form.shipping_method === 'express', 'border-gray-300': form.shipping_method !== 'express'}"
                            >
                                <input type="radio" v-model="form.shipping_method" value="express" class="sr-only">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-bold text-gray-900">Retiro en Tienda</span>
                                        <span class="mt-1 flex items-center text-sm text-gray-500">Visita nuestras sedes para retirar tu paquete sin costo.</span>
                                    </span>
                                </span>
                                <span v-if="form.shipping_method === 'express'" class="text-[#F41D27]">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </label>
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
                            <dd class="font-bold text-gray-900">{{ $formatCurrency(totals.total) }}</dd>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-600">Cargo de envío</dt>
                            <dd class="font-bold text-gray-900">{{ form.shipping_method === 'standard' ? 'Por Calcular' : 'Gratis' }}</dd>
                        </div>
                        <div class="border-t border-gray-200 pt-4 flex items-center justify-between font-bold text-lg mt-4">
                            <dt class="text-[#040054]">Total a Pagar</dt>
                            <dd class="text-[#F41D27]">{{ $formatCurrency(totals.total) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
