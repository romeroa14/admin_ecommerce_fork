<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import ImageZoomModal from '@/Components/ImageZoomModal.vue';
import { getProductImage, getAllProductImages } from '@/composables/useProductImage';

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
    reviews: {
        type: Array,
        default: () => [],
    },
    reviewStats: {
        type: Object,
        default: () => ({ average: 0, total: 0, ratings: { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 } }),
    },
});

const form = useForm({
    product_id: props.product.id,
    quantity: 1,
    variants: {} as Record<string, any>,
});

const allImages = getAllProductImages(props.product);
const selectedImage = ref(allImages[0] || 'https://placehold.co/600x600/f3f4f6/9ca3af?text=Sin+imagen');
const currentQuantity = ref(1);
const isZoomOpen = ref(false);
const zoomImageIndex = ref(0);
const addedToCart = ref(false);

// Tab state for product info section
const activeTab = ref('descripcion');

// Carousel
const carouselContainer = ref<HTMLElement | null>(null);
const canScrollLeft = ref(false);
const canScrollRight = ref(false);

// Review form
const showReviewForm = ref(false);
const reviewForm = useForm({
    rating: 5,
    title: '',
    comment: '',
    image: null,
    youtube_url: '',
    reviewer_name: '',
    reviewer_email: '',
});

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
            addedToCart.value = true;
            // Open the cart sidebar
            window.dispatchEvent(new Event('open-cart-sidebar'));
            setTimeout(() => {
                addedToCart.value = false;
            }, 3000);
        },
    });
};

