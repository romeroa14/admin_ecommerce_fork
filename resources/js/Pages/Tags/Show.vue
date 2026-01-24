<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    tag: Object,
    products: Object,
});
</script>

<template>
    <AppLayout>
        <Head :title="tag.name" />

        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900">Etiqueta: {{ tag.name }}</h1>
            <p class="mt-4 text-xl text-gray-500">Productos etiquetados con "{{ tag.name }}"</p>
        </div>

        <div v-if="products.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
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
                    </div>
                </div>
            </Link>
        </div>
        <div v-else class="text-center py-20 text-gray-500">
            No hay productos con esta etiqueta.
        </div>
        
    </AppLayout>
</template>
