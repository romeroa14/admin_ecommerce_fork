<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import CheckoutLayout from '@/Layouts/CheckoutLayout.vue';

const props = defineProps({
    cart: Object,
    items: {
        type: Array as () => any[],
        default: () => []
    },
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
    <CheckoutLayout
        formId="address-form"
        :items="items"
        :totals="totals"
        buttonText="Continuar a Envíos"
        :buttonLoading="form.processing"
    >
        <Head title="Dirección de Envío" />

        <template #navigation>
            <nav class="mb-12" aria-label="Progress">
                <ol role="list" class="flex space-x-4 md:space-x-8 text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-400">
                    <li class="flex items-center text-gray-900 border-b-2 border-gray-900 pb-1">
                        <span>1. Dirección</span>
                        <svg class="h-4 w-4 ml-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg>
                    </li>
                    <li class="flex items-center">
                        <span>2. Envío</span>
                        <svg class="h-4 w-4 ml-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg>
                    </li>
                    <li class="flex items-center">
                        <span>3. Pago</span>
                    </li>
                </ol>
            </nav>
        </template>

        <h2 class="text-2xl font-black text-gray-900 mb-8 tracking-tight">Información de contacto y envío</h2>

        <form id="address-form" @submit.prevent="submit" class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
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

        </form>
    </CheckoutLayout>
</template>
