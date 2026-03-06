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

        <div class="bg-white min-h-screen">
            <div class="max-w-lg mx-auto px-4 py-8">

                <!-- Back link -->
                <div class="flex justify-end mb-6">
                    <Link href="/" class="text-sm text-gray-500 hover:text-[#040054] transition flex items-center gap-1">
                        Volver al carrito
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </Link>
                </div>

                <!-- If cart has items -->
                <template v-if="items && items.length > 0">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-extrabold text-gray-900">Resumen</h1>
                        <span class="text-sm text-gray-500">{{ itemCount }} artículo{{ itemCount !== 1 ? 's' : '' }}</span>
                    </div>

                    <!-- Items List -->
                    <div class="divide-y divide-gray-100">
                        <div
                            v-for="(item, idx) in items"
                            :key="idx"
                            class="flex items-center gap-4 py-4"
                        >
                            <!-- Product Image with Quantity Badge -->
                            <div class="relative flex-shrink-0">
                                <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                    <img
                                        v-if="item.image"
                                        :src="item.image"
                                        :alt="item.name"
                                        class="w-full h-full object-cover"
                                    >
                                    <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-xs">
                                        Sin imagen
                                    </div>
                                </div>
                                <!-- Quantity Badge -->
                                <span class="absolute -top-1.5 -left-1.5 w-5 h-5 bg-[#F41D27] text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow">
                                    {{ item.quantity }}
                                </span>
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1 min-w-0">
                                <Link :href="route('products.show', item.slug)" class="text-sm font-semibold text-gray-900 hover:text-[#040054] transition line-clamp-1">
                                    {{ item.name }}
                                </Link>
                                <!-- Variant Tags -->
                                <div v-if="item.variants && Object.keys(item.variants).length > 0" class="flex flex-wrap gap-1 mt-1">
                                    <span
                                        v-for="(val, key) in item.variants"
                                        :key="key"
                                        class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded border border-gray-200"
                                    >
                                        {{ val }}
                                    </span>
                                </div>
                                <!-- Quantity Controls -->
                                <div class="flex items-center gap-2 mt-1.5">
                                    <button
                                        @click="updateQuantity(idx, item.quantity - 1)"
                                        :disabled="item.quantity <= 1"
                                        class="w-6 h-6 flex items-center justify-center rounded border border-gray-300 text-gray-500 hover:bg-gray-100 transition disabled:opacity-30"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M20 12H4" /></svg>
                                    </button>
                                    <span class="text-xs font-semibold text-gray-700 w-4 text-center">{{ item.quantity }}</span>
                                    <button
                                        @click="updateQuantity(idx, item.quantity + 1)"
                                        class="w-6 h-6 flex items-center justify-center rounded border border-gray-300 text-gray-500 hover:bg-gray-100 transition"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    </button>
                                    <button
                                        @click="removeItem(idx)"
                                        class="ml-1 text-gray-400 hover:text-red-500 transition"
                                        title="Eliminar"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="text-right flex-shrink-0">
                                <span class="text-sm font-bold text-gray-900">€{{ (item.price * item.quantity).toFixed(2) }}</span>
                                <p v-if="item.quantity > 1" class="text-[10px] text-gray-400">€{{ Number(item.price).toFixed(2) }} c/u</p>
                            </div>
                        </div>
                    </div>

                    <!-- Spacer -->
                    <div class="my-8"></div>

                    <!-- Footer: Coupon + Totals -->
                    <div class="border-t border-gray-200 pt-6 space-y-4">
                        <!-- Coupon -->
                        <div class="flex gap-2">
                            <input
                                v-model="couponCode"
                                type="text"
                                placeholder="¿Tienes un cupón? Introdúcelo aquí"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-[#040054] focus:border-[#040054] transition"
                            >
                            <button
                                @click="applyCoupon"
                                class="px-4 py-2 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition"
                            >
                                Aplicar
                            </button>
                        </div>

                        <!-- Subtotal -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold text-gray-900">€{{ Number(totals.subtotal).toFixed(2) }}</span>
                        </div>

                        <!-- Discount -->
                        <div v-if="totals.discount_amount > 0" class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Descuento</span>
                            <span class="font-semibold text-green-600">-€{{ Number(totals.discount_amount).toFixed(2) }}</span>
                        </div>

                        <!-- Shipping -->
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Envío</span>
                            <span class="font-medium text-gray-500">Calculado en checkout</span>
                        </div>

                        <!-- Total -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-300">
                            <span class="text-base font-extrabold text-gray-900">Total</span>
                            <div class="text-right">
                                <span class="text-xl font-black text-gray-900">€{{ Number(totals.total).toFixed(2) }}</span>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <Link
                            :href="route('checkout.index')"
                            class="block w-full text-center bg-[#040054] text-white font-bold py-3.5 rounded-lg hover:bg-[#060078] transition shadow-md mt-2"
                        >
                            Proceder al Pago
                        </Link>

                        <!-- Continue Shopping -->
                        <div class="text-center">
                            <Link href="/" class="text-sm text-gray-500 hover:text-[#040054] transition">
                                ← Continuar comprando
                            </Link>
                        </div>
                    </div>
                </template>

                <!-- Empty Cart -->
                <template v-else>
                    <div class="text-center py-20">
                        <svg class="w-24 h-24 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h2 class="text-xl font-bold text-gray-900 mb-2">Tu carrito está vacío</h2>
                        <p class="text-gray-500 mb-8">Agrega productos para comenzar tu compra</p>
                        <Link href="/" class="inline-flex items-center px-6 py-3 bg-[#040054] text-white font-bold rounded-lg hover:bg-[#060078] transition shadow-md">
                            Ir a Comprar
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </Link>
                    </div>
                </template>
            </div>
        </div>
    </AppLayout>
</template>
