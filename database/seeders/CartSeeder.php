<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Variant;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener usuarios y productos
        $users = User::take(5)->get();
        $products = Product::take(10)->get();
        $variants = Variant::take(20)->get();

        foreach ($users as $user) {
            // Crear carrito para cada usuario
            $cart = Cart::create([
                'user_id' => $user->id,
                'session_id' => 'session_' . $user->id,
                'subtotal' => 0,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'total' => 0,
                'expires_at' => now()->addDays(7),
            ]);

            // Agregar 2-5 items aleatorios al carrito
            $itemsCount = rand(2, 5);
            $selectedProducts = $products->random($itemsCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                
                // Aplicar descuento aleatorio
                $discount = rand(0, 20) / 100; // 0-20% descuento
                $finalPrice = $price * (1 - $discount);

                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'product_variant_id' => null, // No usar variantes por ahora
                    'quantity' => $quantity,
                    'price' => $finalPrice,
                    'subtotal' => $finalPrice * $quantity,
                ]);
            }

            // Calcular totales del carrito
            $cart->calculateTotals();
        }

        $this->command->info('✅ Carritos creados exitosamente!');
        $this->command->info('📊 Total: ' . $users->count() . ' carritos con items');
    }
}