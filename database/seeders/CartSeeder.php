<?php

namespace Database\Seeders;

use App\Models\Cart;
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
        $products = Product::all();
        $variants = Variant::all();

        foreach ($users as $user) {
            // Verificar si el usuario ya tiene un carrito
            $existingCart = Cart::where('user_id', $user->id)->first();
            if ($existingCart) {
                $this->command->info("Usuario {$user->name} ya tiene un carrito (ID: {$existingCart->id})");
                continue;
            }

            // Agregar 2-5 items aleatorios al carrito (máximo los productos disponibles)
            $maxItems = min(5, $products->count());
            $itemsCount = rand(2, $maxItems);
            $selectedProducts = $products->random($itemsCount);
            $items = [];

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                
                // Agregar variantes aleatorias (opcional)
                $variantsForProduct = [];
                if (rand(0, 1)) { // 50% chance de tener variantes
                    $randomVariants = $variants->random(rand(1, 2));
                    foreach ($randomVariants as $variant) {
                        $variantsForProduct[$variant->variantGroup->name ?? 'Color'] = $variant->name;
                    }
                }

                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'discount_percentage' => $product->discount_percentage ?? 0,
                    'variants' => $variantsForProduct,
                    'added_at' => now()->toISOString(),
                ];
            }

            // Crear carrito para cada usuario con items
            $cart = Cart::create([
                'user_id' => $user->id,
                'session_id' => 'session_' . $user->id,
                'items' => $items,
                'expires_at' => now()->addDays(7),
            ]);
            
            $this->command->info("Carrito {$cart->id} creado con " . count($items) . " items para usuario {$user->name}");
        }

        $this->command->info('✅ Carritos creados exitosamente!');
        $this->command->info('📊 Total: ' . $users->count() . ' carritos con items');
    }
}