<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { computed, onMounted } from 'vue';

const props = defineProps({
    orderId: [String, Number],
    orderData: {
        type: Object,
        required: true
    }
});

// @ts-ignore
const route = window.route;

const whatsappUrl = computed(() => {
    const order = props.orderData;
    const address = order.shipping_address;
    const items = order.items || [];
    
    let message = `🚀 *NUEVO PEDIDO EN EQUIPO CONTAINER*\n`;
    message += `------------------------------------------\n`;
    message += `*Pedido:* #${props.orderId}\n`;
    message += `*Cliente:* ${address.first_name} ${address.last_name}\n`;
    message += `*Email:* ${address.email}\n`;
    message += `*Teléfono:* ${address.phone}\n`;
    message += `*Dirección:* ${address.address_line_1}, ${address.city}\n`;
    message += `*Método:* ${order.shipping_method === 'express' ? 'Retiro en Tienda' : 'Envío a Domicilio'}\n`;
    message += `------------------------------------------\n`;
    message += `*PRODUCTOS:*\n`;
    
    items.forEach((item: any) => {
        message += `• ${item.product?.name} (x${item.quantity}) - ${item.price}\n`;
    });
    
    message += `------------------------------------------\n`;
    message += `*TOTAL A PAGAR:* ${order.total}\n`;
    message += `------------------------------------------\n`;
    message += `_Por favor, envíeme los datos bancarios para completar el pago._`;

    const phoneNumber = '584123816330'; // Sin el + para la URL de API
    return `https://api.whatsapp.com/send?phone=${phoneNumber}&text=${encodeURIComponent(message)}`;
});

onMounted(() => {
    // Redirección automática después de 3 segundos
    setTimeout(() => {
        window.open(whatsappUrl.value, '_blank');
    }, 3000);
});
</script>

<template>
    <AppLayout>
        <Head title="Pedido Confirmado" />

        <div class="max-w-3xl mx-auto text-center py-16 px-4 sm:px-6 lg:px-8 bg-white my-10 rounded-2xl shadow-xl border border-gray-100">
            <div class="inline-flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-8">
                <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h1 class="text-4xl font-black text-[#040054] tracking-tight sm:text-5xl mb-4">¡Pedido Recibido!</h1>
            <p class="text-xl text-gray-600 mb-8">Tu pedido <span class="font-bold text-[#F41D27]">#{{ orderId }}</span> ha sido registrado correctamente.</p>
            
            <div class="bg-green-50 rounded-xl p-8 border border-green-100 mb-10">
                <h2 class="text-lg font-bold text-green-800 mb-4">¡Paso Final!</h2>
                <p class="text-green-700 mb-6">Para concretar el pago y coordinar la entrega, debes enviar el resumen de tu compra a nuestro WhatsApp oficial.</p>
                
                <a 
                    :href="whatsappUrl" 
                    target="_blank"
                    class="inline-flex items-center gap-3 bg-[#25D366] hover:bg-[#128C7E] text-white text-lg font-bold py-4 px-10 rounded-full shadow-lg transform transition hover:scale-105 active:scale-95"
                >
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Confirmar por WhatsApp
                </a>
                
                <p class="mt-4 text-xs text-green-600 italic">Te estamos redirigiendo automáticamente en unos segundos...</p>
            </div>
            
            <div class="space-y-4">
                <Link href="/" class="block text-base font-bold text-[#040054] hover:text-[#F41D27] transition">
                    ← Volver a la tienda
                </Link>
                <Link :href="route('account.orders')" class="block text-sm text-gray-500 hover:text-gray-700 underline">
                    Ver el estado de mis pedidos
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
