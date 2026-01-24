<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
</script>

<template>
    <AppLayout>
        <Head title="Mi Cuenta" />

        <div class="bg-gray-50 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-extrabold text-[#040054]">
                        Hola, {{ user?.name }}!
                    </h1>
                    <p class="mt-2 text-gray-600">Gestiona tu cuenta y revisa tus pedidos</p>
                </div>

                <!-- Account Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                        <div class="flex items-center mb-4">
                            <div class="w-16 h-16 bg-[#040054] text-white rounded-full flex items-center justify-center text-2xl font-bold">
                                {{ user?.name?.charAt(0).toUpperCase() }}
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-bold text-gray-900">{{ user?.name }}</h3>
                                <p class="text-sm text-gray-600">{{ user?.email }}</p>
                            </div>
                        </div>
                        <button class="w-full mt-4 px-4 py-2 border border-[#040054] text-[#040054] rounded-lg hover:bg-[#040054] hover:text-white transition font-medium">
                            Editar Perfil
                        </button>
                    </div>

                    <!-- Orders Card -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Mis Pedidos</h3>
                            <svg class="w-6 h-6 text-[#F41D27]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Rastrea y gestiona tus pedidos</p>
                        <a href="/mis-pedidos" class="inline-block px-4 py-2 bg-[#F41D27] text-white rounded-lg hover:bg-red-600 transition font-medium">
                            Ver Pedidos
                        </a>
                    </div>

                    <!-- Wishlist Card -->
                    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Lista de Deseos</h3>
                            <svg class="w-6 h-6 text-[#F41D27]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Guarda tus productos favoritos</p>
                        <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                            Ver Lista
                        </button>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="mt-8 bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Actividad Reciente</h3>
                    <div class="text-center py-12 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="mt-4">No hay actividad reciente</p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
