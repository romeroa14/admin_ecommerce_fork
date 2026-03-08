<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import CartSidebar from '@/Components/CartSidebar.vue';

// @ts-ignore
const route = window.route;

const searchQuery = ref('');

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    searchQuery.value = urlParams.get('search') || '';
});

function globalSearch() {
    if (searchQuery.value.trim()) {
        router.get('/products', { search: searchQuery.value.trim() });
    } else {
        router.get('/products');
    }
}

const page = usePage();
const user = computed(() => page.props.auth?.user);
// @ts-ignore
const categories = computed(() => page.props.categories || []);
const itemsCount = computed(() => {
    // @ts-ignore
    const items = page.props.items || [];
    if (!Array.isArray(items) || items.length === 0) return 0;
    return items.reduce((sum: number, item: any) => sum + (item.quantity || 1), 0);
});

// Cart sidebar state
const isCartOpen = ref(false);

// Listen for global event to open cart sidebar (e.g. from Show.vue after adding to cart)
const openCartFromEvent = () => { isCartOpen.value = true; };
onMounted(() => window.addEventListener('open-cart-sidebar', openCartFromEvent));
onUnmounted(() => window.removeEventListener('open-cart-sidebar', openCartFromEvent));

const announcements = [
    '💎 Descuentos Únicos',
    '📦 Paga al recibir en toda Caracas',
    '🚀 Despacho gratis a todo el país',
    '⭐ Productos de Calidad Premium',
    '🔒 Compra 100% Segura',
    '📱 si compras mas de 50$ el envio es gratis'
];
</script>

