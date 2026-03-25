<script setup lang="ts">
import { computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { getProductImage } from '@/composables/useProductImage';

// @ts-ignore
const route = window.route;

const props = withDefaults(defineProps<{
    isOpen?: boolean;
    isCheckout?: boolean;
    checkoutTotals?: any;
    checkoutShipping?: number;
    checkoutButtonText?: string;
    checkoutFormId?: string;
    checkoutButtonLoading?: boolean;
}>(), {
    isOpen: false,
    isCheckout: false,
    checkoutTotals: null,
    checkoutShipping: -1,
    checkoutButtonText: 'Continuar',
    checkoutFormId: '',
    checkoutButtonLoading: false
});

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const page = usePage();
// @ts-ignore
const cartItems = computed(() => {
    return page.props.items || [];
});

const subtotal = computed(() => {
    if (props.isCheckout && props.checkoutTotals) return props.checkoutTotals.subtotal;
    return cartItems.value.reduce((sum: number, item: any) => {
        return sum + (item.price * item.quantity);
    }, 0);
});

const total = computed(() => {
    if (props.isCheckout && props.checkoutTotals) {
        return props.checkoutTotals.total + (props.checkoutShipping > 0 ? props.checkoutShipping : 0);
    }
    return subtotal.value;
});

const removeItem = (productId: number) => {
    if (props.isCheckout) return;
    router.post(route('cart.remove'), { product_id: productId }, {
        preserveScroll: true,
        onSuccess: () => {
            // Item removed
        }
    });
};

const updateQuantity = (item: any, delta: number) => {
    if (props.isCheckout) return;
    const newQuantity = item.quantity + delta;
    if (newQuantity < 1) return;
    
    // Update logic here
    router.post(route('cart.add'), {
        product_id: item.id,
        quantity: newQuantity
    }, {
        preserveScroll: true,
    });
};

const proceedToCheckout = () => {
    emit('close');
    router.visit(route('checkout.init'));
};
</script>

<template>
    <!-- Overlay with Blur Effect -->
    <Transition v-if="!isCheckout"
        enter-active-class="transition-opacity ease-linear duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity ease-linear duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div 
            v-if="isOpen"
            class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm z-40"
            @click="emit('close')"
        ></div>
    </Transition>

    <!-- Sidebar Panel -->
    <Transition
        :css="!isCheckout"
        enter-active-class="transition ease-in-out duration-300 transform"
        enter-from-class="translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition ease-in-out duration-300 transform"
        leave-from-class="translate-x-0"
        leave-to-class="translate-x-full"
    >
        <div 
            v-if="isOpen || isCheckout"
            :class="isCheckout 
                ? 'w-full h-full bg-white flex flex-col rounded-2xl md:min-h-[500px] border border-gray-100 shadow-[0_0_30px_rgba(0,0,0,0.03)]' 
                : 'fixed inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl z-50 flex flex-col'"
        >
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200" :class="isCheckout ? 'bg-gray-50/50 rounded-t-2xl' : ''">
                <div class="flex items-center space-x-2">
                    <svg v-if="!isCheckout" class="w-6 h-6 text-[#040054]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h2 class="text-xl font-bold text-gray-900">
                        {{ isCheckout ? 'Resumen' : 'Carrito' }} <span v-if="!isCheckout">({{ cartItems.length }})</span>
                        <span v-else class="text-sm text-gray-400 font-medium ml-2">{{ cartItems.length }} artículos</span>
                    </h2>
                </div>
                <button v-if="!isCheckout"
                    @click="emit('close')"
                    class="text-gray-400 hover:text-gray-600 transition"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Free Shipping Banner (Hide in checkout) -->
            <template v-if="!isCheckout">
                <div v-if="total < 100" class="px-6 py-3 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-blue-900 font-medium">
                            ¡Gasta {{ $formatCurrency((100 - total)) }} más para envío gratis!
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
            </template>

            <!-- Cart Items - Scrollable -->
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4 max-h-[45vh]">
                <!-- Empty State -->
                <div v-if="cartItems.length === 0" class="flex flex-col items-center justify-center h-full text-center">
                    <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Tu carrito está vacío</h3>
                    <p class="text-gray-500 mb-6">Agrega productos para comenzar tu compra</p>
                    <button v-if="!isCheckout"
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
                        class="flex gap-4 p-4 rounded-xl border border-gray-200 hover:border-[#040054]/50 transition bg-white shadow-sm"
                    >
                        <!-- Product Image -->
                        <div class="relative flex-shrink-0 w-20 h-20 bg-white rounded-lg overflow-hidden border border-gray-100 p-1">
                            <img 
                                :src="item.product ? getProductImage(item.product) : (item.image || 'https://placehold.co/80x80/f3f4f6/9ca3af?text=Sin+img')" 
                                :alt="item.name || item.product?.name"
                                class="w-full h-full object-contain"
                            >
                            <span v-if="isCheckout" class="absolute -top-1 -right-1 bg-gray-500 text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-bold shadow-sm">{{ item.quantity }}</span>
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0 flex flex-col justify-center">
                            <h4 class="text-sm font-semibold text-gray-900 line-clamp-2 mb-1">
                                {{ item.name || item.product?.name }}
                            </h4>
                            <p v-if="isCheckout" class="text-sm text-gray-500 mb-1">{{ $formatCurrency(item.price) }}</p>
                            
                            <!-- Variant Info -->
                            <p v-if="isCheckout && item.variants" class="text-xs font-medium text-gray-400 mt-1 line-clamp-1">
                                {{ Object.values(item.variants).join(', ') }}
                            </p>

                            <!-- Quantity Controls (Only in cart mode) -->
                            <div v-if="!isCheckout" class="flex justify-between items-center mt-3">
                                <p class="text-sm text-gray-500">{{ $formatCurrency(item.price) }}</p>
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
                        </div>

                        <!-- Remove Button & Price -->
                        <div class="flex flex-col items-end justify-between">
                            <button v-if="!isCheckout"
                                @click="removeItem(item.id)"
                                class="text-gray-400 hover:text-red-500 transition"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <span class="text-base font-bold text-gray-900 mt-auto">
                                {{ $formatCurrency((item.price * item.quantity)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer - Totals & Checkout -->
            <div v-if="cartItems.length > 0" class="border-t border-gray-200 px-6 py-5 bg-gray-50 flex-none pb-8 lg:pb-6" :class="isCheckout ? 'rounded-b-2xl' : ''">
                
                <!-- Coupon Input Mockup (Only for checkout) -->
                <div v-if="isCheckout" class="flex gap-2 mb-6">
                    <input type="text" placeholder="¿Tienes un cupón?" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-[#040054] focus:ring-[#040054] bg-white text-sm py-2.5 px-4 outline-none">
                    <button type="button" class="bg-gray-100 border border-gray-200 text-gray-700 px-5 py-2.5 rounded-xl font-bold text-sm tracking-wide hover:bg-gray-200 transition-colors">Aplicar</button>
                </div>

                <!-- Subtotal -->
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600 font-medium">Subtotal</span>
                    <span class="text-lg font-semibold text-gray-900">{{ $formatCurrency(subtotal) }}</span>
                </div>

                <!-- Discounts (Only for checkout) -->
                <div v-if="isCheckout && checkoutTotals?.discount_amount > 0" class="flex items-center justify-between mb-2">
                    <span class="text-green-600 font-medium text-sm">Descuentos</span>
                    <span class="font-bold text-green-600">-{{ $formatCurrency(checkoutTotals.discount_amount) }}</span>
                </div>

                <!-- Shipping -->
                <div class="flex items-center justify-between mb-4 text-sm pb-4 border-b border-gray-200/50">
                    <span class="text-gray-600 font-medium">Envío</span>
                    <span class="text-green-600 font-medium">
                        <template v-if="isCheckout">
                            <span v-if="checkoutShipping > 0">{{ $formatCurrency(checkoutShipping) }}</span>
                            <span v-else-if="checkoutShipping === 0">Gratis</span>
                            <span v-else>Calculado en checkout</span>
                        </template>
                        <template v-else>
                            {{ total >= 100 ? 'Gratis' : 'Calculado en checkout' }}
                        </template>
                    </span>
                </div>

                <!-- Total -->
                <div class="flex items-center justify-between mb-5">
                    <span class="text-xl font-bold text-gray-900 tracking-tight">TOTAL</span>
                    <span class="text-2xl font-black text-[#040054] tracking-tight">{{ $formatCurrency(total) }}</span>
                </div>

                <!-- Main Action Button -->
                <button v-if="!isCheckout"
                    @click="proceedToCheckout"
                    class="w-full bg-gradient-to-r from-[#F41D27] to-[#d4141f] hover:from-[#d4141f] hover:to-[#F41D27] active:scale-[0.98] text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:scale-[1.02] flex items-center justify-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Pago Seguro</span>
                </button>
                <button v-else
                    :form="checkoutFormId" type="submit" :disabled="checkoutButtonLoading"
                    class="w-full bg-gradient-to-r from-[#F41D27] to-[#d4141f] hover:from-[#d4141f] hover:to-[#F41D27] active:scale-[0.98] text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:scale-[1.02] flex items-center justify-center space-x-2"
                >
                    <svg v-if="checkoutButtonLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <svg v-if="!checkoutButtonLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span class="tracking-wide">{{ checkoutButtonText }}</span>
                </button>

                <!-- Payment Methods -->
                <div class="mt-5 flex items-center justify-center space-x-2 opacity-60">
                    <span class="text-xs text-gray-500">Aceptamos:</span>
                    <div class="flex space-x-1">
                        <div class="w-8 h-5 bg-gray-200 rounded flex items-center justify-center text-[8px] font-bold">VISA</div>
                        <div class="w-8 h-5 bg-gray-200 rounded flex items-center justify-center text-[8px] font-bold">MC</div>
                        <div class="w-8 h-5 bg-gray-200 rounded flex items-center justify-center text-[8px] font-bold">ZELLE</div>
                    </div>
                </div>

                <!-- View Full Cart Link (Only in cart mode) -->
                <Link v-if="!isCheckout"
                    href="/cart"
                    @click="emit('close')"
                    class="w-full mt-4 block text-center text-sm font-semibold text-[#040054] border border-[#040054]/20 py-2.5 rounded-xl hover:bg-[#040054] hover:text-white transition"
                >
                    Ver Carrito Completo
                </Link>

                <!-- Continue Shopping Link -->
                <button v-if="!isCheckout"
                    @click="emit('close')"
                    class="w-full mt-3 text-sm text-gray-500 hover:text-gray-900 font-medium transition"
                >
                    &larr; Continuar Comprando
                </button>
            </div>
        </div>
    </Transition>
</template>