const buyNow = () => {
    form.quantity = currentQuantity.value;
    form.post(route('cart.add'), {
        onSuccess: () => {
            window.location.href = route('checkout.init');
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
                                @click="openZoom(allImages.indexOf(selectedImage))"
                                class="absolute bottom-4 right-4 bg-white hover:bg-gray-50 px-5 py-3 rounded-lg flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-all shadow-xl border border-gray-200 font-semibold"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7" />
                                </svg>
                                <span class="text-sm">ZOOM</span>
                            </button>
                        </div>
                        
                        <!-- Thumbnails -->
                        <div class="grid grid-cols-5 gap-3" v-if="allImages.length > 1">
                            <button 
                                v-for="(imgUrl, idx) in allImages" 
                                :key="idx"
                                @click="selectedImage = imgUrl"
                                class="aspect-square rounded-lg overflow-hidden border-2 transition-all duration-200"
                                :class="selectedImage === imgUrl ? 'border-[#040054] ring-2 ring-[#040054]/20' : 'border-gray-200 hover:border-gray-300'"
                            >
                                <img :src="imgUrl" class="w-full h-full object-cover">
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
                                <span class="text-5xl font-black text-gray-900">{{ $formatCurrency(product.price) }}</span>
                                <span v-if="product.compare_price > product.price" class="ml-3 text-2xl text-gray-400 line-through">
                                    {{ $formatCurrency(product.compare_price) }}
                                </span>
                            </div>
                            <p v-if="discount > 0" class="text-[#F41D27] font-bold text-base">
                                ¡Ahorras {{ $formatCurrency((product.compare_price - product.price)) }} hoy!
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
                                class="w-full font-bold py-4 rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                :class="addedToCart 
                                    ? 'bg-green-500 text-white border-2 border-green-500' 
                                    : 'bg-white border-2 border-[#040054] text-[#040054] hover:bg-[#040054] hover:text-white'"
                            >
                                <template v-if="addedToCart">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    ¡Agregado al Carrito!
                                </template>
                                <template v-else-if="form.processing">
                                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Agregando...
                                </template>
                                <template v-else>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                    Agregar al Carrito
                                </template>
                            </button>

                            <!-- View Cart Link (appears after adding) -->
                            <Transition
                                enter-active-class="transition-all duration-300"
                                enter-from-class="opacity-0 -translate-y-2"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition-all duration-200"
                                leave-from-class="opacity-100"
                                leave-to-class="opacity-0"
                            >
                                <Link v-if="addedToCart" href="/cart" class="block w-full text-center text-sm font-semibold text-[#040054] hover:underline py-2">
                                    Ver carrito →
                                </Link>
                            </Transition>
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


                    </div>
                </div>

                <!-- =================== TABS: Descripción / Envío / Similares =================== -->
                <div class="mt-12 border-t border-gray-200 pt-8">
                    <!-- Tab Buttons -->
                    <div class="border-b border-gray-200">
                        <nav class="flex gap-8">
                            <button
                                @click="activeTab = 'descripcion'"
                                class="relative pb-4 text-sm font-bold uppercase tracking-wider transition-colors"
                                :class="activeTab === 'descripcion' ? 'text-[#040054]' : 'text-gray-400 hover:text-gray-600'"
                            >
                                Descripción
                                <span v-if="activeTab === 'descripcion'" class="absolute bottom-0 left-0 right-0 h-[3px] bg-[#040054] rounded-t"></span>
                            </button>
                            <button
                                @click="activeTab = 'envio'"
                                class="relative pb-4 text-sm font-bold uppercase tracking-wider transition-colors"
                                :class="activeTab === 'envio' ? 'text-[#040054]' : 'text-gray-400 hover:text-gray-600'"
                            >
                                Tiempos de Envío
                                <span v-if="activeTab === 'envio'" class="absolute bottom-0 left-0 right-0 h-[3px] bg-[#040054] rounded-t"></span>
                            </button>
                            <button
                                @click="activeTab = 'similares'"
                                class="relative pb-4 text-sm font-bold uppercase tracking-wider transition-colors"
                                :class="activeTab === 'similares' ? 'text-[#040054]' : 'text-gray-400 hover:text-gray-600'"
                            >
                                Productos Similares
                                <span v-if="activeTab === 'similares'" class="absolute bottom-0 left-0 right-0 h-[3px] bg-[#040054] rounded-t"></span>
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="py-8">
                        <!-- Descripción -->
                        <div v-show="activeTab === 'descripcion'">
                            <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                                <div v-if="product.short_description" class="mb-6">
                                    <p class="text-lg text-gray-500 italic">{{ product.short_description }}</p>
                                </div>
                                <div v-if="product.description" v-html="product.description"></div>
                                <p v-else class="text-gray-400">No hay descripción disponible para este producto.</p>
                            </div>
                        </div>

                        <!-- Envío -->
                        <div v-show="activeTab === 'envio'">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="flex items-start gap-4 p-6 bg-green-50 rounded-xl">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 mb-1">Envío Gratis, Nivel Nacional</h4>
                                        <p class="text-sm text-gray-600">En pedidos superiores a {{ $formatCurrency(25) }} para envíos a toda venezuela por MRW ( Que pese menos de 1kg en total ).</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4 p-6 bg-blue-50 rounded-xl">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 mb-1">Paga Al recibir</h4>
                                        <p class="text-sm text-gray-600">Valido solo en Caracas.</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4 p-6 bg-purple-50 rounded-xl">
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 mb-1">Retiro en Tienda</h4>
                                        <p class="text-sm text-gray-600">Disponible para pickup y retiro en Caracas La Candelaria C.C. Galerias Avila Local A-14. ¡Sin costo adicional!</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Productos Similares -->
                        <div v-show="activeTab === 'similares'">
                            <div v-if="similarProducts.length > 0" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                                <Link
                                    v-for="similar in similarProducts"
                                    :key="similar.id"
                                    :href="route('products.show', similar.slug)"
                                    class="group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col"
                                >
                                    <div class="relative aspect-square overflow-hidden bg-gray-50">
                                        <img
                                            :src="getProductImage(similar, 'https://placehold.co/300x300/f3f4f6/9ca3af?text=Sin+imagen')"
                                            :alt="similar.name"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            loading="lazy"
                                        >
                                        <div v-if="similar.stock > 0" class="absolute top-2 right-2">
                                            <span class="bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">En Stock</span>
                                        </div>
                                    </div>
                                    <div class="p-3 flex-1 flex flex-col">
                                        <h4 class="text-sm font-semibold text-gray-800 group-hover:text-[#F41D27] transition line-clamp-2 mb-2 flex-1 leading-snug">
                                            {{ similar.name }}
                                        </h4>
                                        <div class="flex items-center justify-between">
                                            <span class="text-base font-extrabold text-[#040054]">{{ $formatCurrency(similar.price) }}</span>
                                            <span v-if="similar.compare_price && similar.compare_price > similar.price" class="text-xs text-gray-400 line-through">
                                                {{ $formatCurrency(similar.compare_price) }}
                                            </span>
                                        </div>
                                    </div>
                                </Link>
                            </div>
                            <p v-else class="text-center text-gray-400 py-8">No hay productos similares en esta categoría.</p>
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
                                        :src="getProductImage(similar, 'https://placehold.co/300x300/f3f4f6/9ca3af?text=Sin+imagen')" 
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
                                            <span class="text-2xl font-black text-[#040054]">{{ $formatCurrency(similar.price) }}</span>
                                            <span v-if="similar.compare_price && similar.compare_price > similar.price" class="text-sm text-gray-400 line-through">
                                                {{ $formatCurrency(similar.compare_price) }}
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

                <!-- Reviews Section -->
                <div class="mt-16 border-t border-gray-200 pt-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8">Reseñas de Clientes</h2>
                    
                    <!-- Reviews Summary -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                        <!-- Average Rating -->
                        <div class="bg-gray-50 rounded-xl p-8 text-center">
                            <div class="text-5xl font-black text-[#040054] mb-2">{{ reviewStats.average || 0 }}</div>
                            <div class="flex justify-center mb-3">
                                <svg v-for="i in 5" :key="i" class="w-6 h-6" :class="i <= Math.round(reviewStats.average) ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current'" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            <p class="text-gray-600">Basado en {{ reviewStats.total }} reseñas</p>
                        </div>

                        <!-- Rating Distribution -->
                        <div class="lg:col-span-2 space-y-3">
                            <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="flex items-center gap-3">
                                <div class="flex items-center gap-1 w-20">
                                    <span class="text-sm font-semibold text-gray-700">{{ star }}</span>
                                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 h-3 bg-gray-200 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full bg-[#040054] rounded-full transition-all"
                                        :style="{ width: reviewStats.total > 0 ? (reviewStats.ratings[star] / reviewStats.total * 100) + '%' : '0%' }"
                                    ></div>
                                </div>
                                <span class="text-sm text-gray-600 w-8">{{ reviewStats.ratings[star] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Write Review Button -->
                    <div class="text-center mb-12">
                        <button 
                            @click="showReviewForm = !showReviewForm"
                            class="bg-[#5A3D2B] text-white px-8 py-4 rounded-lg hover:bg-[#6B4D3B] transition-colors font-semibold text-lg"
                        >
                            {{ showReviewForm ? 'Cancelar' : 'Escribir una reseña' }}
                        </button>
                    </div>

                    <!-- Review Form -->
                    <div v-if="showReviewForm" class="bg-gray-50 rounded-2xl p-8 mb-12 border-2 border-gray-200">
                        <h3 class="text-2xl font-bold mb-6 text-center">Escribe tu reseña</h3>
                        <form @submit.prevent="reviewForm.post(route('reviews.store', product.slug))" class="max-w-2xl mx-auto space-y-6">
                            <!-- Rating -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2 text-center">Calificación</label>
                                <div class="flex justify-center gap-2">
                                    <button 
                                        v-for="star in [1, 2, 3, 4, 5]" 
                                        :key="star"
                                        type="button"
                                        @click="reviewForm.rating = star"
                                        class="transition-transform hover:scale-110"
                                    >
                                        <svg class="w-10 h-10" :class="star <= reviewForm.rating ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current'" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Título de la Reseña <span class="text-gray-400">(100)</span></label>
                                <input 
                                    v-model="reviewForm.title"
                                    type="text"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-[#040054] focus:ring-2 focus:ring-[#040054]/20 transition"
                                    placeholder="Da un título a tu reseña"
                                    maxlength="100"
                                    required
                                >
                            </div>

                            <!-- Comment -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Contenido de la reseña</label>
                                <textarea 
                                    v-model="reviewForm.comment"
                                    rows="5"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-[#040054] focus:ring-2 focus:ring-[#040054]/20 transition"
                                    placeholder="Empieza a escribir aquí..."
                                ></textarea>
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Imagen/Video (opcional)</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-[#040054] transition-colors cursor-pointer">
                                    <input type="file" @change="reviewForm.image = $event.target.files[0]" accept="image/*" class="hidden" id="review-image">
                                    <label for="review-image" class="cursor-pointer">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        <p class="text-gray-600">Haz clic para subir una imagen</p>
                                    </label>
                                </div>
                            </div>

                            <!-- YouTube URL -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">YouTube URL</label>
                                <input 
                                    v-model="reviewForm.youtube_url"
                                    type="url"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-[#040054] focus:ring-2 focus:ring-[#040054]/20 transition"
                                    placeholder="https://youtube.com/..."
                                >
                            </div>

                            <!-- Name & Email -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre <span class="text-xs text-gray-500">(mostrado públicamente como John Smith...)</span></label>
                                    <input 
                                        v-model="reviewForm.reviewer_name"
                                        type="text"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-[#040054] focus:ring-2 focus:ring-[#040054]/20 transition"
                                        placeholder="Nombre"
                                        required
                                    >
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Dirección de correo electrónico</label>
                                    <input 
                                        v-model="reviewForm.reviewer_email"
                                        type="email"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-[#040054] focus:ring-2 focus:ring-[#040054]/20 transition"
                                        placeholder="Tu dirección de correo electrónico"
                                        required
                                    >
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="flex gap-4 justify-center pt-4">
                                <button 
                                    type="button"
                                    @click="showReviewForm = false"
                                    class="px-8 py-3 border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-semibold"
                                >
                                    Cancelar reseña
                                </button>
                                <button 
                                    type="submit"
                                    :disabled="reviewForm.processing"
                                    class="px-8 py-3 bg-[#5A3D2B] text-white rounded-lg hover:bg-[#6B4D3B] transition font-semibold disabled:opacity-50"
                                >
                                    {{ reviewForm.processing ? 'Enviando...' : 'Enviar Reseña' }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Reviews List -->
                    <div v-if="reviews.length > 0" class="space-y-6">
                        <div v-for="review in reviews" :key="review.id" class="bg-white rounded-xl p-6 border border-gray-200">
                            <!-- Review Header -->
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-bold text-gray-900">{{ review.reviewer_display_name }}</span>
                                        <span v-if="review.is_verified_purchase" class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-semibold">Verificado</span>
                                    </div>
                                    <div class="flex gap-1 mb-2">
                                        <svg v-for="i in 5" :key="i" class="w-5 h-5" :class="i <= review.rating ? 'text-yellow-400 fill-current' : 'text-gray-300 fill-current'" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-500">{{ new Date(review.created_at).toLocaleDateString() }}</span>
                            </div>

                            <!-- Review Title -->
                            <h4 class="text-lg font-bold text-gray-900 mb-2">{{ review.title }}</h4>

                            <!-- Review Content -->
                            <p class="text-gray-700 mb-4">{{ review.comment }}</p>

                            <!-- Review Image -->
                            <img v-if="review.image_url" :src="review.image_url" class="w-32 h-32 object-cover rounded-lg mb-4">

                            <!-- YouTube Video -->
                            <div v-if="review.youtube_embed_url" class="mb-4">
                                <iframe 
                                    :src="review.youtube_embed_url" 
                                    class="w-full aspect-video rounded-lg"
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                ></iframe>
                            </div>
                        </div>
                    </div>

                    <!-- No Reviews -->
                    <div v-else class="text-center py-12 bg-gray-50 rounded-xl">
                        <p class="text-gray-500">Aún no hay reseñas para este producto. ¡Sé el primero en escribir una!</p>
                    </div>
                </div>
                
            </div>
        </div>

        <!-- Image Zoom Modal -->
        <ImageZoomModal 
            :is-open="isZoomOpen"
            :images="allImages"
            :product-name="product.name"
            :current-index="zoomImageIndex"
            @close="isZoomOpen = false"
        />
    </AppLayout>
</template>
