<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import BannerCarousel from '@/Components/BannerCarousel.vue';
import { getProductImage } from '@/composables/useProductImage';

// @ts-ignore
const route = window.route;

const props = defineProps({
    bannerHero:   { type: Array as () => any[], default: () => [] },
    bannerMiddle: { type: Object as () => any,  default: null },
    bannerMiddle2:{ type: Object as () => any,  default: null },
    bannerBottom: { type: Object as () => any,  default: null },
    saleProducts: { type: Array as () => any[], default: () => [] },
    newProducts:  { type: Array as () => any[], default: () => [] },
    bestSellers:  { type: Array as () => any[], default: () => [] },
    categories:   { type: Array as () => any[], default: () => [] },
});

/* ── Category Carousel Logic ── */
const catSlide = ref(0);
const catsPerView = 4;
const totalCats = computed(() => props.categories?.length || 0);
const maxCatSlide = computed(() => Math.max(0, totalCats.value - catsPerView));

const visibleCats = computed(() => {
    if (!props.categories) return [];
    return props.categories.slice(catSlide.value, catSlide.value + catsPerView);
});

const prevCat = () => {
    if (catSlide.value > 0) catSlide.value--;
    else catSlide.value = maxCatSlide.value; // loop
};
const nextCat = () => {
    if (catSlide.value < maxCatSlide.value) catSlide.value++;
    else catSlide.value = 0; // loop
};

let catInterval: any = null;
const startCatAutoplay = () => {
    catInterval = setInterval(nextCat, 5000); // Changed to 5000 for slower movement
};
const stopCatAutoplay = () => {
    if (catInterval) clearInterval(catInterval);
};

onMounted(() => {
    if (totalCats.value > catsPerView) {
        startCatAutoplay();
    }
});
onUnmounted(() => {
    stopCatAutoplay();
});

function getBannerUrl(banner: any): string {
    return banner?.image ? `/storage/${banner.image}` : '';
}

function getCategoryImage(cat: any): string | null {
    const p = cat?.preview_product;
    if (!p) return null;
    return getProductImage(p);
}
</script>

