<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import { ref, computed, watch } from 'vue';

// @ts-ignore
const route = window.route;

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
    currentCategory: Object, // When viewing a specific category
});

// Modal state
const showFiltersModal = ref(false);

// Filter state
const selectedStock = ref(props.filters?.stock || '');
const selectedCategory = ref(props.filters?.category || '');
const minPrice = ref(props.filters?.min_price || 0);
const maxPrice = ref(props.filters?.max_price || 10000);
const sortBy = ref(props.filters?.sort || 'latest');

// Watch and fix price range bugs
watch(minPrice, (newMin) => {
    if (newMin > maxPrice.value) {
        maxPrice.value = newMin;
    }
    if (newMin < 0) {
        minPrice.value = 0;
    }
});

watch(maxPrice, (newMax) => {
    if (newMax < minPrice.value) {
        minPrice.value = newMax;
    }
    if (newMax > 10000) {
        maxPrice.value = 10000;
    }
});

// Apply filters
const applyFilters = () => {
    router.get('/products', {
        stock: selectedStock.value,
        category: selectedCategory.value,
        min_price: minPrice.value,
        max_price: maxPrice.value,
        sort: sortBy.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
    showFiltersModal.value = false;
};

// Clear filters
const clearFilters = () => {
    selectedStock.value = '';
    selectedCategory.value = '';
    minPrice.value = 0;
    maxPrice.value = 10000;
    sortBy.value = 'latest';
    router.get('/products');
    showFiltersModal.value = false;
};

// Check if any filter is active
const hasActiveFilters = computed(() => {
    return selectedStock.value || selectedCategory.value || minPrice.value > 0 || maxPrice.value < 10000 || sortBy.value !== 'latest';
});

const activeFiltersCount = computed(() => {
    let count = 0;
    if (selectedStock.value) count++;
    if (selectedCategory.value) count++;
    if (minPrice.value > 0 || maxPrice.value < 10000) count++;
    if (sortBy.value !== 'latest') count++;
    return count;
});

// Price range percentage for visual
const minPercentage = computed(() => (minPrice.value / 10000) * 100);
const maxPercentage = computed(() => (maxPrice.value / 10000) * 100);
</script>

<template>
    <AppLayout>
        <Head title="Catálogo de Productos" />

        <div class="bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumbs -->
                <Breadcrumbs 
                    v-if="currentCategory"
                    :items="[
                        { label: 'Categorías', href: '/categories' },
                        { label: currentCategory.name }
                    ]"
                />
                
                <!-- Header -->
                <div class="text-center mb-8">
                    <div v-if="currentCategory" class="flex items-center justify-center space-x-3 mb-4">
                        <span v-if="currentCategory.icon" class="text-5xl">{{ currentCategory.icon }}</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-[#040054]">
                        {{ currentCategory ? currentCategory.name : 'Catálogo de Productos' }}
                    </h1>
                    <p class="mt-2 text-lg text-gray-600">
                        {{ currentCategory ? currentCategory.description || `Todos los productos de ${currentCategory.name}` : 'Encuentra lo que buscas al mejor precio' }}
                    </p>
                </div>

                <!-- Filter Button and Results -->
                <div class="flex items-center justify-between mb-6 relative">
                    <div class="flex items-center space-x-4">
                        <button 
                            @click="showFiltersModal = !showFiltersModal"
                            class="inline-flex items-center px-6 py-3 bg-white border-2 border-[#040054] text-[#040054] font-semibold rounded-lg hover:bg-[#040054] hover:text-white transition-all shadow-md"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Filtros
                            <span v-if="activeFiltersCount > 0" class="ml-2 bg-[#F41D27] text-white text-xs font-bold px-2 py-1 rounded-full">
                                {{ activeFiltersCount }}
                            </span>
                        </button>
                        <button 
                            v-if="hasActiveFilters"
                            @click="clearFilters"
                            class="text-sm text-[#F41D27] hover:underline font-medium"
                        >
                            Limpiar filtros
                        </button>
                    </div>
                    <p class="text-sm text-gray-600">
                        Mostrando <span class="font-semibold text-[#040054]">{{ products.data.length }}</span> de <span class="font-semibold text-[#040054]">{{ products.total }}</span> productos
                    </p>

                    <!-- Floating Filters Panel -->
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <div 
                            v-if="showFiltersModal"
                            class="absolute top-full left-0 mt-2 w-full max-w-2xl bg-white rounded-xl shadow-2xl border-2 border-[#040054] p-6 z-50"
                        >
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                                <h3 class="text-xl font-bold text-[#040054]">Filtros</h3>
                                <button 
                                    @click="showFiltersModal = false"
                                    class="text-gray-400 hover:text-gray-600 transition rounded-full p-2 hover:bg-gray-100"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Filters Content -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Stock Filter -->
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-3">Disponibilidad</h4>
                                    <div class="space-y-2">
                                        <label class="flex items-center cursor-pointer group">
                                            <input 
                                                type="radio" 
                                                v-model="selectedStock" 
                                                value="" 
                                                class="text-[#040054] focus:ring-[#040054] w-4 h-4"
                                            >
                                            <span class="ml-3 text-sm text-gray-700 group-hover:text-[#040054]">Todos</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer group">
                                            <input 
                                                type="radio" 
                                                v-model="selectedStock" 
                                                value="available" 
                                                class="text-[#040054] focus:ring-[#040054] w-4 h-4"
                                            >
                                            <span class="ml-3 text-sm text-gray-700 group-hover:text-[#040054]">En Stock</span>
                                        </label>
                                        <label class="flex items-center cursor-pointer group">
                                            <input 
                                                type="radio" 
                                                v-model="selectedStock" 
                                                value="out_of_stock" 
                                                class="text-[#040054] focus:ring-[#040054] w-4 h-4"
                                            >
                                            <span class="ml-3 text-sm text-gray-700 group-hover:text-[#040054]">Agotado</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Category Filter -->
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-3">Categorías</h4>
                                    <select 
                                        v-model="selectedCategory" 
                                        class="w-full border-gray-300 rounded-lg text-sm focus:ring-[#040054] focus:border-[#040054]"
                                    >
                                        <option value="">Todas las categorías</option>
                                        <option v-for="category in categories" :key="category.id" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Price Range Filter - Full Width -->
                            <div class="mb-6">
                                <h4 class="font-bold text-gray-900 mb-4">Precio</h4>
                                
                                <!-- Price Inputs -->
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-600 mb-1 block">Mínimo</label>
                                        <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2 bg-white">
                                            <span class="text-gray-500 text-sm mr-1">$</span>
                                            <input 
                                                type="number" 
                                                v-model.number="minPrice" 
                                                min="0"
                                                max="10000"
                                                step="10"
                                                class="flex-1 bg-transparent border-none text-sm focus:ring-0 p-0"
                                            >
                                        </div>
                                    </div>
                                    <span class="text-gray-400 mt-5">a</span>
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-600 mb-1 block">Máximo</label>
                                        <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2 bg-white">
                                            <span class="text-gray-500 text-sm mr-1">$</span>
                                            <input 
                                                type="number" 
                                                v-model.number="maxPrice" 
                                                min="0"
                                                max="10000"
                                                step="10"
                                                class="flex-1 bg-transparent border-none text-sm focus:ring-0 p-0"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Custom Range Slider -->
                                <div class="relative px-2 h-10">
                                    <div class="absolute top-3 w-full h-2 bg-gray-200 rounded-full">
                                        <!-- Active range bar -->
                                        <div 
                                            class="absolute h-2 bg-[#040054] rounded-full"
                                            :style="{
                                                left: minPercentage + '%',
                                                right: (100 - maxPercentage) + '%'
                                            }"
                                        ></div>
                                    </div>
                                    
                                    <!-- Min slider -->
                                    <input 
                                        type="range"
                                        v-model.number="minPrice"
                                        min="0"
                                        max="10000"
                                        step="100"
                                        class="absolute w-full range-slider"
                                    >
                                    
                                    <!-- Max slider -->
                                    <input 
                                        type="range"
                                        v-model.number="maxPrice"
                                        min="0"
                                        max="10000"
                                        step="100"
                                        class="absolute w-full range-slider"
                                    >
                                </div>
                            </div>

                            <!-- Sort -->
                            <div class="mb-6">
                                <h4 class="font-bold text-gray-900 mb-3">Ordenar por</h4>
                                <select 
                                    v-model="sortBy" 
                                    class="w-full border-gray-300 rounded-lg text-sm focus:ring-[#040054] focus:border-[#040054]"
                                >
                                    <option value="latest">Más recientes</option>
                                    <option value="price_asc">Precio: Menor a Mayor</option>
                                    <option value="price_desc">Precio: Mayor a Menor</option>
                                    <option value="name">Nombre (A-Z)</option>
                                </select>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-3 pt-4 border-t border-gray-200">
                                <button 
                                    @click="clearFilters"
                                    class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition"
                                >
                                    Limpiar
                                </button>
                                <button 
                                    @click="applyFilters"
                                    class="flex-1 px-6 py-3 bg-[#040054] text-white font-semibold rounded-lg hover:bg-[#060078] transition shadow-lg"
                                >
                                    Aplicar Filtros
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>

                <!-- Products Grid - 4 columns -->
                <div v-if="products.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <Link 
                        v-for="product in products.data" 
                        :key="product.id" 
                        :href="route('products.show', product.slug)"
                        class="group bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col"
                    >
                        <div class="relative overflow-hidden bg-gray-100">
                            <img 
                                :src="product.images?.[0] || 'https://via.placeholder.com/400'" 
                                :alt="product.name"
                                class="h-64 w-full object-cover object-center group-hover:scale-110 transition-transform duration-500"
                            >
                            <div v-if="product.discount_percentage > 0" class="absolute top-3 right-3 bg-[#F41D27] text-white px-3 py-1 rounded-full font-bold text-xs shadow-lg">
                                -{{ product.discount_percentage }}%
                            </div>
                        </div>
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="mb-2">
                                <span v-if="product.category" class="text-xs font-medium text-[#040054] bg-blue-50 px-2 py-1 rounded">
                                    {{ product.category.name }}
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-gray-900 group-hover:text-[#F41D27] transition line-clamp-2 mb-2">
                                {{ product.name }}
                            </h3>
                            <p class="text-sm text-gray-500 line-clamp-2 mb-4 flex-1">{{ product.short_description }}</p>
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div>
                                    <span class="text-xl font-bold text-[#040054]">€{{ product.price }}</span>
                                    <span v-if="product.compare_price > product.price" class="block text-xs text-gray-400 line-through">
                                        €{{ product.compare_price }}
                                    </span>
                                </div>
                                <span v-if="product.stock > 0" class="text-xs font-semibold text-green-600 bg-green-100 px-3 py-1 rounded-full">
                                    En Stock
                                </span>
                                <span v-else class="text-xs font-semibold text-red-600 bg-red-100 px-3 py-1 rounded-full">
                                    Agotado
                                </span>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- No results -->
                <div v-else class="text-center py-16">
                    <svg class="mx-auto h-24 w-24 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No se encontraron productos</h3>
                    <p class="mt-2 text-sm text-gray-500">Intenta ajustar tus filtros</p>
                    <button 
                        @click="clearFilters"
                        class="mt-6 inline-flex items-center px-4 py-2 bg-[#F41D27] text-white rounded-lg hover:bg-red-600 transition"
                    >
                        Limpiar Filtros
                    </button>
                </div>

                <!-- Pagination -->
                <div v-if="products.data.length > 0 && (products.prev_page_url || products.next_page_url)" class="mt-8 flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <Link 
                            v-if="products.prev_page_url"
                            :href="products.prev_page_url"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
                        >
                            Anterior
                        </Link>
                        <span class="px-4 py-2 text-sm text-gray-700">
                            Página {{ products.current_page }} de {{ products.last_page }}
                        </span>
                        <Link 
                            v-if="products.next_page_url"
                            :href="products.next_page_url"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
                        >
                            Siguiente
                        </Link>
                    </nav>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
/* Custom range slider styles */
.range-slider {
    -webkit-appearance: none;
    appearance: none;
    background: transparent;
    pointer-events: none;
    height: 40px;
}

.range-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #040054;
    border: 3px solid white;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    pointer-events: all;
    position: relative;
    z-index: 10;
}

.range-slider::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: #040054;
    border: 3px solid white;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    pointer-events: all;
    position: relative;
    z-index: 10;
}

.range-slider::-webkit-slider-runnable-track {
    background: transparent;
}

.range-slider::-moz-range-track {
    background: transparent;
}
</style>
