<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

// @ts-ignore
const route = window.route;

// Define the form with initial values
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Crear Cuenta" />

        <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-extrabold text-[#040054]">
                        Crea tu cuenta
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        ¿Ya tienes cuenta?
                        <Link href="/login" class="font-medium text-[#F41D27] hover:underline">
                            Inicia sesión aquí
                        </Link>
                    </p>
                </div>

                <!-- Registration Form -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name Field -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre Completo
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                autocomplete="name"
                                required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#040054] focus:border-transparent transition"
                                placeholder="Juan Pérez"
                            />
                            <div v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Correo Electrónico
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#040054] focus:border-transparent transition"
                                placeholder="tu@email.com"
                            />
                            <div v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Contraseña
                            </label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                autocomplete="new-password"
                                required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#040054] focus:border-transparent transition"
                                placeholder="Mínimo 8 caracteres"
                            />
                            <div v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                                {{ form.errors.password }}
                            </div>
                        </div>

                        <!-- Password Confirmation Field -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmar Contraseña
                            </label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                autocomplete="new-password"
                                required
                                class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#040054] focus:border-transparent transition"
                                placeholder="Repite tu contraseña"
                            />
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input
                                    id="terms"
                                    type="checkbox"
                                    required
                                    class="h-4 w-4 text-[#040054] focus:ring-[#040054] border-gray-300 rounded"
                                />
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="terms" class="text-gray-600">
                                    Acepto los
                                    <a href="#" class="font-medium text-[#F41D27] hover:underline">Términos y Condiciones</a>
                                    y la
                                    <a href="#" class="font-medium text-[#F41D27] hover:underline">Política de Privacidad</a>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-[#040054] hover:bg-[#060078] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#040054] transition shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Creando cuenta...' : 'Crear Cuenta' }}
                            </button>
                        </div>
                    </form>

                    <!-- Additional Info -->
                    <div class="mt-6 text-center text-xs text-gray-500">
                        Al registrarte, aceptas recibir correos electrónicos sobre ofertas y novedades.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
