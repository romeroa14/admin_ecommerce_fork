<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import ImageZoomModal from '@/Components/ImageZoomModal.vue';

// @ts-ignore
const route = window.route;

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    isInStock: Boolean,
    similarProducts: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    product_id: props.product.id,
    quantity: 1,
    variants: {} as Record<string, any>,
});

const selectedImage = ref(props.product.images?.[0] || 'https://via.placeholder.com/600');
const currentQuantity = ref(1);
const isZoomOpen = ref(false);
const zoomImageIndex = ref(0);

// Accordion states for right sidebar
const detailsOpen = ref(false);
const shippingOpen = ref(false);
const similarOpen = ref(false);

// Carousel
const carouselContainer = ref<HTMLElement | null>(null);
const canScrollLeft = ref(false);
const canScrollRight = ref(false);

const discount = computed(() => {
    if (!props.product.compare_price || props.product.compare_price <= props.product.price) return 0;
    return Math.round(((props.product.compare_price - props.product.price) / props.product.compare_price) * 100);
});

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
    });
    return groups;
});

const openZoom = (index: number = 0) => {
    zoomImageIndex.value = index;
    isZoomOpen.value = true;
};

const updateScrollButtons = () => {
    if (!carouselContainer.value) return;
    
    const container = carouselContainer.value;
    canScrollLeft.value = container.scrollLeft > 0;
    canScrollRight.value = container.scrollLeft < (container.scrollWidth - container.clientWidth - 10);
};

const scrollCarousel = (direction: 'left' | 'right') => {
    if (!carouselContainer.value) return;
    
    const scrollAmount = 300;
    const newScrollLeft = direction === 'left' 
        ? carouselContainer.value.scrollLeft - scrollAmount
        : carouselContainer.value.scrollLeft + scrollAmount;
    
    carouselContainer.value.scrollTo({
        left: newScrollLeft,
        behavior: 'smooth'
    });
    
    setTimeout(updateScrollButtons, 300);
};

