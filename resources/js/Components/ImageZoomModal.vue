<script setup lang="ts">
import { ref, computed } from 'vue';

const props = defineProps<{
    isOpen: boolean;
    images: string[];
    productName: string;
    currentIndex?: number;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const activeIndex = ref(props.currentIndex || 0);

const currentImage = computed(() => props.images[activeIndex.value] || '');

const nextImage = () => {
    if (activeIndex.value < props.images.length - 1) {
        activeIndex.value++;
    } else {
        activeIndex.value = 0; // Loop back
    }
};

const prevImage = () => {
    if (activeIndex.value > 0) {
        activeIndex.value--;
    } else {
        activeIndex.value = props.images.length - 1; // Loop to end
    }
};

const goToImage = (index: number) => {
    activeIndex.value = index;
};

// Keyboard navigation
const handleKeydown = (e: KeyboardEvent) => {
    if (!props.isOpen) return;
    
    if (e.key === 'ArrowRight') nextImage();
    if (e.key === 'ArrowLeft') prevImage();
    if (e.key === 'Escape') emit('close');
};

// Watch for keyboard events
if (typeof window !== 'undefined') {
    window.addEventListener('keydown', handleKeydown);
}
</script>

<template>
    <!-- Modal Overlay -->
    <Transition
        enter-active-class="transition-opacity ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div 
            v-if="isOpen"
            class="fixed inset-0 bg-black/90 backdrop-blur-md z-50 flex items-center justify-center"
            @click="emit('close')"
        >
            <!-- Modal Content -->
            <div 
                class="relative w-full h-full flex flex-col items-center justify-center p-4"
                @click.stop
            >
                <!-- Close Button -->
                <button 
                    @click="emit('close')"
                    class="absolute top-6 right-6 text-white/80 hover:text-white transition z-10"
                >
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Product Name -->
                <div class="absolute top-6 left-6 text-white z-10">
                    <h3 class="text-xl font-semibold">{{ productName }}</h3>
                    <p class="text-sm text-white/60">{{ activeIndex + 1 }} / {{ images.length }}</p>
                </div>

                <!-- Main Image Container -->
                <div class="relative flex items-center justify-center w-full max-w-5xl h-full">
                    <!-- Previous Button -->
                    <button 
                        v-if="images.length > 1"
                        @click="prevImage"
                        class="absolute left-0 z-10 p-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full text-white transition transform hover:scale-110"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <!-- Image -->
                    <div class="flex items-center justify-center max-h-[80vh]">
                        <TransitionGroup
                            enter-active-class="transition-opacity duration-300"
                            enter-from-class="opacity-0"
                            enter-to-class="opacity-100"
                            leave-active-class="transition-opacity duration-300"
                            leave-from-class="opacity-100"
                            leave-to-class="opacity-0"
                        >
                            <img 
                                :key="activeIndex"
                                :src="currentImage" 
                                :alt="`${productName} - Image ${activeIndex + 1}`"
                                class="max-h-[80vh] max-w-full object-contain rounded-lg shadow-2xl"
                            >
                        </TransitionGroup>
                    </div>

                    <!-- Next Button -->
                    <button 
                        v-if="images.length > 1"
                        @click="nextImage"
                        class="absolute right-0 z-10 p-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full text-white transition transform hover:scale-110"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- Thumbnails -->
                <div v-if="images.length > 1" class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex gap-2 max-w-full overflow-x-auto px-4 py-2 bg-black/40 backdrop-blur-md rounded-lg">
                    <button
                        v-for="(img, idx) in images"
                        :key="idx"
                        @click="goToImage(idx)"
                        class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition"
                        :class="idx === activeIndex ? 'border-white ring-2 ring-white/50' : 'border-white/20 hover:border-white/50'"
                    >
                        <img :src="img" :alt="`Thumbnail ${idx + 1}`" class="w-full h-full object-cover">
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>
