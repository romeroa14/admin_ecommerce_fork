<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// @ts-ignore
const route = window.route;

const page = usePage();
const user = computed(() => page.props.auth?.user);
const itemsCount = computed(() => {
    // @ts-ignore
    return page.props.items ? page.props.items.length : 0;
});

const announcements = [
    '💎 Descuentos Únicos',
    '📦 Paga al recibir en toda Caracas',
    '🚀 Despacho gratis a todo el país',
    '⭐ Productos de Calidad Premium',
    '🔒 Compra 100% Segura'
];
</script>

<template>
    <div class="min-h-screen bg-white text-gray-900">
        <!-- Infinite Ticker / Announcement Bar -->
        <div class="bg-gradient-to-r from-[#F41D27] to-[#040054] text-white py-2 overflow-hidden">
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
        <header class="bg-white shadow-md sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link href="/" class="flex items-center">
                            <img src="/storage/Logos/equipocontainer.png" alt="Logo" class="h-16 w-auto">
                        </Link>
                    </div>
                    
                    <!-- Navigation -->
                    <nav class="hidden md:flex space-x-8">
                        <Link href="/" class="text-gray-700 hover:text-[#F41D27] font-medium transition-colors">
                            Inicio
                        </Link>
                        <Link href="/products" class="text-gray-700 hover:text-[#F41D27] font-medium transition-colors">
                            Catálogo
                        </Link>
                        <Link href="/categories" class="text-gray-700 hover:text-[#F41D27] font-medium transition-colors">
                            Categorías
                        </Link>
                        <a href="#contacto" class="text-gray-700 hover:text-[#F41D27] font-medium transition-colors">
                            Contacto
                        </a>
                    </nav>

                    <!-- Actions -->
                    <div class="flex items-center space-x-4">
                        <!-- User Account -->
                        <div v-if="user" class="hidden md:flex items-center space-x-2 relative group">
                            <Link :href="route('account.dashboard')" class="flex items-center space-x-2 text-gray-700 hover:text-[#F41D27] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="text-sm font-medium">{{ user.name }}</span>
                            </Link>
                            <!-- Dropdown Menu -->
                            <div class="absolute top-full right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                                <Link :href="route('account.dashboard')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Mi Cuenta
                                </Link>
                                <Link href="/mis-pedidos" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Mis Pedidos
                                </Link>
                                <hr class="my-2 border-gray-200">
                                <Link :href="route('logout')" method="post" as="button" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    Cerrar Sesión
                                </Link>
                            </div>
                        </div>
                        <Link v-else :href="route('login')" class="hidden md:flex items-center space-x-1 text-gray-700 hover:text-[#F41D27] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-sm font-medium">Acceso</span>
                        </Link>

                        <!-- Search -->
                        <button class="p-2 text-gray-600 hover:text-[#F41D27] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>

                        <!-- Cart -->
                        <Link :href="route('cart.index')" class="relative p-2 text-gray-600 hover:text-[#F41D27] transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span v-if="itemsCount > 0" class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-[#F41D27] rounded-full">
                                {{ itemsCount }}
                            </span>
                        </Link>

                        <!-- Mobile Menu Button -->
                        <button class="md:hidden p-2 text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Banner (Optional - for home page) -->
        <slot name="hero" />

        <!-- About Section (Optional - for home page) -->
        <slot name="about" />

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <footer class="bg-[#040054] text-white mt-16 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">Sobre Nosotros</h3>
                    <p class="text-gray-300 text-sm">Ofrecemos los mejores productos con la mejor calidad y precio del mercado.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Enlaces Rápidos</h3>
                    <div class="flex flex-col space-y-2 text-sm">
                        <Link href="/products" class="text-gray-300 hover:text-[#F41D27] transition">Productos</Link>
                        <Link href="/categories" class="text-gray-300 hover:text-[#F41D27] transition">Categorías</Link>
                        <Link href="/cart" class="text-gray-300 hover:text-[#F41D27] transition">Carrito</Link>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Atención al Cliente</h3>
                    <div class="flex flex-col space-y-2 text-sm text-gray-300">
                        <p>Lun - Vie: 9:00 AM - 6:00 PM</p>
                        <p>Sáb: 10:00 AM - 2:00 PM</p>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Contacto</h3>
                    <p class="text-gray-300 text-sm">info@equipocontainer.com</p>
                    <p class="text-gray-300 text-sm mt-2">+58 412 123 4567</p>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-8 border-t border-gray-700 text-center text-gray-400 text-sm">
                <p>&copy; 2026 Equipo Container. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.ticker-wrapper {
    width: 100%;
    overflow: hidden;
    position: relative;
}

.ticker-content {
    display: flex;
    animation: ticker 30s linear infinite;
    white-space: nowrap;
}

.ticker-item {
    padding: 0 3rem;
    font-weight: 600;
    font-size: 0.875rem;
    letter-spacing: 0.05em;
}

@keyframes ticker {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
}

/* Pause animation on hover */
.ticker-wrapper:hover .ticker-content {
    animation-play-state: paused;
}
</style>