const addToCart = () => {
    form.quantity = currentQuantity.value;
    form.post(route('cart.add'), {
        preserveScroll: true,
        onSuccess: () => {
            // Success
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

        <div class="bg-white min-h-screen">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Breadcrumbs -->
                <Breadcrumbs
                    :items="[
                        { label: product.category ? product.category.name : 'Productos', href: product.category ? `/categories/${product.category.slug}` : '/products' },
                        { label: product.name }
                    ]"
                />

                <!-- Main Product Section - Full Width -->
                <div class="flex flex-col lg:flex-row gap-8 mt-6">
                    
                    <!-- Left: Gallery (45%) -->
                    <div class="w-full lg:w-[45%] space-y-4">
                        <div class="aspect-square w-full overflow-hidden rounded-2xl bg-gray-100 relative group border border-gray-200">
                            <img 
                                :src="selectedImage" 
                                :alt="product.name" 
                                class="w-full h-full object-contain p-4"
                            >
                            <!-- Discount Badge -->
                            <div v-if="discount > 0" class="absolute top-4 left-4 bg-[#F41D27] text-white px-4 py-2 rounded-full font-bold shadow-lg animate-pulse">
                                -{{ discount }}%
                            </div>
                            
                            <!-- Zoom Button -->
                            <button 
                                @click="openZoom(product.images.indexOf(selectedImage))"
                                class="absolute bottom-4 right-4 bg-white hover:bg-gray-50 px-5 py-3 rounded-lg flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-all shadow-xl border border-gray-200 font-semibold"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                                </svg>
                                <span class="text-sm">ZOOM</span>
                            </button>
                        </div>
                        
                        <!-- Thumbnails -->
                        <div class="grid grid-cols-5 gap-3" v-if="product.images?.length > 1">
                            <button 
                                v-for="(img, idx) in product.images" 
                                :key="idx"
                                @click="selectedImage = img"
                                class="aspect-square rounded-lg overflow-hidden border-2 transition-all duration-200"
                                :class="selectedImage === img ? 'border-[#040054] ring-2 ring-[#040054]/20' : 'border-gray-200 hover:border-gray-300'"
                            >
                                <img :src="img" class="w-full h-full object-cover">
                            </button>
                        </div>
                    </div>

                    <!-- Right: Product Info (55%) -->
                    <div class="w-full lg:w-[55%] flex flex-col">
                        <!-- Category Badge -->
                        <Link v-if="product.category" :href="route('categories.show', product.category.slug)" class="text-sm text-[#040054] font-semibold hover:underline mb-2 inline-block">
                            {{ product.category.name }}
                        </Link>
                        
                        <!-- Product Name -->
                        <h1 class="text-4xl font-extrabold text-gray-900 leading-tight mb-3">
                            {{ product.name }}
                        </h1>
                        
                        <!-- Rating -->
                        <div class="flex items-center space-x-2 mb-6">
                            <div class="flex text-yellow-400">
                                <svg v-for="i in 5" :key="i" class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <span class="text-gray-500 text-sm">(4.8/5 basado en opiniones)</span>
                        </div>

                        <!-- Price -->
                        <div class="mb-6">
                            <div class="flex items-baseline mb-2">
                                <span class="text-5xl font-black text-gray-900">€{{ product.price }}</span>
                                <span v-if="product.compare_price > product.price" class="ml-3 text-2xl text-gray-400 line-through">
                                    €{{ product.compare_price }}
                                </span>
                            </div>
                            <p v-if="discount > 0" class="text-[#F41D27] font-bold text-base">
                                ¡Ahorras €{{ (product.compare_price - product.price).toFixed(2) }} hoy!
                            </p>
                        </div>

                        <!-- Stock Status -->
                        <div class="flex items-center space-x-2 mb-6 p-4 rounded-lg" :class="isInStock ? 'bg-green-50' : 'bg-red-50'">
                            <span class="relative flex h-3 w-3">
                              <span v-if="isInStock" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3" :class="isInStock ? 'bg-green-500' : 'bg-red-500'"></span>
                            </span>
                            <span class="font-semibold" :class="isInStock ? 'text-green-700' : 'text-red-700'">
                                {{ isInStock ? 'En Stock - Envío Inmediato' : 'Agotado Temporalmente' }}
                            </span>
                        </div>

                        <!-- Variants -->
                        <div v-for="(vars, groupName) in groupedVariants" :key="groupName" class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ groupName }}</h3>
                            <div class="flex flex-wrap gap-2">
                                <button 
                                    v-for="v in vars" 
                                    :key="v.id"
                                    @click="form.variants[groupName] = v.id"
                                    class="px-4 py-2 text-sm border-2 rounded-lg transition-all font-medium"
                                    :class="form.variants[groupName] === v.id ? 'border-[#040054] bg-[#040054]/5 text-[#040054]' : 'border-gray-300 text-gray-600 hover:border-gray-400'"
                                >
                                    {{ v.name }}
                                </button>
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-6">
                            <label class="text-sm font-semibold text-gray-900 mb-3 block">Cantidad:</label>
                            <div class="flex items-center border-2 border-gray-300 rounded-lg bg-white w-32">
                                <button @click="currentQuantity > 1 && currentQuantity--" class="px-4 py-3 text-gray-600 hover:bg-gray-100 transition">-</button>
                                <input v-model="currentQuantity" readonly class="w-16 text-center border-none focus:ring-0 py-3 font-bold text-gray-900 bg-transparent">
                                <button @click="currentQuantity++" class="px-4 py-3 text-gray-600 hover:bg-gray-100 transition">+</button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3 mb-6">
                            <button 
                                @click="buyNow" 
                                :disabled="!isInStock || form.processing"
                                class="w-full bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 text-gray-900 text-lg font-bold py-5 rounded-xl shadow-lg transform transition hover:-translate-y-0.5 active:translate-y-0 flex justify-center items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span>¡Comprar Ahora!</span>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                            
                            <button 
                                @click="addToCart" 
                                :disabled="!isInStock || form.processing"
                                class="w-full bg-white border-2 border-[#040054] text-[#040054] font-bold py-4 rounded-xl hover:bg-[#040054] hover:text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Agregar al Carrito
                            </button>
                        </div>

                        <!-- Trust Badges -->
                        <div class="grid grid-cols-3 gap-4 py-6 border-y border-gray-200 mb-6">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-2">
                                    <span class="text-2xl">🚚</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-700">Envío Gratis</span>
                            </div>
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-2">
                                    <span class="text-2xl">🔒</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-700">Pago Seguro</span>
                            </div>
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mb-2">
                                    <span class="text-2xl">↩️</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-700">Devolución 30d</span>
                            </div>
                        </div>

                        <!-- Accordions -->
                        <div class="space-y-3">
                            <!-- Details Accordion -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="detailsOpen = !detailsOpen"
                                    class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition"
                                >
                                    <span class="font-bold text-gray-900">DETALLES</span>
                                    <svg class="w-5 h-5 transition-transform" :class="detailsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div v-show="detailsOpen" class="p-4 bg-white text-sm text-gray-600 leading-relaxed">
                                    <div v-html="product.description"></div>
                                </div>
                            </div>

                            <!-- Shipping Accordion -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="shippingOpen = !shippingOpen"
                                    class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition"
                                >
                                    <span class="font-bold text-gray-900">TIEMPOS DE ENVÍO</span>
                                    <svg class="w-5 h-5 transition-transform" :class="shippingOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div v-show="shippingOpen" class="p-4 bg-white space-y-3">
                                    <div class="flex items-start space-x-3 text-sm">
                                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-gray-900">Envíos gratis en pedidos de +€100 (Península)</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3 text-sm">
                                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-gray-900">Entrega 24/72h (Península) Resto de Europa 3-7 días</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3 text-sm">
                                        <svg class="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div>
                                            <p class="font-semibold text-gray-900">Envíos gratis en pedidos internacionales de +150€</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Similar Products Accordion -->
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <button 
                                    @click="similarOpen = !similarOpen"
                                    class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 transition"
                                >
                                    <span class="font-bold text-gray-900">PAIRS WELL WITH</span>
                                    <svg class="w-5 h-5 transition-transform" :class="similarOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                                <div v-show="similarOpen" class="p-4 bg-white">
                                    <div v-if="similarProducts.length > 0" class="grid grid-cols-3 gap-3">
                                        <Link 
                                            v-for="similar in similarProducts.slice(0, 3)" 
                                            :key="similar.id"
                                            :href="route('products.show', similar.slug)"
                                            class="group text-center"
                                        >
                                            <div class="aspect-square w-full overflow-hidden rounded-lg bg-gray-100 mb-2">
                                                <img 
                                                    :src="similar.images?.[0] || 'https://via.placeholder.com/200'" 
                                                    :alt="similar.name"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                                >
                                            </div>
                                            <h4 class="text-xs font-semibold text-gray-900 line-clamp-2 mb-1">
                                                {{ similar.name }}
                                            </h4>
                                            <p class="text-sm font-bold text-[#040054]">€{{ similar.price }}</p>
                                            <button class="mt-2 w-full text-xs font-semibold text-[#040054] border border-[#040054] rounded px-2 py-1 hover:bg-[#040054] hover:text-white transition">
                                                ADD
                                            </button>
                                        </Link>
                                    </div>
                                    <p v-else class="text-sm text-gray-500 text-center py-4">No hay productos similares</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Similar Products Carousel -->
                <div class="mt-16 border-t border-gray-200 pt-12">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">Productos Similares</h2>
                            <p class="text-gray-600">Otros productos de {{ product.category?.name || 'esta categoría' }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button 
                                @click="scrollCarousel('left')"
                                class="p-3 rounded-full bg-white border-2 border-gray-300 hover:border-[#040054] hover:bg-[#040054] hover:text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!canScrollLeft"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button 
                                @click="scrollCarousel('right')"
                                class="p-3 rounded-full bg-white border-2 border-gray-300 hover:border-[#040054] hover:bg-[#040054] hover:text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                :disabled="!canScrollRight"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Carousel Container -->
                    <div v-if="similarProducts.length > 0" class="relative">
                        <div 
                            ref="carouselContainer"
                            @scroll="updateScrollButtons"
                            class="flex space-x-6 overflow-x-auto scrollbar-hide scroll-smooth pb-4"
                            style="scrollbar-width: none; -ms-overflow-style: none;"
                        >
                            <Link 
                                v-for="similar in similarProducts" 
                                :key="similar.id"
                                :href="route('products.show', similar.slug)"
                                class="group flex-shrink-0 w-72 bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 overflow-hidden"
                            >
                                <!-- Product Image -->
                                <div class="relative aspect-square w-full overflow-hidden bg-gray-100">
                                    <img 
                                        :src="similar.images?.[0] || 'https://via.placeholder.com/300'" 
                                        :alt="similar.name"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                    >
                                    <!-- Quick View Overlay -->
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-all flex items-center justify-center">
                                        <span class="bg-white text-[#040054] px-4 py-2 rounded-lg font-semibold opacity-0 group-hover:opacity-100 transform scale-90 group-hover:scale-100 transition-all">
                                            Ver Detalles
                                        </span>
                                    </div>
                                    <!-- Stock Badge -->
                                    <div v-if="similar.stock > 0" class="absolute top-3 right-3">
                                        <span class="bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                            En Stock
                                        </span>
                                    </div>
                                    <div v-else class="absolute top-3 right-3">
                                        <span class="bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                            Agotado
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Product Info -->
                                <div class="p-5">
                                    <h4 class="text-base font-bold text-gray-900 group-hover:text-[#F41D27] transition line-clamp-2 mb-3 min-h-[3rem]">
                                        {{ similar.name }}
                                    </h4>
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex flex-col">
                                            <span class="text-2xl font-black text-[#040054]">€{{ similar.price }}</span>
                                            <span v-if="similar.compare_price && similar.compare_price > similar.price" class="text-sm text-gray-400 line-through">
                                                €{{ similar.compare_price }}
                                            </span>
                                        </div>
                                        <div class="flex text-yellow-400">
                                            <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <button class="w-full bg-[#040054] text-white font-semibold py-3 rounded-lg hover:bg-[#060078] transition-colors flex items-center justify-center space-x-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <span>Ver Producto</span>
                                    </button>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="text-center py-16 bg-gray-50 rounded-xl">
                        <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No hay productos similares</h3>
                        <p class="text-gray-500">Por el momento no tenemos más productos en esta categoría</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Zoom Modal -->
        <ImageZoomModal 
            :is-open="isZoomOpen"
            :images="product.images || []"
            :product-name="product.name"
            :current-index="zoomImageIndex"
            @close="isZoomOpen = false"
        />
    </AppLayout>
</template>
