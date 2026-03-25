<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import CartSidebar from '@/Components/CartSidebar.vue';

// @ts-ignore
const route = window.route;

const props = defineProps({
    items: {
        type: Array as () => any[],
        default: () => []
    },
    totals: {
        type: Object as () => any,
        default: () => ({ subtotal: 0, total: 0, discount_amount: 0 })
    },
    shippingCost: {
        type: Number,
        default: -1 // -1 means "Por calcular"
    },
    buttonText: {
        type: String,
        default: 'Continuar'
    },
    buttonLoading: {
        type: Boolean,
        default: false
    },
    formId: {
        type: String,
        required: true
    }
});
</script>

<template>
    <div class="min-h-screen bg-[#fcfcfc] font-sans text-gray-900 flex flex-col pt-16 lg:pt-0">
        <!-- Header -->
        <header class="border-b border-gray-100 bg-white fixed top-0 w-full z-20 lg:static lg:w-auto pb-1 lg:pb-0">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 h-16 lg:h-20 flex items-center justify-between">
                <!-- Logo -->
                <Link href="/" class="flex flex-col items-start gap-1">
                    <img src="/storage/Logos/econtainer.png" alt="Logo" class="h-8 lg:h-10 w-auto object-contain drop-shadow-sm hover:scale-105 transition-transform duration-200">
                </Link>
                
                <!-- Safe Payment Badge & Back -->
                <div class="flex items-center gap-6">
                    <div class="hidden sm:flex items-center text-gray-500 text-sm gap-2 font-medium">
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Pago seguro
                    </div>

                    <Link :href="route('cart.index')" class="text-sm font-semibold text-[#040054] hover:text-[#060078] transition-colors flex items-center gap-1">
                        Volver al carrito
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="w-full flex-grow flex flex-col pb-16 bg-[#fcfcfc]">
            <!-- max-w-[1400px] ensures it can span nice and wide. 
                 Left block uses flex-1, right block uses w-[360px] or w-[380px] so it's slim and out of the way -->
            <div class="w-full max-w-[1300px] mx-auto flex flex-col lg:flex-row gap-10 lg:gap-14 px-4 sm:px-6 lg:px-8 pt-8 lg:pt-14 justify-between">
                
                <!-- Left Side / Content -->
                <div class="flex-1 min-w-0 flex flex-col lg:pr-8 mx-auto lg:mx-0 w-full" style="max-width: 780px;">
                    <slot name="navigation" />
                    <slot />
                    
                    <div class="mt-16 pt-8 pb-4 flex flex-wrap items-center gap-6 text-xs text-gray-400 mt-auto">
                        <Link href="#" class="hover:text-gray-600 transition-colors">Términos y condiciones</Link>
                        <Link href="#" class="hover:text-gray-600 transition-colors">Políticas de privacidad</Link>
                    </div>
                </div>

                <!-- Right Side / Sidebar mapped perfectly to CartSidebar Component -->
                <aside class="w-full lg:w-[380px] xl:w-[400px] flex-shrink-0 mx-auto lg:mx-0">
                    <div class="sticky top-28 block">
                        <CartSidebar 
                            :is-checkout="true"
                            :checkout-totals="totals"
                            :checkout-shipping="shippingCost"
                            :checkout-button-text="buttonText"
                            :checkout-form-id="formId"
                            :checkout-button-loading="buttonLoading"
                        />
                    </div>
                </aside>
            </div>
        </main>
    </div>
</template>
