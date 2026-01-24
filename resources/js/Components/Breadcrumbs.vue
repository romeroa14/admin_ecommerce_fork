<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    items: Array<{
        label: string;
        href?: string;
    }>;
}>();
</script>

<template>
    <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
        <Link href="/" class="hover:text-[#F41D27] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </Link>
        
        <template v-for="(item, index) in items" :key="index">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            
            <Link 
                v-if="item.href && index < items.length - 1" 
                :href="item.href" 
                class="hover:text-[#F41D27] transition"
            >
                {{ item.label }}
            </Link>
            <span v-else class="font-medium" :class="index === items.length - 1 ? 'text-gray-900' : ''">
                {{ item.label }}
            </span>
        </template>
    </nav>
</template>
