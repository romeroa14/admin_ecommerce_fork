<script setup lang="ts">
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

// @ts-ignore
const route = window.route;

const props = defineProps<{
    isOpen: boolean;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const page = usePage();
// @ts-ignore
const cartItems = computed(() => page.props.items || []);

const subtotal = computed(() => {
    return cartItems.value.reduce((sum: number, item: any) => {
        return sum + (item.price * item.quantity);
    }, 0);
});

const total = computed(() => subtotal.value);

const removeItem = (productId: number) => {
    router.post(route('cart.remove'), { product_id: productId }, {
        preserveScroll: true,
        onSuccess: () => {
            // Item removed
        }
    });
};

const updateQuantity = (item: any, delta: number) => {
    const newQuantity = item.quantity + delta;
    if (newQuantity < 1) return;
    
    // Update logic here - you might need to add a route for this
    router.post(route('cart.add'), {
        product_id: item.id,
        quantity: newQuantity
    }, {
        preserveScroll: true,
    });
};

const proceedToCheckout = () => {
    emit('close');
    router.visit(route('checkout.index'));
};
</script>

<template>
    <!-- Overlay -->
    <Transition
        enter-active-class="transition-opacity ease-linear duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity ease-linear duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div 
            v-if="isOpen"
            class="fixed inset-0 bg-black bg-opacity-50 z-40"
            @click="emit('close')"
        ></div>
    </Transition>

    <!-- Sidebar Panel -->
    <Transition
        enter-active-class="transition ease-in-out duration-300 transform"
        enter-from-class="translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition ease-in-out duration-300 transform"
        leave-from-class="translate-x-0"
        leave-to-class="translate-x-full"
    >
        <div 
            v-if="isOpen"
            class="fixed inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl z-50 flex flex-col"
        >
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-2">
                    <svg class="w-6 h-6 text-[#040054]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h2 class="text-xl font-bold text-gray-900">
                        Carrito ({{ cartItems.length }})
                    </h2>
                </div>
                <button 
                    @click="emit('close')"
                    class="text-gray-400 hover:text-gray-600 transition"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Free Shipping Banner -->
            <div v-if="total < 100" class="px-6 py-3 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-blue-900 font-medium">
                        ¡Gasta €{{ (100 - total).toFixed(2) }} más para envío gratis!
                    </span>
                </div>
                <div class="mt-2 bg-white rounded-full h-2 overflow-hidden">
                    <div 
                        class="bg-gradient-to-r from-blue-500 to-blue-600 h-full transition-all duration-500"
                        :style="{ width: `${Math.min((total / 100) * 100, 100)}%` }"
                    ></div>
                </div>
            </div>
            <div v-else class="px-6 py-3 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-green-900 font-medium text-sm">¡Envío gratis desbloqueado!</span>
                </div>
            </div>

            <!-- Cart Items - Scrollable -->
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                <!-- Empty State -->
                <div v-if="cartItems.length === 0" class="flex flex-col items-center justify-center h-full text-center">
                    <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Tu carrito está vacío</h3>
                    <p class="text-gray-500 mb-6">Agrega productos para comenzar tu compra</p>
                    <button 
                        @click="emit('close')"
                        class="px-6 py-3 bg-[#040054] text-white font-semibold rounded-lg hover:bg-[#060078] transition"
                    >
                        Continuar Comprando
                    </button>
                </div>

                <!-- Cart Items List -->
                <div v-else class="space-y-4">
                    <div 
                        v-for="item in cartItems" 
                        :key="item.id"
                        class="flex gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200 hover:border-[#040054] transition"
                    >
                        <!-- Product Image -->
                        <div class="flex-shrink-0 w-20 h-20 bg-white rounded-lg overflow-hidden border border-gray-200">
                            <img 
                                :src="item.image || 'https://via.placeholder.com/80'" 
                                :alt="item.name"
                                class="w-full h-full object-cover"
                            >
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-semibold text-gray-900 line-clamp-2 mb-1">
                                {{ item.name }}
                            </h4>
                            <p class="text-sm text-gray-500 mb-2">€{{ item.price }}</p>
                            
                            <!-- Quantity Controls -->
                            <div class="flex items-center space-x-2">
                                <button 
                                    @click="updateQuantity(item, -1)"
                                    class="w-7 h-7 flex items-center justify-center rounded-md border border-gray-300 hover:bg-gray-100 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                </button>
                                <span class="w-8 text-center font-semibold text-sm">{{ item.quantity }}</span>
                                <button 
                                    @click="updateQuantity(item, 1)"
                                    class="w-7 h-7 flex items-center justify-center rounded-md border border-gray-300 hover:bg-gray-100 transition"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Remove Button & Price -->
                        <div class="flex flex-col items-end justify-between">
                            <button 
                                @click="removeItem(item.id)"
                                class="text-gray-400 hover:text-red-500 transition"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <span class="text-base font-bold text-gray-900">
                                €{{ (item.price * item.quantity).toFixed(2) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer - Totals & Checkout -->
            <div v-if="cartItems.length > 0" class="border-t border-gray-200 px-6 py-4 bg-gray-50">
                <!-- Subtotal -->
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="text-lg font-semibold text-gray-900">€{{ subtotal.toFixed(2) }}</span>
                </div>

                <!-- Shipping -->
                <div class="flex items-center justify-between mb-4 text-sm">
                    <span class="text-gray-600">Envío</span>
                    <span class="text-green-600 font-medium">
                        {{ total >= 100 ? 'Gratis' : 'Calculado en checkout' }}
                    </span>
                </div>

                <!-- Total -->
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-300">
                    <span class="text-lg font-bold text-gray-900">TOTAL</span>
                    <span class="text-2xl font-black text-[#040054]">€{{ total.toFixed(2) }}</span>
                </div>

                <!-- Checkout Button -->
                <button 
                    @click="proceedToCheckout"
                    class="w-full bg-gradient-to-r from-[#F41D27] to-[#d4141f] hover:from-[#d4141f] hover:to-[#F41D27] text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:scale-105 flex items-center justify-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Pago Seguro</span>
                </button>

                <!-- Payment Methods -->
                <div class="mt-4 flex items-center justify-center space-x-2 opacity-60">
                    <span class="text-xs text-gray-500">Aceptamos:</span>
                    <div class="flex space-x-1">
                        <div class="w-8 h-5 bg-gray-200 rounded flex items-center justify-center text-[8px] font-bold">VISA</div>
                        <div class="w-8 h-5 bg-gray-200 rounded flex items-center justify-center text-[8px] font-bold">MC</div>
                        <div class="w-8 h-5 bg-gray-200 rounded flex items-center justify-center text-[8px] font-bold">AMEX</div>
                    </div>
                </div>

                <!-- Continue Shopping Link -->
                <button 
                    @click="emit('close')"
                    class="w-full mt-3 text-sm text-[#040054] hover:underline font-medium"
                >
                    ← Continuar Comprando
                </button>
            </div>
        </div>
    </Transition>
</template>
