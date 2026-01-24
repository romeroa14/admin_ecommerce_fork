<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

// Helper to avoid TS errors with global route
// @ts-ignore
const route = window.route;

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    isInStock: Boolean,
});

const form = useForm({
    product_id: props.product.id,
    quantity: 1,
    variants: {} as Record<string, any>,
});

const selectedImage = ref(props.product.images?.[0] || 'https://via.placeholder.com/600');
const currentQuantity = ref(1);

const discount = computed(() => {
    if (!props.product.compare_price || props.product.compare_price <= props.product.price) return 0;
    return Math.round(((props.product.compare_price - props.product.price) / props.product.compare_price) * 100);
});

// Group variants by their group name
const groupedVariants = computed(() => {
    if (!props.product.variants) return {};
    const groups: Record<string, any[]> = {};
    
    // @ts-ignore
    props.product.variants.forEach((v: any) => {
        const groupName = v.variant_group?.name || 'Otras Opciones';
        if (!groups[groupName]) {
            groups[groupName] = [];
        }
        groups[groupName].push(v);
        
        // Auto select first variant of each group if not selected
        if (!form.variants[groupName] && groups[groupName].length > 0) {
            // Logic to select default? Maybe better not to force unless needed.
            // form.variants[groupName] = groups[groupName][0].id;
        }
    });
    return groups;
});

const addToCart = () => {
    form.quantity = currentQuantity.value;
    form.post(route('cart.add'), {
        preserveScroll: true,
        onSuccess: () => {
            // Could add toast here
        },
    });
};

const buyNow = () => {
    form.quantity = currentQuantity.value;
    form.post(route('cart.add'), {
        onSuccess: () => {
            window.location.href = route('checkout.index');
        }
    });
};
</script>

