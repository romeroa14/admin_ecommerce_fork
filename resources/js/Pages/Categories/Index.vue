<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

// @ts-ignore
const route = window.route;

defineProps({
    categories: Array,
});
</script>

<template>
    <AppLayout>
        <Head title="Categorías" />

        <div class="bg-gray-50 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-12">
                    <h1 class="text-3xl md:text-5xl font-extrabold text-[#040054] mb-4">
                        Nuestras Categorías
                    </h1>
                    <div class="w-24 h-1 bg-[#F41D27] mx-auto mb-6"></div>
                    <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                        Explora nuestra amplia selección de productos organizados por categorías
                    </p>
                </div>

                <!-- Categories Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <Link 
                        v-for="category in categories" 
                        :key="category.id"
                        :href="`/categories/${category.slug}`"
                        class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col p-8 text-center"
                    >
                        <!-- Icon -->
                        <div class="mb-4">
                            <span v-if="category.icon" class="text-6xl">{{ category.icon }}</span>
                            <div v-else class="w-16 h-16 bg-[#040054] rounded-full flex items-center justify-center mx-auto">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Name -->
                        <h3 class="text-xl font-bold text-gray-900 group-hover:text-[#F41D27] transition mb-2">
                            {{ category.name }}
                        </h3>

                        <!-- Product Count -->
                        <p v-if="category.products_count" class="text-sm text-gray-500 mb-4">
                            {{ category.products_count }} {{ category.products_count === 1 ? 'producto' : 'productos' }}
                        </p>

                        <!-- Description -->
                        <p v-if="category.description" class="text-sm text-gray-600 line-clamp-2 flex-1">
                            {{ category.description }}
                        </p>

                        <!-- View Button -->
                        <div class="mt-6">
                            <span class="inline-flex items-center text-sm font-medium text-[#040054] group-hover:text-[#F41D27] transition">
                                Ver productos
                                <svg class="ml-2 w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </Link>
                </div>

                <!-- CTA Section -->
                <div class="mt-16 text-center bg-gradient-to-r from-[#040054] to-[#F41D27] rounded-2xl p-12 text-white">
                    <h2 class="text-3xl font-bold mb-4">¿No encuentras lo que buscas?</h2>
                    <p class="text-lg mb-6 opacity-90">Explora todo nuestro catálogo de productos</p>
                    <Link 
                        href="/products"
                        class="inline-flex items-center px-8 py-4 bg-white text-[#040054] font-bold rounded-lg hover:bg-gray-100 transition-all transform hover:scale-105 shadow-lg"
                    >
                        Ver Todo el Catálogo
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
