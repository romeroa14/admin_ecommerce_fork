<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

// @ts-ignore
const route = window.route;

defineProps({
    products: Object,
});
</script>

<template>
    <AppLayout>
        <Head title="Inicio" />

        <!-- Hero Banner -->
        <section class="relative bg-gradient-to-br from-[#040054] via-[#1a0a7e] to-[#F41D27] text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-32">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                    <div class="space-y-6">
                        <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">
                            Equipos de Calidad para tu Negocio
                        </h1>
                        <p class="text-lg md:text-xl text-gray-200">
                            Encuentra los mejores productos para equipar tu negocio con la mejor relación calidad-precio del mercado.
                        </p>
                        <div class="flex flex-wrap gap-4">
                            <Link 
                                href="/products" 
                                class="inline-flex items-center px-8 py-4 bg-[#F41D27] text-white font-bold rounded-lg hover:bg-red-600 transition-all transform hover:scale-105 shadow-lg"
                            >
                                Ver Catálogo
                                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </Link>
                            <a 
                                href="#about" 
                                class="inline-flex items-center px-8 py-4 bg-white text-[#040054] font-bold rounded-lg hover:bg-gray-100 transition-all shadow-lg"
                            >
                                Conocer Más
                            </a>
                        </div>
                    </div>
                    <div class="relative hidden md:block">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#F41D27]/20 to-transparent rounded-3xl blur-3xl"></div>
                        <img 
                            src="/storage/Logos/equipocontainer.png" 
                            alt="Hero" 
                            class="relative z-10 drop-shadow-2xl"
                        >
                    </div>
                </div>
            </div>
            <!-- Wave SVG -->
            <div class="absolute bottom-0 left-0 right-0">
                <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="white"/>
                </svg>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#040054] mb-4">
                        ¿Por Qué Elegirnos?
                    </h2>
                    <div class="w-24 h-1 bg-[#F41D27] mx-auto mb-6"></div>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Somos tu aliado de confianza en la adquisición de equipos y productos de calidad premium
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-[#F41D27] rounded-full flex items-center justify-center mb-6 mx-auto">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#040054] text-center mb-3">Calidad Garantizada</h3>
                        <p class="text-gray-600 text-center">
                            Todos nuestros productos pasan rigurosos controles de calidad para garantizar tu satisfacción.
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-[#040054] rounded-full flex items-center justify-center mb-6 mx-auto">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#040054] text-center mb-3">Mejores Precios</h3>
                        <p class="text-gray-600 text-center">
                            Ofrecemos los precios más competitivos del mercado sin comprometer la calidad.
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-2">
                        <div class="w-16 h-16 bg-[#F41D27] rounded-full flex items-center justify-center mb-6 mx-auto">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-[#040054] text-center mb-3">Entrega Rápida</h3>
                        <p class="text-gray-600 text-center">
                            Envíos a todo el país con las mejores empresas de logística. ¡Recibe tu pedido rápidamente!
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Products Section -->
        <div class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-5xl font-extrabold text-[#040054] mb-4">
                        Nuestros Productos Destacados
                    </h2>
                    <div class="w-24 h-1 bg-[#F41D27] mx-auto"></div>
                </div>

                <div v-if="products && products.data" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <Link 
                        v-for="product in products.data.slice(0, 8)" 
                        :key="product.id" 
                        :href="route('products.show', product.slug)"
                        class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col"
                    >
                        <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-200 relative">
                            <img 
                                :src="product.images?.[0] || 'https://via.placeholder.com/400'" 
                                :alt="product.name"
                                class="h-64 w-full object-cover object-center group-hover:scale-110 transition-transform duration-500"
                            >
                            <div v-if="product.discount_percentage > 0" class="absolute top-4 right-4 bg-[#F41D27] text-white px-3 py-1 rounded-full font-bold text-sm shadow-lg">
                                -{{ product.discount_percentage }}%
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col">
                            <!-- Category Badge -->
                            <div v-if="product.category" class="mb-3">
                                <Link 
                                    :href="`/categories/${product.category.slug}`"
                                    @click.stop
                                    class="inline-flex items-center text-xs font-medium text-[#040054] bg-blue-50 px-3 py-1 rounded-full hover:bg-blue-100 transition"
                                >
                                    <span v-if="product.category.icon" class="mr-1">{{ product.category.icon }}</span>
                                    {{ product.category.name }}
                                </Link>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-[#F41D27] transition line-clamp-2 mb-2">
                                {{ product.name }}
                            </h3>
                            <p class="text-sm text-gray-500 line-clamp-2 mb-4 flex-1">{{ product.short_description }}</p>
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <span class="text-2xl font-bold text-[#040054]">€{{ product.price }}</span>
                                <span v-if="product.stock > 0" class="text-xs font-semibold text-green-600 bg-green-100 px-3 py-1 rounded-full">En Stock</span>
                                <span v-else class="text-xs font-semibold text-red-600 bg-red-100 px-3 py-1 rounded-full">Agotado</span>
                            </div>
                        </div>
                    </Link>
                </div>

                <div class="text-center mt-12">
                    <Link 
                        href="/products" 
                        class="inline-flex items-center px-8 py-4 bg-[#040054] text-white font-bold rounded-lg hover:bg-[#060078] transition-all transform hover:scale-105 shadow-lg"
                    >
                        Ver Todos los Productos
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
