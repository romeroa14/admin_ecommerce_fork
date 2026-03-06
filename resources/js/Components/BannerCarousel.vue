<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps<{
    banners: Array<{
        id: number;
        title: string;
        description?: string;
        image: string;
        mobile_image?: string;
        link?: string;
        button_text?: string;
    }>;
    autoplayInterval?: number;
}>();

const currentIndex = ref(0);
const isTransitioning = ref(false);
const isPaused = ref(false);
let autoplayTimer: ReturnType<typeof setInterval> | null = null;

const totalSlides = computed(() => props.banners.length);

function getImageUrl(path: string | null): string {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    return '/storage/' + path;
}

function goTo(index: number) {
    if (isTransitioning.value) return;
    isTransitioning.value = true;
    currentIndex.value = ((index % totalSlides.value) + totalSlides.value) % totalSlides.value;
    setTimeout(() => { isTransitioning.value = false; }, 600);
}

function next() {
    goTo(currentIndex.value + 1);
}

function prev() {
    goTo(currentIndex.value - 1);
}

function startAutoplay() {
    if (autoplayTimer) clearInterval(autoplayTimer);
    autoplayTimer = setInterval(() => {
        if (!isPaused.value) next();
    }, props.autoplayInterval || 5000);
}

function stopAutoplay() {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = null;
    }
}

onMounted(() => {
    if (totalSlides.value > 1) startAutoplay();
});

onUnmounted(() => {
    stopAutoplay();
});
</script>

<template>
    <div 
        v-if="banners.length > 0"
        class="banner-carousel relative w-full overflow-hidden"
        @mouseenter="isPaused = true"
        @mouseleave="isPaused = false"
    >
        <!-- Slides Container -->
        <div class="relative w-full" style="aspect-ratio: 21/9;">
            <TransitionGroup
                enter-active-class="transition-all duration-600 ease-out"
                leave-active-class="transition-all duration-600 ease-out absolute inset-0"
                enter-from-class="opacity-0 translate-x-8"
                enter-to-class="opacity-100 translate-x-0"
                leave-from-class="opacity-100 translate-x-0"
                leave-to-class="opacity-0 -translate-x-8"
            >
                <div
                    v-for="(banner, idx) in banners"
                    :key="banner.id"
                    v-show="idx === currentIndex"
                    class="absolute inset-0 w-full h-full"
                >
                    <!-- Background Image -->
                    <img
                        :src="getImageUrl(banner.image)"
                        :alt="banner.title"
                        class="w-full h-full object-cover"
                    >

                    <!-- Overlay gradient -->
                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent"></div>

                    <!-- Content -->
                    <div class="absolute inset-0 flex items-center">
                        <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 w-full">
                            <div class="max-w-lg space-y-4">
                                <h2 
                                    class="text-3xl md:text-5xl font-extrabold text-white leading-tight drop-shadow-lg"
                                    style="text-shadow: 0 2px 8px rgba(0,0,0,0.5);"
                                >
                                    {{ banner.title }}
                                </h2>
                                <p 
                                    v-if="banner.description" 
                                    class="text-base md:text-lg text-white/90 leading-relaxed drop-shadow"
                                    style="text-shadow: 0 1px 4px rgba(0,0,0,0.5);"
                                >
                                    {{ banner.description }}
                                </p>
                                <a
                                    v-if="banner.link"
                                    :href="banner.link"
                                    class="inline-flex items-center px-8 py-3.5 bg-[#F41D27] text-white font-bold rounded-lg hover:bg-red-600 transition-all transform hover:scale-105 shadow-xl text-sm md:text-base"
                                >
                                    {{ banner.button_text || 'Ver Más' }}
                                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </TransitionGroup>
        </div>

        <!-- Navigation Arrows -->
        <template v-if="totalSlides > 1">
            <button
                @click="prev"
                class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 backdrop-blur-sm hover:bg-white/40 text-white p-3 rounded-full transition-all shadow-lg group"
                aria-label="Anterior"
            >
                <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button
                @click="next"
                class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 backdrop-blur-sm hover:bg-white/40 text-white p-3 rounded-full transition-all shadow-lg group"
                aria-label="Siguiente"
            >
                <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <!-- Dots Indicator -->
            <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2.5">
                <button
                    v-for="(_, idx) in banners"
                    :key="idx"
                    @click="goTo(idx)"
                    class="transition-all duration-300 rounded-full"
                    :class="idx === currentIndex 
                        ? 'w-8 h-3 bg-white shadow-lg' 
                        : 'w-3 h-3 bg-white/50 hover:bg-white/80'"
                    :aria-label="'Ir al banner ' + (idx + 1)"
                />
            </div>
        </template>
    </div>
</template>

<style scoped>
.banner-carousel {
    container-type: inline-size;
}

.duration-600 {
    transition-duration: 600ms;
}
</style>
