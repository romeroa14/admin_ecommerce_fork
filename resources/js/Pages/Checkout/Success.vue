<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    orderId: [String, Number]
});
</script>

<template>
    <AppLayout>
        <Head title="Pedido Confirmado" />

        <div class="text-center py-16 px-4 sm:px-6 lg:px-8">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">¡Gracias por tu compra!</h1>
            <p class="mt-4 text-lg text-gray-500">Tu pedido #{{ orderId }} ha sido confirmado y está siendo procesado.</p>
            
            <div class="mt-10">
                <Link href="/" class="text-base font-medium text-indigo-600 hover:text-indigo-500">
                    Volver a la tienda <span aria-hidden="true">&rarr;</span>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
