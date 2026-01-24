<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    cart: Object,
    items: Array,
    totals: Object,
});

const form = useForm({
    index: null,
});

const removeItem = (index) => {
    form.index = index;
    form.post(route('cart.remove'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Carrito de Compras" />

        <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Carrito de Compras</h1>

        <div v-if="items && items.length > 0" class="lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start xl:gap-x-16">
            <section class="lg:col-span-7">
                <ul role="list" class="border-t border-b border-gray-200 divide-y divide-gray-200">
                    <li v-for="(item, idx) in items" :key="idx" class="flex py-6 sm:py-10">
                        <div class="flex-shrink-0">
                            <!-- Ideally fetch product image, here assuming logic handles it or we pass it -->
                            <div class="w-24 h-24 bg-gray-200 rounded-md overflow-hidden object-center object-cover">
                                <!-- Placeholder or real image logic -->
                                <span class="text-xs text-gray-500 flex items-center justify-center h-full">IMG</span>
                            </div>
                        </div>

                        <div class="ml-4 flex-1 flex flex-col justify-between sm:ml-6">
                            <div class="relative pr-9 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:pr-0">
                                <div>
                                    <div class="flex justify-between">
                                        <h3 class="text-sm">
                                            <Link :href="route('products.show', { product: item.product_id })" class="font-medium text-gray-700 hover:text-gray-800">
                                                Producto #{{ item.product_id }}
                                            </Link>
                                        </h3>
                                    </div>
                                    <div class="mt-1 flex text-sm">
                                        <p class="text-gray-500">Cant: {{ item.quantity }}</p>
                                    </div>
                                    <p class="mt-1 text-sm font-medium text-gray-900">€{{ item.price }}</p>
                                </div>

                                <div class="mt-4 sm:mt-0 sm:pr-9">
                                    <button @click="removeItem(idx)" type="button" class="-m-2 p-2 inline-flex text-gray-400 hover:text-gray-500">
                                        <span class="sr-only">Eliminar</span>
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </section>

            <!-- Order Summary -->
            <section class="mt-16 bg-gray-50 rounded-lg px-4 py-6 sm:p-6 lg:p-8 lg:mt-0 lg:col-span-5">
                <h2 id="summary-heading" class="text-lg font-medium text-gray-900">Resumen del Pedido</h2>

                <dl class="mt-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600">Subtotal</dt>
                        <dd class="text-sm font-medium text-gray-900">€{{ totals.subtotal }}</dd>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                        <dt class="text-base font-bold text-gray-900">Total</dt>
                        <dd class="text-base font-bold text-gray-900">€{{ totals.total }}</dd>
                    </div>
                </dl>

                <div class="mt-6">
                    <Link :href="route('checkout.index')" class="w-full bg-indigo-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 flex justify-center">
                        Proceder al Pago
                    </Link>
                </div>
                
                <div class="mt-6 text-center text-sm">
                    <p>
                        o <Link href="/" class="text-indigo-600 font-medium hover:text-indigo-500">Continuar Comprando<span aria-hidden="true"> &rarr;</span></Link>
                    </p>
                </div>
            </section>
        </div>

        <div v-else class="text-center py-20">
            <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            <h2 class="mt-4 text-lg font-medium text-gray-900">Tu carrito está vacío</h2>
            <div class="mt-6">
                <Link href="/" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    Ir a Comprar
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
