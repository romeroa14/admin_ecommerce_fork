<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    cart: Object,
    items: Array as () => any[],
    totals: {
        type: Object as () => any,
        default: () => ({ total: 0 })
    },
    defaultAddress: {
        type: Object as () => any,
        default: () => ({})
    }
});

// @ts-ignore
const route = window.route;

const form = useForm({
    email: props.defaultAddress.email || '',
    first_name: props.defaultAddress.first_name || props.defaultAddress.name || '',
    last_name: props.defaultAddress.last_name || '',
    address: props.defaultAddress.address || '',
    city: props.defaultAddress.city || '',
    postal_code: props.defaultAddress.postal_code || '',
    phone: props.defaultAddress.phone || '',
});

const submit = () => {
    form.post(route('checkout.address.store'));
};
</script>

<template>
    <AppLayout>
        <Head title="Dirección de Envío" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 bg-white min-h-screen">
            <!-- Navigation steps -->
            <nav class="mb-8" aria-label="Progress">
                <ol role="list" class="flex space-x-8 justify-center mb-10">
                    <li class="flex items-center">
                        <span class="text-[#F41D27] font-bold text-lg">1. Dirección</span>
                        <svg class="h-5 w-5 ml-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg>
                    </li>
                    <li class="flex items-center">
                        <span class="text-gray-400 font-bold text-lg">2. Envío</span>
                        <svg class="h-5 w-5 ml-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg>
                    </li>
                    <li class="flex items-center">
                        <span class="text-gray-400 font-bold text-lg">3. Pago</span>
                    </li>
                </ol>
            </nav>

            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 xl:gap-x-16">
                <div class="lg:col-span-7">
                    <h2 class="text-2xl font-bold text-[#040054] mb-8">Información de Envío</h2>

                    <form @submit.prevent="submit" class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input v-model="form.email" type="email" autocomplete="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F41D27] focus:ring-[#F41D27] py-3 px-4 border text-sm" placeholder="tu@email.com">
                            <div v-if="form.errors.email" class="text-red-600 text-xs mt-1">{{ form.errors.email }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                            <input v-model="form.first_name" type="text" autocomplete="given-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F41D27] focus:ring-[#F41D27] py-3 px-4 border text-sm">
                            <div v-if="form.errors.first_name" class="text-red-600 text-xs mt-1">{{ form.errors.first_name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Apellido</label>
                            <input v-model="form.last_name" type="text" autocomplete="family-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F41D27] focus:ring-[#F41D27] py-3 px-4 border text-sm">
                            <div v-if="form.errors.last_name" class="text-red-600 text-xs mt-1">{{ form.errors.last_name }}</div>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dirección Completa</label>
                            <input v-model="form.address" type="text" autocomplete="street-address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F41D27] focus:ring-[#F41D27] py-3 px-4 border text-sm" placeholder="Avenida, calle, edificio...">
                            <div v-if="form.errors.address" class="text-red-600 text-xs mt-1">{{ form.errors.address }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ciudad / Estado</label>
                            <input v-model="form.city" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F41D27] focus:ring-[#F41D27] py-3 px-4 border text-sm">
                            <div v-if="form.errors.city" class="text-red-600 text-xs mt-1">{{ form.errors.city }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Código Postal</label>
                            <input v-model="form.postal_code" type="text" autocomplete="postal-code" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F41D27] focus:ring-[#F41D27] py-3 px-4 border text-sm">
                            <div v-if="form.errors.postal_code" class="text-red-600 text-xs mt-1">{{ form.errors.postal_code }}</div>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono de Contacto (con WhatsApp)</label>
                            <input v-model="form.phone" type="tel" autocomplete="tel" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#F41D27] focus:ring-[#F41D27] py-3 px-4 border text-sm" placeholder="+58 412 1234567">
                            <div v-if="form.errors.phone" class="text-red-600 text-xs mt-1">{{ form.errors.phone }}</div>
                        </div>

                        <div class="sm:col-span-2 mt-6 flex justify-end">
                            <button type="submit" :disabled="form.processing" class="w-full sm:w-auto bg-[#F41D27] hover:bg-red-700 border border-transparent rounded-lg shadow-md py-4 px-8 text-lg font-bold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F41D27] transition">
                                Continuar a Envíos
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Order Preview -->
                <div class="mt-10 lg:mt-0 lg:col-span-5 bg-gray-50 border border-gray-200 rounded-xl p-6 lg:p-8 h-fit">
                    <h2 class="text-xl font-bold text-[#040054] mb-6">Resumen del Pedido</h2>
                    <ul role="list" class="divide-y divide-gray-200">
                        <li v-for="(item, idx) in items" :key="idx" class="flex py-4">
                            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 bg-white">
                                <img :src="item.product?.image_url || '/storage/placeholder.png'" class="h-full w-full object-cover object-center">
                            </div>
                            <div class="ml-4 flex flex-1 flex-col justify-center">
                                <div class="flex justify-between text-sm font-bold text-gray-900">
                                    <h3 class="line-clamp-2 pr-2">{{ item.product?.name || `Producto #${item.product_id}` }}</h3>
                                    <p class="ml-4 whitespace-nowrap">{{ $formatCurrency(item.price) }}</p>
                                </div>
                                <p class="text-gray-500 text-sm mt-1">Cant: {{ item.quantity }}</p>
                            </div>
                        </li>
                    </ul>
                    <dl class="border-t border-gray-200 pt-6 mt-6 space-y-4">
                        <div class="flex items-center justify-between font-bold text-lg">
                            <dt class="text-[#040054]">Total Estimado</dt>
                            <dd class="text-[#F41D27]">{{ $formatCurrency(totals.total) }}</dd>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-4">Los costos de envío se calcularán en el siguiente paso.</p>
                    </dl>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