<template>
    <AppLayout>
        <Head title="Inicio" />

        <!-- ═══════════════ 1. HERO BANNER ═══════════════ -->
        <section class="w-full">
            <BannerCarousel
                v-if="bannerHero && bannerHero.length > 0"
                :banners="bannerHero"
                :autoplay-interval="5000"
            />
        </section>

        <!-- ═══════════════ 2. CARRUSEL DE CATEGORÍAS ═══════════════ -->
        <section v-if="categories && categories.length > 0" class="bg-white py-12 border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- <h2 class="text-2xl font-extrabold text-[#040054] mb-8 text-center">Explora por Categoría</h2> -->
                
                <div class="relative group/carousel max-w-5xl mx-auto" @mouseenter="stopCatAutoplay" @mouseleave="startCatAutoplay">
                    <!-- Nav Prev -->
                    <button
                        v-if="totalCats > catsPerView"
                        @click="prevCat"
                        class="absolute -left-6 lg:-left-12 top-1/2 -translate-y-1/2 z-10 bg-white shadow hover:shadow-lg border border-gray-100 w-12 h-12 rounded-full flex items-center justify-center text-gray-500 hover:text-[#F41D27] opacity-0 group-hover/carousel:opacity-100 -translate-x-4 group-hover/carousel:translate-x-0 transition-all duration-300"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                    </button>

                    <!-- Visible Categories -->
                    <div class="cats-grid">
                        <Link
                            v-for="cat in visibleCats"
                            :key="cat.id"
                            :href="`/categories/${cat.slug}`"
                            class="cat-item group flex flex-col items-center gap-3"
                        >
                            <!-- Image / icon -->
                            <div class="cat-img overflow-hidden rounded-2xl bg-gray-100 border-2 border-transparent group-hover:border-[#F41D27] shadow-sm group-hover:shadow-lg transition-all duration-300 relative">
                                <img
                                    v-if="getCategoryImage(cat)"
                                    :src="getCategoryImage(cat)!"
                                    :alt="cat.name"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-400"
                                >
                                <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#040054]/10 to-[#F41D27]/10">
                                    <span class="text-6xl">{{ cat.icon || '📦' }}</span>
                                </div>
                                <!-- Hover overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-3">
                                    <span class="text-white text-xs font-black uppercase tracking-wider">Ver más →</span>
                                </div>
                            </div>
                            <!-- Name -->
                            <span class="text-sm font-bold text-gray-700 group-hover:text-[#F41D27] transition text-center leading-tight line-clamp-1">{{ cat.name }}</span>
                            <!-- Button -->
                            <!-- <span class="text-[11px] font-extrabold bg-[#F41D27] text-white px-3 py-1 rounded-sm group-hover:bg-[#040054] transition-colors uppercase tracking-wide">VER PRODUCTOS</span> -->
                        </Link>
                    </div>

                    <!-- Nav Next -->
                    <button
                        v-if="totalCats > catsPerView"
                        @click="nextCat"
                        class="absolute -right-6 lg:-right-12 top-1/2 -translate-y-1/2 z-10 bg-white shadow hover:shadow-lg border border-gray-100 w-12 h-12 rounded-full flex items-center justify-center text-gray-500 hover:text-[#F41D27] opacity-0 group-hover/carousel:opacity-100 translate-x-4 group-hover/carousel:translate-x-0 transition-all duration-300"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- ═══════════════ 3. OFERTAS EXCLUSIVAS ═══════════════ -->
        <section v-if="saleProducts && saleProducts.length > 0" class="bg-gray-50 py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-extrabold text-[#040054]">Ofertas Exclusivas</h2>
                    <Link href="/products?on_sale=1" class="text-sm font-bold text-[#F41D27] hover:underline flex items-center gap-1">
                        Ver todas <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                </div>
                <div class="product-grid">
                    <Link
                        v-for="product in saleProducts"
                        :key="product.id"
                        :href="route('products.show', product.slug)"
                        class="product-card group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col"
                    >
                        <div class="relative aspect-square overflow-hidden bg-gray-50">
                            <img :src="getProductImage(product)" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            <div class="absolute top-2 left-2 bg-[#F41D27] text-white px-2 py-0.5 rounded-md font-bold text-[11px] shadow">-{{ product.discount_percentage }}%</div>
                        </div>
                        <div class="p-3 flex-1 flex flex-col">
                            <span v-if="product.category" class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-0.5">{{ product.category.name }}</span>
                            <h3 class="text-sm font-bold text-gray-800 group-hover:text-[#F41D27] transition line-clamp-2 mb-2 flex-1 leading-snug">{{ product.name }}</h3>
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-base font-extrabold text-[#040054]">{{ $formatCurrency(product.price) }}</span>
                                    <span v-if="product.compare_price" class="ml-1.5 text-xs text-gray-400 line-through">{{ $formatCurrency(product.compare_price) }}</span>
                                </div>
                                <span v-if="product.stock > 0" class="text-[9px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded">En Stock</span>
                                <span v-else class="text-[9px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded">Agotado</span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- ═══════════════ 4. BANNER CENTRAL ═══════════════ -->
        <section v-if="bannerMiddle" class="w-full">
            <a :href="bannerMiddle.link || '#'" class="block w-full overflow-hidden relative">
                <img :src="getBannerUrl(bannerMiddle)" :alt="bannerMiddle.title" class="w-full object-cover max-h-[440px]">
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/20 to-transparent flex items-center">
                    <div class="max-w-7xl mx-auto px-8 w-full">
                        <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight drop-shadow-xl">{{ bannerMiddle.title }}</h2>
                        <p v-if="bannerMiddle.description" class="text-white/85 mb-6 max-w-lg text-base">{{ bannerMiddle.description }}</p>
                        <a v-if="bannerMiddle.button_text" :href="bannerMiddle.link || '#'" class="inline-block bg-[#F41D27] text-white font-extrabold px-8 py-3 rounded-xl shadow-xl hover:bg-[#cc1520] transition text-sm uppercase tracking-wide">{{ bannerMiddle.button_text }}</a>
                    </div>
                </div>
            </a>
        </section>

        <!-- ═══════════════ 5. ARTÍCULOS NUEVOS ═══════════════ -->
        <section v-if="newProducts && newProducts.length > 0" class="bg-white py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-extrabold text-[#040054]">Artículos Nuevos</h2>
                    <Link href="/products" class="text-sm font-bold text-[#F41D27] hover:underline flex items-center gap-1">
                        Ver todos <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                </div>
                <div class="product-grid">
                    <Link
                        v-for="product in newProducts"
                        :key="product.id"
                        :href="route('products.show', product.slug)"
                        class="product-card group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col"
                    >
                        <div class="relative aspect-square overflow-hidden bg-gray-50">
                            <img :src="getProductImage(product)" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            <div v-if="product.discount_percentage > 0" class="absolute top-2 left-2 bg-[#F41D27] text-white px-2 py-0.5 rounded-md font-bold text-[11px] shadow">-{{ product.discount_percentage }}%</div>
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors flex items-end justify-center pb-3 opacity-0 group-hover:opacity-100">
                                <span class="bg-white/95 text-[#040054] text-xs font-bold px-4 py-1.5 rounded-full shadow">Ver Producto</span>
                            </div>
                        </div>
                        <div class="p-3 flex-1 flex flex-col">
                            <span v-if="product.category" class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-0.5">{{ product.category.name }}</span>
                            <h3 class="text-sm font-bold text-gray-800 group-hover:text-[#F41D27] transition line-clamp-2 mb-2 flex-1 leading-snug">{{ product.name }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-base font-extrabold text-[#040054]">{{ $formatCurrency(product.price) }}</span>
                                <span v-if="product.stock > 0" class="text-[9px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded">En Stock</span>
                                <span v-else class="text-[9px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded">Agotado</span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- ═══════════════ 5.5 BANNER MEDIANO CENTRAL ═══════════════ -->
        <section v-if="bannerMiddle2" class="w-full">
            <a :href="bannerMiddle2.link || '#'" class="block w-full overflow-hidden relative">
                <img :src="getBannerUrl(bannerMiddle2)" :alt="bannerMiddle2.title" class="w-full object-cover max-h-[440px]">
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/20 to-transparent flex items-center">
                    <div class="max-w-7xl mx-auto px-8 w-full">
                        <h2 class="text-3xl md:text-5xl font-black text-white mb-4 leading-tight drop-shadow-xl">{{ bannerMiddle2.title }}</h2>
                        <p v-if="bannerMiddle2.description" class="text-white/85 mb-6 max-w-lg text-base">{{ bannerMiddle2.description }}</p>
                        <a v-if="bannerMiddle2.button_text" :href="bannerMiddle2.link || '#'" class="inline-block bg-[#F41D27] text-white font-extrabold px-8 py-3 rounded-xl shadow-xl hover:bg-[#cc1520] transition text-sm uppercase tracking-wide">{{ bannerMiddle2.button_text }}</a>
                    </div>
                </div>
            </a>
        </section>

        <!-- ═══════════════ 6. MÁS VENDIDOS ═══════════════ -->
        <section v-if="bestSellers && bestSellers.length > 0" class="bg-gray-50 py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-extrabold text-[#040054]">Más Vendidos</h2>
                    <Link href="/products" class="text-sm font-bold text-[#F41D27] hover:underline flex items-center gap-1">
                        Ver todos <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                </div>
                <div class="product-grid">
                    <Link
                        v-for="product in bestSellers"
                        :key="product.id"
                        :href="route('products.show', product.slug)"
                        class="product-card group bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col"
                    >
                        <div class="relative aspect-square overflow-hidden bg-gray-50">
                            <img :src="getProductImage(product)" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                            <div v-if="product.discount_percentage > 0" class="absolute top-2 left-2 bg-[#F41D27] text-white px-2 py-0.5 rounded-md font-bold text-[11px] shadow">-{{ product.discount_percentage }}%</div>
                        </div>
                        <div class="p-3 flex-1 flex flex-col">
                            <span v-if="product.category" class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-0.5">{{ product.category.name }}</span>
                            <h3 class="text-sm font-bold text-gray-800 group-hover:text-[#F41D27] transition line-clamp-2 mb-2 flex-1 leading-snug">{{ product.name }}</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-base font-extrabold text-[#040054]">{{ $formatCurrency(product.price) }}</span>
                                <span v-if="product.stock > 0" class="text-[9px] font-bold text-green-600 bg-green-50 px-1.5 py-0.5 rounded">En Stock</span>
                                <span v-else class="text-[9px] font-bold text-red-500 bg-red-50 px-1.5 py-0.5 rounded">Agotado</span>
                            </div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>

        <!-- ═══════════════ 7. BANNER FOOTER ═══════════════ -->
        <section v-if="bannerBottom" class="w-full">
            <a :href="bannerBottom.link || '#'" class="block w-full overflow-hidden relative">
                <img :src="getBannerUrl(bannerBottom)" :alt="bannerBottom.title" class="w-full object-cover max-h-[220px]">
                <div class="absolute inset-0 bg-gradient-to-r from-[#040054]/75 via-[#040054]/40 to-transparent flex items-center">
                    <div class="max-w-7xl mx-auto px-8 w-full flex items-center justify-between gap-6">
                        <div>
                            <h3 class="text-xl md:text-3xl font-black text-white mb-1 drop-shadow">{{ bannerBottom.title }}</h3>
                            <p v-if="bannerBottom.description" class="text-white/80 text-sm">{{ bannerBottom.description }}</p>
                        </div>
                        <a v-if="bannerBottom.button_text" :href="bannerBottom.link || '#'" class="flex-shrink-0 bg-[#F41D27] text-white font-extrabold px-7 py-3 rounded-xl shadow-xl hover:bg-[#cc1520] transition text-sm uppercase tracking-wide whitespace-nowrap">{{ bannerBottom.button_text }}</a>
                    </div>
                </div>
            </a>
        </section>

    </AppLayout>
</template>

<style scoped>
/* ──────────────────────────────────────────────────────────
   GRID DE CATEGORÍAS — Mostrando siempre 4 y centrados
   ────────────────────────────────────────────────────────── */
.cats-grid {
    display: grid;
    /* Forzamos que sean 4 columnas de igual tamaño, usando 1fr */
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    justify-items: center;
}

.cat-item { width: 100%; max-width: 220px; }

.cat-img {
    width: 100%;
    aspect-ratio: 1 / 1;
}

/* ──────────────────────────────────────────────────────────
   GRID DE PRODUCTOS — Exactamente 4 columnas en desktop
   ────────────────────────────────────────────────────────── */
.product-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.875rem;
}
@media (min-width: 640px)  { .product-grid { grid-template-columns: repeat(3, 1fr); } }
/* En desktop (1024px) forzamos a 4 columnas */
@media (min-width: 1024px) { .product-grid { grid-template-columns: repeat(4, 1fr); } }

/* ──────────────────────────────────────────────────────────
   TEXT CLAMP
   ────────────────────────────────────────────────────────── */
.line-clamp-1 {
    display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical;
    line-clamp: 1; overflow: hidden;
}
.line-clamp-2 {
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
    line-clamp: 2; overflow: hidden;
}
</style>
