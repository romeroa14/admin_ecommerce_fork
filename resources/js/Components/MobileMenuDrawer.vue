<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    isOpen: boolean;
    categories: any[];
    user: any;
    currencies: any[];
    currentCurrency: any;
    currencyOpen: boolean;
    subcategoryOpen: Record<number, boolean>;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
    (e: 'toggle-currency'): void;
    (e: 'toggle-subcategory', categoryId: number): void;
    (e: 'change-currency', currencyId: number): void;
}>();

const route = window.route;
</script>

<template>
    <Teleport to="body">
        <!-- Backdrop -->
        <div
            v-if="isOpen"
            @click="emit('close')"
            class="fixed inset-0 bg-black/50 z-50 transition-opacity"
        ></div>

        <!-- Drawer -->
        <div
            class="fixed top-0 left-0 h-full w-80 max-w-[85vw] bg-white z-50 shadow-2xl transform transition-transform duration-300 ease-in-out overflow-y-auto"
            :class="isOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Drawer Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 bg-[#040054] text-white">
                <span class="font-bold text-lg">Menú</span>
                <button @click="emit('close')" class="p-2 hover:bg-white/10 rounded-full transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Account Links -->
            <div class="border-b border-gray-100">
                <Link
                    :href="user ? route('account.dashboard') : route('login')"
                    @click="emit('close')"
                    class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition"
                >
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="font-medium text-gray-800">{{ user ? 'Mi cuenta' : 'Iniciar Sesión' }}</span>
                </Link>

                <Link
                    href="/favoritos"
                    @click="emit('close')"
                    class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition"
                >
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <span class="font-medium text-gray-800">Favoritos</span>
                </Link>
            </div>

            <!-- Currency Selector (in drawer) -->
            <div v-if="currencies && currencies.length > 0" class="border-b border-gray-100">
                <button
                    @click="emit('toggle-currency')"
                    class="flex items-center justify-between w-full px-4 py-3 hover:bg-gray-50 transition font-medium text-gray-800"
                >
                    <span class="flex items-center gap-2">
                        <span class="text-lg">{{ currentCurrency?.symbol || '$' }}</span>
                        Moneda: {{ currentCurrency?.code || 'USD' }}
                    </span>
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': currencyOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div v-show="currencyOpen" class="bg-gray-50 border-t border-gray-100">
                    <button
                        v-for="curr in currencies"
                        :key="curr.id"
                        @click="emit('change-currency', curr.id); emit('close')"
                        class="w-full text-left px-6 py-2.5 hover:bg-gray-100 transition flex items-center justify-between text-gray-700"
                        :class="{ 'bg-gray-100 text-[#F41D27] font-bold': currentCurrency?.id === curr.id }"
                    >
                        <span>{{ curr.name }}</span>
                        <span class="font-bold bg-gray-200 rounded px-1 text-xs">{{ curr.symbol }}</span>
                    </button>
                </div>
            </div>

            <!-- Categories Navigation -->
            <div class="py-2">
                <div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Categorías</div>
                <Link
                    href="/"
                    @click="emit('close')"
                    class="block px-4 py-3 hover:bg-gray-50 transition font-medium text-gray-800"
                >
                    Inicio
                </Link>
                <div v-for="cat in categories" :key="cat.id">
                    <div
                        @click="emit('toggle-subcategory', cat.id)"
                        class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition font-medium text-gray-800 cursor-pointer"
                    >
                        <span>{{ cat.name }}</span>
                        <svg
                            v-if="cat.subcategories && cat.subcategories.length"
                            class="w-4 h-4 transition-transform"
                            :class="{ 'rotate-180': subcategoryOpen[cat.id] }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    <!-- Subcategories -->
                    <div v-if="cat.subcategories && cat.subcategories.length && subcategoryOpen[cat.id]" class="bg-gray-50 border-t border-gray-100">
                        <Link
                            v-for="child in cat.subcategories"
                            :key="child.id"
                            :href="`/subcategories/${child.slug}`"
                            @click="emit('close')"
                            class="block px-8 py-2.5 hover:bg-gray-100 transition text-gray-600 text-sm"
                        >
                            {{ child.name }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
