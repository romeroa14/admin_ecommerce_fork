<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import CartSidebar from '@/Components/CartSidebar.vue';

// @ts-ignore
const route = window.route;

const page = usePage();
const user = computed(() => page.props.auth?.user);
// @ts-ignore
const categories = computed(() => page.props.categories || []);
const itemsCount = computed(() => {
    // @ts-ignore
    return page.props.items ? page.props.items.length : 0;
});

// Cart sidebar state
const isCartOpen = ref(false);

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
                    <nav class="hidden md:flex space-x-8 items-center">
                        <Link href="/" class="text-gray-700 hover:text-[#F41D27] font-medium transition-colors">
                            Inicio
                        </Link>
                        
                        <!-- Categories Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-1 text-gray-700 hover:text-[#F41D27] font-medium transition-colors">
                                <span>Categorías</span>
                                <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div class="absolute left-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-gray-200 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all z-50">
                                <Link 
                                    v-for="category in categories" 
                                    :key="category.id"
                                    :href="`/categories/${category.slug}`"
                                    class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#F41D27] transition"
                                >
                                    <span v-if="category.icon" class="mr-3 text-lg">{{ category.icon }}</span>
                                    <span class="font-medium">{{ category.name }}</span>
                                </Link>
                                
                                <hr class="my-2 border-gray-200">
                                
                                <Link href="/categories" class="flex items-center px-4 py-3 text-sm text-[#040054] font-semibold hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                    Ver todas las categorías
                                </Link>
                            </div>
                        </div>
                        
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

                        <!-- Search Icon -->
                        <button class="hidden md:block p-2 text-gray-600 hover:text-[#F41D27] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>

                        <!-- Cart Button -->
                        <button 
                            @click="isCartOpen = true"
                            class="relative p-2 text-gray-600 hover:text-[#F41D27] transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span v-if="itemsCount > 0" class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-[#F41D27] rounded-full animate-pulse">
                                {{ itemsCount }}
                            </span>
                        </button>

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
