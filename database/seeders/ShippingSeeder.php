<?php

namespace Database\Seeders;

use App\Models\Shipping;
use Illuminate\Database\Seeder;

class ShippingSeeder extends Seeder
{
    public function run(): void
    {
        $shippingMethods = [
            [
                'name' => 'Envío Estándar',
                'code' => 'standard',
                'description' => 'Envío estándar con entrega en 3-5 días laborables',
                'base_price' => 4.95,
                'price_per_kg' => 0.50,
                'free_shipping_threshold' => 50.00,
                'estimated_days_min' => 3,
                'estimated_days_max' => 5,
                'zones' => ['España', 'Portugal'],
                'weight_limits' => ['max' => 30],
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Envío Express',
                'code' => 'express',
                'description' => 'Envío rápido con entrega en 1-2 días laborables',
                'base_price' => 9.95,
                'price_per_kg' => 1.00,
                'free_shipping_threshold' => 100.00,
                'estimated_days_min' => 1,
                'estimated_days_max' => 2,
                'zones' => ['España', 'Portugal', 'Francia'],
                'weight_limits' => ['max' => 20],
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Envío Gratuito',
                'code' => 'free',
                'description' => 'Envío gratuito para pedidos superiores a €50',
                'base_price' => 0.00,
                'price_per_kg' => 0.00,
                'free_shipping_threshold' => 50.00,
                'estimated_days_min' => 5,
                'estimated_days_max' => 7,
                'zones' => ['España'],
                'weight_limits' => ['max' => 10],
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Recogida en Tienda',
                'code' => 'pickup',
                'description' => 'Recogida gratuita en nuestras tiendas físicas',
                'base_price' => 0.00,
                'price_per_kg' => 0.00,
                'free_shipping_threshold' => null,
                'estimated_days_min' => 0,
                'estimated_days_max' => 0,
                'zones' => ['España'],
                'weight_limits' => null,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($shippingMethods as $method) {
            Shipping::firstOrCreate(
                ['code' => $method['code']],
                $method
            );
        }

        $this->command->info('✅ Métodos de envío creados exitosamente!');
        $this->command->info('📦 Total: ' . count($shippingMethods) . ' métodos de envío');
    }
}