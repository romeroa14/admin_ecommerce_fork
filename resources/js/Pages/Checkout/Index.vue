<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    cart: Object,
    items: Array,
    totals: Object,
});

const form = useForm({
    email: '',
    first_name: '',
    last_name: '',
    address: '',
    city: '',
    postal_code: '',
    phone: '',
});

const submit = () => {
    form.post(route('checkout.store'));
};
</script>

<template>
    <AppLayout>
        <Head title="Finalizar Compra" />

        <div class="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-8">Información de Envío</h2>

                <form @submit.prevent="submit" class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input v-model="form.email" type="email" autocomplete="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border">
                        <div v-if="form.errors.email" class="text-red-600 text-xs mt-1">{{ form.errors.email }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input v-model="form.first_name" type="text" autocomplete="given-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border">
                        <div v-if="form.errors.first_name" class="text-red-600 text-xs mt-1">{{ form.errors.first_name }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Apellido</label>
                        <input v-model="form.last_name" type="text" autocomplete="family-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border">
                        <div v-if="form.errors.last_name" class="text-red-600 text-xs mt-1">{{ form.errors.last_name }}</div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Dirección</label>
                        <input v-model="form.address" type="text" autocomplete="street-address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border">
                        <div v-if="form.errors.address" class="text-red-600 text-xs mt-1">{{ form.errors.address }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ciudad</label>
                        <input v-model="form.city" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border">
                        <div v-if="form.errors.city" class="text-red-600 text-xs mt-1">{{ form.errors.city }}</div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Código Postal</label>
                        <input v-model="form.postal_code" type="text" autocomplete="postal-code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border">
                        <div v-if="form.errors.postal_code" class="text-red-600 text-xs mt-1">{{ form.errors.postal_code }}</div>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input v-model="form.phone" type="tel" autocomplete="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-3 border">
                        <div v-if="form.errors.phone" class="text-red-600 text-xs mt-1">{{ form.errors.phone }}</div>
                    </div>

                    <div class="sm:col-span-2 mt-4">
                        <button type="submit" :disabled="form.processing" class="w-full bg-indigo-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Confirmar Pedido - €{{ totals.total }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Preview -->
            <div class="mt-10 lg:mt-0">
                <h2 class="text-lg font-medium text-gray-900">Tu Pedido</h2>
                <div class="mt-4 bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="(item, idx) in items" :key="idx" class="flex py-4">
                            <div class="flex-1 flex flex-col">
                                <div>
                                    <div class="flex justify-between text-base font-medium text-gray-900">
                                        <h3>Producto #{{ item.product_id }}</h3>
                                        <p class="ml-4">€{{ item.price }}</p>
                                    </div>
                                </div>
                                <div class="flex-1 flex items-end justify-between text-sm">
                                    <p class="text-gray-500">Cant: {{ item.quantity }}</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <dl class="border-t border-gray-200 pt-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <dt class="text-sm text-gray-600">Total</dt>
                            <dd class="text-sm font-medium text-gray-900">€{{ totals.total }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
