<script setup lang="ts">
import { Head, useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { getProductImage } from '@/composables/useProductImage';

const props = defineProps({
    cart: Object,
    items: Array as () => any[],
    totals: {
        type: Object as () => any,
        default: () => ({ total: 0 })
    },
    sessionData: Object as () => any,
    shippingMethods: {
        type: Array as () => any[],
        default: () => []
    },
});

// @ts-ignore
const route = window.route;

const form = useForm({});

const submit = () => {
    console.log('Iniciando proceso de pago y confirmación...');
    console.log('Datos de la sesión:', props.sessionData);
    form.post(route('checkout.payment.store'), {
        onSuccess: () => {
            console.log('Pago procesado correctamente, redirigiendo a éxito...');
        },
        onError: (errors: any) => {
            console.error('Error al procesar el pago:', errors);
        },
        onFinish: () => {
            console.log('Petición de pago finalizada.');
        }
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Pago y Confirmación" />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12 bg-white min-h-screen">
            <!-- Navigation steps -->
            <nav class="mb-8" aria-label="Progress">
                <ol role="list" class="flex space-x-8 justify-center mb-10">
                    <li class="flex items-center">
                        <Link href="/checkout/address" class="text-[#040054] hover:text-[#F41D27] font-bold text-lg transition">1. Dirección</Link>
                        <svg class="h-5 w-5 ml-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg>
                    </li>
                    <li class="flex items-center">
                        <Link href="/checkout/shipping" class="text-[#040054] hover:text-[#F41D27] font-bold text-lg transition">2. Envío</Link>
                        <svg class="h-5 w-5 ml-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" /></svg>
                    </li>
                    <li class="flex items-center">
                        <span class="text-[#F41D27] font-bold text-lg">3. Pago</span>
                    </li>
                </ol>
            </nav>

            <div class="lg:grid lg:grid-cols-12 lg:gap-x-12 xl:gap-x-16">
                <div class="lg:col-span-7 space-y-8">
                    <h2 class="text-2xl font-bold text-[#040054] mb-4">Información de Pago</h2>
                    
                    <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4">
                        <p class="font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            Pago vía WhatsApp
                        </p>
                        <p class="mt-2 text-sm text-blue-700">Por los momentos, todas nuestras compras se gestionan exclusivamente vía WhatsApp. Una vez hagas clic en el botón de confirmar, recibiremos tu pedido y un asesor te contactará con los métodos de pago (Zelle, Pago Móvil, etc.) para finiquitar tu compra.</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 border-b pb-4 mb-4">Resumen de tus Datos</h3>
                        <div class="space-y-3 text-sm text-gray-700">
                            <p><strong class="font-semibold text-gray-900">Contacto:</strong> {{ sessionData?.address?.first_name }} {{ sessionData?.address?.last_name }}</p>
                            <p><strong class="font-semibold text-gray-900">Email:</strong> {{ sessionData?.address?.email }}</p>
                            <p><strong class="font-semibold text-gray-900">Teléfono:</strong> {{ sessionData?.address?.phone }}</p>
                            <p><strong class="font-semibold text-gray-900">Dirección a enviar:</strong> {{ sessionData?.address?.address }}, {{ sessionData?.address?.city }} ({{ sessionData?.address?.postal_code }})</p>
                            <p><strong class="font-semibold text-gray-900">Tipo de entrega:</strong> {{ shippingMethods.find(m => m.code === sessionData?.shipping?.shipping_method)?.name || sessionData?.shipping?.shipping_method }}</p>
                        </div>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="mt-8 flex justify-between items-center">
                            <Link href="/checkout/shipping" class="text-sm font-semibold text-gray-600 hover:text-[#040054] flex flex-row items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                Volver a Envíos
                            </Link>
                            <button type="submit" :disabled="form.processing" class="bg-green-600 flex items-center gap-2 hover:bg-green-700 border border-transparent rounded-lg shadow-md py-4 px-8 text-lg font-bold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600 transition">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>    
                                Confirmar Pedido en el WhatsApp
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
                                <img :src="item.product ? getProductImage(item.product) : '/storage/placeholder.png'" class="h-full w-full object-cover object-center">
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
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-600">Subtotal</dt>
                            <dd class="font-bold text-gray-900">{{ $formatCurrency(totals.subtotal) }}</dd>
                        </div>
                        <div v-if="totals.discount_amount > 0" class="flex items-center justify-between text-sm">
                            <dt class="text-gray-600 flex items-center gap-1">
                                Descuentos
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                            </dt>
                            <dd class="font-bold text-green-600">-{{ $formatCurrency(totals.discount_amount) }}</dd>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <dt class="text-gray-600">Cargo de envío</dt>
                            <dd class="font-bold text-gray-900">
                                <template v-if="shippingMethods.find(m => m.code === sessionData?.shipping?.shipping_method)?.base_price > 0">
                                    {{ $formatCurrency(shippingMethods.find(m => m.code === sessionData?.shipping?.shipping_method)?.base_price) }}
                                </template>
                                <template v-else>
                                    <span class="text-[#1CA862]">Gratis</span>
                                </template>
                            </dd>
                        </div>
                        <div class="border-t border-gray-200 pt-4 flex items-center justify-between font-bold text-xl mt-4">
                            <dt class="text-[#040054]">Total a Pagar</dt>
                            <dd class="text-[#F41D27]">
                                {{ $formatCurrency(totals.total + Number(shippingMethods.find(m => m.code === sessionData?.shipping?.shipping_method)?.base_price || 0)) }}
                            </dd>
                        </div>
                    </dl>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