<template>
    <AppLayout>
        <Head :title="product.name" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Breadcrumbs -->
            <Breadcrumbs
                :items="[
                    { label: product.category ? product.category.name : 'Productos', href: product.category ? `/categories/${product.category.slug}` : '/products' },
                    { label: product.name }
                ]"
            />

            <!-- Sales Letter Style Container -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6 lg:p-10">
                
                <!-- Gallery Section -->
                <div class="space-y-4">
                    <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-xl bg-gray-100 relative group">
                        <img :src="selectedImage" :alt="product.name" class="w-full h-full object-cover object-center transform transition duration-500 group-hover:scale-105">
                        <div v-if="discount > 0" class="absolute top-4 left-4 bg-red-600 text-white px-3 py-1 rounded-full font-bold shadow-md animate-pulse">
                            -{{ discount }}%
                        </div>
                    </div>
                    <div class="grid grid-cols-5 gap-2" v-if="product.images?.length > 1">
                        <button 
                            v-for="(img, idx) in product.images" 
                            :key="idx"
                            @click="selectedImage = img"
                            class="aspect-w-1 aspect-h-1 rounded-lg overflow-hidden border-2 transition-all duration-200 hover:opacity-100"
                            :class="selectedImage === img ? 'border-indigo-600 ring-2 ring-indigo-200' : 'border-transparent opacity-70'"
                        >
                            <img :src="img" class="w-full h-full object-cover">
                        </button>
                    </div>
                </div>

                <!-- Product Info - Sales Copy -->
                <div class="flex flex-col">
                    <div class="mb-4">
                        <Link v-if="product.category" :href="route('categories.show', product.category.slug)" class="text-sm text-indigo-600 font-medium hover:underline mb-2 inline-block">
                            {{ product.category.name }}
                        </Link>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight mb-2">
                            {{ product.name }}
                        </h1>
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="flex text-yellow-400">
                                <svg v-for="i in 5" :key="i" class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <span class="text-gray-500 text-sm">(4.8/5 basado en opiniones)</span>
                        </div>
                    </div>

                    <!-- Price Box -->
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 mb-6 relative overflow-hidden">
                        <!-- Urgency Banner -->
                        <div v-if="product.stock < 10 && isInStock" class="absolute top-0 left-0 right-0 bg-red-100 text-red-800 text-xs font-bold text-center py-1 border-b border-red-200">
                            ¡Quedan pocas unidades!
                        </div>

                        <div class="flex items-baseline mb-2 mt-4">
                            <span class="text-4xl font-black text-gray-900">€{{ product.price }}</span>
                            <span v-if="product.compare_price > product.price" class="ml-3 text-lg text-gray-400 line-through">
                                €{{ product.compare_price }}
                            </span>
                        </div>
                        <p v-if="discount > 0" class="text-green-600 font-bold text-sm mb-4">
                            ¡Ahorras €{{ (product.compare_price - product.price).toFixed(2) }} hoy!
                        </p>

                        <!-- Variants -->
                        <div v-for="(vars, groupName) in groupedVariants" :key="groupName" class="mb-4">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">{{ groupName }}</h3>
                            <div class="flex flex-wrap gap-2">
                                <button 
                                    v-for="v in vars" 
                                    :key="v.id"
                                    @click="form.variants[groupName] = v.id"
                                    class="px-3 py-1 text-sm border rounded-md transition-colors"
                                    :class="form.variants[groupName] === v.id ? 'border-indigo-600 bg-indigo-50 text-indigo-700 font-medium' : 'border-gray-300 text-gray-600 hover:border-gray-400'"
                                >
                                    {{ v.name }}
                                </button>
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div class="flex items-center space-x-2 mb-6">
                            <span class="relative flex h-3 w-3">
                              <span v-if="isInStock" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3" :class="isInStock ? 'bg-green-500' : 'bg-red-500'"></span>
                            </span>
                            <span class="font-medium" :class="isInStock ? 'text-green-700' : 'text-red-700'">
                                {{ isInStock ? 'En Stock - Envío Inmediato' : 'Agotado Temporalmente' }}
                            </span>
                        </div>

                        <!-- Quantity and Buttons -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-4">
                                <label class="font-medium text-gray-700">Cantidad:</label>
                                <div class="flex items-center border border-gray-300 rounded-lg bg-white">
                                    <button @click="currentQuantity > 1 && currentQuantity--" class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-l-lg">-</button>
                                    <input v-model="currentQuantity" readonly class="w-12 text-center border-none focus:ring-0 py-1 font-bold text-gray-900">
                                    <button @click="currentQuantity++" class="px-3 py-1 text-gray-600 hover:bg-gray-100 rounded-r-lg">+</button>
                                </div>
                            </div>

                            <button 
                                @click="buyNow" 
                                :disabled="!isInStock || form.processing"
                                class="w-full bg-yellow-400 hover:bg-yellow-500 text-gray-900 text-xl font-bold py-4 rounded-xl shadow-lg transform transition hover:-translate-y-1 active:translate-y-0 flex justify-center items-center space-x-2"
                            >
                                <span>¡Comprar Ahora!</span>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                            
                            <button 
                                @click="addToCart" 
                                :disabled="!isInStock || form.processing"
                                class="w-full bg-white border border-gray-300 text-gray-700 font-bold py-3 rounded-xl hover:bg-gray-50 transition"
                            >
                                Agregar al Carrito
                            </button>
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="grid grid-cols-3 gap-2 text-center text-xs text-gray-600 border-t pt-4">
                        <div class="flex flex-col items-center">
                            <span class="text-2xl mb-1">🚚</span>
                            <span>Envío Gratis</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="text-2xl mb-1">🔒</span>
                            <span>Pago Seguro</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="text-2xl mb-1">↩️</span>
                            <span>Devolución 30 días</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description & Details -->
            <div class="bg-gray-50 border-t border-gray-200 p-6 lg:p-10">
                <div class="max-w-3xl mx-auto">
                    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">¿Por qué elegir nuestro {{ product.name }}?</h2>
                    
                    <div class="prose prose-lg prose-indigo mx-auto" v-html="product.description"></div>
                </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