<template>
    <div class="min-h-screen bg-white text-gray-900">
        <!-- Infinite Ticker / Announcement Bar (Hidden as requested) -->
        <div v-if="false" class="bg-gradient-to-r from-[#F41D27] to-[#040054] text-white py-2 overflow-hidden">
            <div class="ticker-wrapper">
                <div class="ticker-content">
                    <span v-for="(announcement, idx) in announcements" :key="`a-${idx}`" class="ticker-item">
                        {{ announcement }}
                    </span>
                    <span v-for="(announcement, idx) in announcements" :key="`b-${idx}`" class="ticker-item">
                        {{ announcement }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Header -->
        <header class="w-full shadow-sm sticky top-0 z-50">
            <!-- Top Bar -->
            <div class="bg-[#040054] text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-[72px] gap-4 md:gap-8">
                        <!-- Logo -->
                        <div class="flex-1 flex items-center">
                            <Link href="/">
                                <img src="/storage/Logos/equipocontainer.png" alt="Logo" class="h-10 w-auto bg-white rounded p-1">
                            </Link>
                        </div>
                        
                        <!-- Search Bar (Desktop) -->
                        <div class="hidden md:flex flex-[2] justify-center px-4">
                            <form @submit.prevent="globalSearch" class="relative w-full max-w-2xl">
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Buscar..."
                                    class="w-full pl-4 pr-10 py-2.5 bg-white text-gray-900 rounded-sm border-none focus:ring-0 text-sm"
                                >
                                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-black">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <circle cx="11" cy="11" r="8" stroke-width="2" />
                                        <path stroke-linecap="round" stroke-width="2" d="M21 21l-4.35-4.35" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <!-- Actions -->
                        <div class="flex-1 flex items-center justify-end space-x-6 text-sm">
                            <!-- User -->
                            <div class="relative group hidden sm:block h-full">
                                <Link :href="user ? route('account.dashboard') : route('login')" class="flex items-center gap-2 hover:text-gray-300 transition py-6">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="font-medium hidden lg:block">{{ user ? 'Mi cuenta' : 'Acceso' }}</span>
                                </Link>
                                <!-- Dropdown if logged in -->
                                <div v-if="user" class="absolute right-0 top-full -mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-100 py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all">
                                    <Link :href="route('account.dashboard')" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Mi Cuenta</Link>
                                    <Link href="/mis-pedidos" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">Mis Pedidos</Link>
                                    <Link :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-50">Cerrar Sesión</Link>
                                </div>
                            </div>

                            <!-- Favoritos -->
                            <Link href="/favoritos" class="flex items-center gap-2 hover:text-gray-300 transition hidden sm:flex">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span class="font-medium hidden lg:block">Favoritos</span>
                            </Link>

                            <!-- Cart -->
                            <button @click="isCartOpen = true" class="relative flex items-center gap-2 hover:text-gray-300 transition">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span v-if="itemsCount > 0" class="absolute -top-1.5 -right-2 w-5 h-5 flex items-center justify-center bg-[#F41D27] text-white text-[10px] font-bold rounded-full">
                                    {{ itemsCount }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Search Bar -->
            <div class="bg-[#040054] p-3 md:hidden border-t border-[#040054]">
                <form @submit.prevent="globalSearch" class="relative w-full">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar..."
                        class="w-full pl-4 pr-10 py-2 bg-white text-gray-900 rounded-sm border-none focus:ring-0 text-sm"
                    >
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" stroke-width="2" />
                            <path stroke-linecap="round" stroke-width="2" d="M21 21l-4.35-4.35" />
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Bottom White Bar for Categories -->
            <div class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <ul class="flex items-center justify-center gap-8 md:gap-12 overflow-x-auto scrollbar-hide py-3 text-sm font-bold text-gray-800">
                        <li>
                            <Link href="/" class="hover:text-[#F41D27] whitespace-nowrap transition-colors">Inicio</Link>
                        </li>
                        <li v-for="cat in categories" :key="cat.id">
                            <Link :href="`/categories/${cat.slug}`" class="hover:text-[#F41D27] whitespace-nowrap transition-colors">
                                {{ cat.name }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Cart Sidebar -->
        <CartSidebar 
            :is-open="isCartOpen" 
            @close="isCartOpen = false"
        />

        <!-- Footer -->
        <footer class="bg-[#040054] text-white mt-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- About -->
                    <div>
                        <h3 class="text-lg font-bold mb-4">Sobre Nosotros</h3>
                        <p class="text-gray-300 text-sm">Tu tienda de confianza para productos de calidad premium con los mejores precios del mercado.</p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-lg font-bold mb-4">Enlaces Rápidos</h3>
                        <ul class="space-y-2 text-sm">
                            <li><Link href="/" class="text-gray-300 hover:text-white transition">Inicio</Link></li>
                            <li><Link href="/products" class="text-gray-300 hover:text-white transition">Catálogo</Link></li>
                            <li><Link href="/categories" class="text-gray-300 hover:text-white transition">Categorías</Link></li>
                            <li><a href="#contacto" class="text-gray-300 hover:text-white transition">Contacto</a></li>
                        </ul>
                    </div>

                    <!-- About Client -->
                    <div>
                        <h3 class="text-lg font-bold mb-4">Atención al Cliente</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Preguntas Frecuentes</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Políticas de Envío</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Devoluciones</a></li>
                            <li><a href="#" class="text-gray-300 hover:text-white transition">Términos y Condiciones</a></li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h3 class="text-lg font-bold mb-4">Contacto</h3>
                        <ul class="space-y-2 text-sm text-gray-300">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                +58 424-1234567
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                info@equipocontainer.com
                            </li>
                            <li class="flex items-start">
                                <svg class="w-4 h-4 mr-2 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Caracas, Venezuela
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-700 text-center text-sm text-gray-400">
                    <p>&copy; {{ new Date().getFullYear() }} EquipoContainer. Todos los derechos reservados.</p>
                    <p>Paginas web desarrollada por: <a href="https://www.alfredoromero.io" target="_blank"><span class="text-blue-500">alfredoromero.io</span></a></p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Ticker Animation */
.ticker-wrapper {
    overflow: hidden;
    white-space: nowrap;
}

.ticker-content {
    display: inline-block;
    animation: ticker 30s linear infinite;
}

.ticker-item {
    display: inline-block;
    padding: 0 3rem;
    font-weight: 600;
    font-size: 0.875rem;
}

@keyframes ticker {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}
</style>
