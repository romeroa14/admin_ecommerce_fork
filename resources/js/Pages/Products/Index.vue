<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    products: Object,
});
</script>

<template>
    <AppLayout>
        <Head title="Nuestros Productos" />

        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900">Catálogo de Productos</h1>
            <p class="mt-4 text-xl text-gray-500">Encuentra lo que buscas al mejor precio.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <Link 
                v-for="product in products.data" 
                :key="product.id" 
                :href="route('products.show', product.slug)"
                class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col"
            >
                <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-200">
                    <img 
                        :src="product.images?.[0] || 'https://via.placeholder.com/400'" 
                        :alt="product.name"
                        class="h-full w-full object-cover object-center group-hover:opacity-90 transform group-hover:scale-105 transition duration-500"
                    >
                </div>
                <div class="p-4 flex-1 flex flex-col">
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition">
                        {{ product.name }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 line-clamp-2">{{ product.short_description }}</p>
                    <div class="mt-auto pt-4 flex items-center justify-between">
                        <span class="text-xl font-bold text-gray-900">€{{ product.price }}</span>
                        <span v-if="product.stock > 0" class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-1 rounded-full">En Stock</span>
                        <span v-else class="text-xs font-semibold text-red-600 bg-red-100 px-2 py-1 rounded-full">Agotado</span>
                    </div>
                </div>
            </Link>
        </div>
        
        <!-- Pagination would go here -->
    </AppLayout>
</template>
