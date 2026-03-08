<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

// @ts-ignore
const route = window.route;

const props = defineProps({
    cart: Object,
    items: {
        type: Array as () => any[],
        default: () => [],
    },
    totals: {
        type: Object,
        default: () => ({ subtotal: 0, discount_amount: 0, tax_amount: 0, total: 0 }),
    },
});

const couponCode = ref('');

const itemCount = computed(() => {
    return props.items.reduce((sum: number, item: any) => sum + item.quantity, 0);
});

const removeItem = (index: number) => {
    router.post(route('cart.remove'), { index }, {
        preserveScroll: true,
    });
};

const updateQuantity = (index: number, newQty: number) => {
    if (newQty < 1) return;
    router.post(route('cart.update'), { index, quantity: newQty }, {
        preserveScroll: true,
    });
};

const applyCoupon = () => {
    // Future: apply coupon code
};
</script>

<template>
    <AppLayout>
        <Head title="Carrito de Compras" />

        <div class="bg-white min-h-screen py-8">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Carrito</h1>

                <template v-if="items && items.length > 0">
                    <div class="flex flex-col lg:flex-row gap-8 items-start">
                        
                        <!-- Left Column: Items List -->
                        <div class="flex-1 w-full bg-white">
                            <div class="divide-y divide-gray-200 border-t border-gray-200">
                                <div
                                    v-for="(item, idx) in items"
                                    :key="idx"
                                    class="py-6 flex gap-6"
                                >
                                    <!-- Product Image -->
                                    <div class="w-24 h-24 flex-shrink-0 border border-gray-200 rounded p-1">
                                        <img
                                            v-if="item.image"
                                            :src="item.image"
                                            :alt="item.name"
                                            class="w-full h-full object-contain"
                                        >
                                        <div v-else class="w-full h-full flex items-center justify-center bg-gray-50 text-gray-400 text-xs text-center">
                                            Sin imagen
                                        </div>
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 flex justify-between items-center">
                                        <div class="flex flex-col">
                                            <Link :href="route('products.show', item.slug)" class="text-base font-bold text-gray-900 hover:underline">
                                                {{ item.name }}
                                            </Link>
                                            
                                            <!-- Variant Tags -->
                                            <div v-if="item.variants && Object.keys(item.variants).length > 0" class="flex flex-wrap gap-2 mt-1 mb-2">
                                                <span
                                                    v-for="(val, key) in item.variants"
                                                    :key="key"
                                                    class="text-xs text-gray-600 border border-gray-300 px-2 py-0.5 rounded-sm"
                                                >
                                                    {{ val }}
                                                </span>
                                            </div>
                                            
                                            <div v-else class="h-2"></div> <!-- Spacer if no variants -->

                                            <!-- Actions -->
                                            <div class="flex items-center gap-4 mt-2">
                                                <!-- Quantity Controls -->
                                                <div class="flex items-center border border-gray-300 rounded-sm overflow-hidden h-8">
                                                    <button
                                                        @click="updateQuantity(idx, item.quantity - 1)"
                                                        :disabled="item.quantity <= 1"
                                                        class="w-8 h-full flex items-center justify-center text-gray-600 hover:bg-gray-100 transition disabled:opacity-50"
                                                    >
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M20 12H4" /></svg>
                                                    </button>
                                                    <span class="w-8 text-center text-sm font-semibold text-gray-900 Select-none">{{ item.quantity }}</span>
                                                    <button
                                                        @click="updateQuantity(idx, item.quantity + 1)"
                                                        class="w-8 h-full flex items-center justify-center text-gray-600 hover:bg-gray-100 transition"
                                                    >
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                                    </button>
                                                </div>

                                                <!-- Remove -->
                                                <button
                                                    @click="removeItem(idx)"
                                                    class="text-sm font-medium text-[#D18F3B] hover:text-yellow-700 transition"
                                                >
                                                    Quitar
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Price -->
                                        <div class="text-right self-end mb-2">
                                            <span class="text-lg font-bold text-gray-900">{{ $formatCurrency((item.price * item.quantity)) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Summary -->
                        <div class="w-full lg:w-96 bg-gray-50 p-6 shadow-sm border border-gray-100 sticky top-24">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-xl font-bold text-gray-900">Resumen</h2>
                                <span class="text-sm text-gray-500">{{ itemCount }} artículo{{ itemCount !== 1 ? 's' : '' }}</span>
                            </div>

                            <!-- Apply Coupon -->
                            <div class="mb-6">
                                <button class="text-sm font-medium text-yellow-500 hover:text-yellow-600 flex items-center gap-1">
                                    Aplicar cupón
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                                <!-- Optional: expand field for coupon -->
                            </div>

                            <!-- Totals -->
                            <div class="space-y-4 text-sm text-gray-900 border-b border-gray-200 pb-4 mb-4">
                                <div class="flex justify-between">
                                    <span class="font-medium">Subtotal</span>
                                    <span class="font-bold">{{ $formatCurrency(Number(totals.subtotal)) }}</span>
                                </div>
                                <div v-if="totals.discount_amount > 0" class="flex justify-between text-green-600">
                                    <span class="font-medium">Descuento</span>
                                    <span class="font-bold">-{{ $formatCurrency(Number(totals.discount_amount)) }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="font-medium">Envío</span>
                                    <span class="text-gray-500">Calculado en checkout</span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center mb-6">
                                <span class="text-lg font-bold text-gray-900">Total</span>
                                <div class="text-right">
                                    <span class="text-xl font-bold text-gray-900">{{ $formatCurrency(Number(totals.total)) }}</span>
                                </div>
                            </div>

                            <!-- Checkout Button -->
                            <Link
                                :href="route('checkout.init')"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-sm shadow-sm text-base font-bold text-white bg-yellow-500 hover:bg-yellow-600 active:bg-yellow-700 transition"
                            >
                                Realizar pedido
                            </Link>

                            <!-- Secure Payment Badge -->
                            <div class="mt-6 flex items-center justify-center gap-2 text-gray-500 text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                Pago seguro
                            </div>
                        </div>

                    </div>
                </template>

                <!-- Empty Cart -->
                <template v-else>
                    <div class="bg-gray-50 border border-gray-200 p-12 text-center rounded">
                        <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Tu carrito está vacío</h2>
                        <p class="text-gray-500 mb-8">Parece que aún no has agregado nada a tu carrito.</p>
                        <Link href="/" class="inline-flex justify-center py-3 px-8 border border-transparent rounded-sm shadow-sm text-base font-bold text-white bg-yellow-500 hover:bg-yellow-600 transition">
                            Volver a la tienda
                        </Link>
                    </div>
                </template>

            </div>
        </div>
    </AppLayout>
</template>
