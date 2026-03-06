<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import BannerCarousel from '@/Components/BannerCarousel.vue';
import { getProductImage } from '@/composables/useProductImage';

// @ts-ignore
const route = window.route;

const props = defineProps({
    products: Object,
    banners: {
        type: Array as () => any[],
        default: () => [],
    },
    categories: {
        type: Array as () => any[],
        default: () => [],
    },
});

// Search
const searchQuery = ref('');
const filteredProducts = computed(() => {
    if (!props.products?.data) return [];
    if (!searchQuery.value.trim()) return props.products.data;
    const q = searchQuery.value.toLowerCase().trim();
    return props.products.data.filter((p: any) =>
        p.name?.toLowerCase().includes(q) ||
        p.short_description?.toLowerCase().includes(q) ||
        p.category?.name?.toLowerCase().includes(q)
    );
});

function goToSearch() {
    if (searchQuery.value.trim()) {
        router.get('/products', { search: searchQuery.value.trim() });
    }
}
</script>

<template>
    <AppLayout>
        <Head title="Inicio" />

        <!-- =================== 1. BANNER CAROUSEL =================== -->
        <section class="w-full">
            <BannerCarousel
                v-if="banners && banners.length > 0"
                :banners="banners"
                :autoplay-interval="5000"
            />
        </section>

        <!-- =================== 2. CATEGORIES =================== -->
        <section v-if="categories && categories.length > 0" class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex items-center gap-6 overflow-x-auto scrollbar-hide py-1">
                    <Link
                        v-for="cat in categories"
                        :key="cat.id"
                        :href="`/categories/${cat.slug}`"
                        class="group flex flex-col items-center gap-2 min-w-[80px] flex-shrink-0"
                    >
                        <div class="w-16 h-16 bg-gradient-to-br from-[#040054] to-[#1a0a7e] rounded-2xl flex items-center justify-center text-2xl shadow-md group-hover:shadow-lg group-hover:scale-110 transition-all duration-300">
                            {{ cat.icon || '📦' }}
                        </div>
                        <span class="text-xs font-semibold text-gray-700 group-hover:text-[#F41D27] transition text-center leading-tight whitespace-nowrap">
                            {{ cat.name }}
                        </span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- =================== 3. SEARCH + PRODUCTS =================== -->
        <section class="bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Header: Title + Search -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl md:text-2xl font-extrabold text-[#040054]">
                            Productos
                        </h2>
                        <Link
                            href="/products"
                            class="text-sm font-semibold text-[#040054]/60 hover:text-[#F41D27] transition group flex items-center gap-1"
                        >
                            Ver todos
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>

                    <!-- Search Input -->
                    <form @submit.prevent="goToSearch" class="relative w-full sm:w-80">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Buscar productos..."
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#040054]/20 focus:border-[#040054] transition shadow-sm"
                        >
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" stroke-width="2" />
                            <path stroke-linecap="round" stroke-width="2" d="M21 21l-4.35-4.35" />
                        </svg>
                    </form>
                </div>

                <!-- Products Grid -->
                <div v-if="filteredProducts.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-4">
                    <Link
                        v-for="product in filteredProducts"
                        :key="product.id"
                        :href="route('products.show', product.slug)"
                        class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col"
                    >
                        <!-- Image -->
                        <div class="relative aspect-square overflow-hidden bg-gray-50">
                            <img
                                :src="getProductImage(product)"
                                :alt="product.name"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="lazy"
                            >
                            <div
                                v-if="product.discount_percentage > 0"
                                class="absolute top-2 left-2 bg-[#F41D27] text-white px-2 py-0.5 rounded-md font-bold text-[11px] shadow"
                            >
                                -{{ product.discount_percentage }}%
                            </div>
                            <!-- Hover overlay -->
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300 flex items-end justify-center pb-3 opacity-0 group-hover:opacity-100">
                                <span class="bg-white/95 backdrop-blur-sm text-[#040054] text-xs font-bold px-4 py-1.5 rounded-full shadow transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                                    Ver Producto
                                </span>
                            </div>
                        </div>
                        <!-- Content -->
                        <div class="p-3 flex-1 flex flex-col">
                            <span v-if="product.category" class="text-[10px] text-gray-400 font-medium uppercase tracking-wider mb-0.5">
                                {{ product.category.name }}
                            </span>
                            <h3 class="text-sm font-semibold text-gray-800 group-hover:text-[#F41D27] transition line-clamp-2 mb-2 flex-1 leading-snug">
                                {{ product.name }}
                            </h3>
                            <div class="flex items-center justify-between">
                                <span class="text-base md:text-lg font-extrabold text-[#040054]">€{{ product.price }}</span>
                                <span
                                    v-if="product.stock > 0"
                                    class="text-[9px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded"
                                >En Stock</span>
                                <span v-else class="text-[9px] font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded">Agotado</span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- No results -->
                <div v-else-if="searchQuery.trim()" class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8" stroke-width="1.5" />
                        <path stroke-linecap="round" stroke-width="1.5" d="M21 21l-4.35-4.35" />
                    </svg>
                    <p class="text-gray-500 text-lg font-medium">No se encontraron productos para "{{ searchQuery }}"</p>
                    <button @click="searchQuery = ''" class="mt-3 text-sm text-[#F41D27] font-semibold hover:underline">Limpiar búsqueda</button>
                </div>

                <!-- View All -->
                <div class="text-center mt-8">
                    <Link
                        href="/products"
                        class="inline-flex items-center px-6 py-3 bg-[#040054] text-white font-bold rounded-xl hover:bg-[#060078] transition-all transform hover:scale-[1.02] shadow-lg text-sm"
                    >
                        Ver Catálogo Completo
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
