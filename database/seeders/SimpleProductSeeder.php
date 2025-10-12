<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class SimpleProductSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener categoría y marca existentes
        $category = Category::first();
        $brand = Brand::first();

        if (!$category || !$brand) {
            $this->command->error('No hay categorías o marcas disponibles');
            return;
        }

        // Crear productos simples
        $products = [
            [
                'name' => 'Producto Test 1',
                'slug' => 'producto-test-1',
                'sku' => 'PT-001',
                'description' => 'Descripción del producto test 1',
                'price' => 29.99,
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'status' => 'active',
                'is_featured' => true,
                'stock' => 50,
            ],
            [
                'name' => 'Producto Test 2',
                'slug' => 'producto-test-2',
                'sku' => 'PT-002',
                'description' => 'Descripción del producto test 2',
                'price' => 49.99,
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'status' => 'active',
                'is_featured' => false,
                'stock' => 30,
            ],
            [
                'name' => 'Producto Test 3',
                'slug' => 'producto-test-3',
                'sku' => 'PT-003',
                'description' => 'Descripción del producto test 3',
                'price' => 79.99,
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'status' => 'active',
                'is_featured' => true,
                'stock' => 20,
            ],
            [
                'name' => 'Producto Test 4',
                'slug' => 'producto-test-4',
                'sku' => 'PT-004',
                'description' => 'Descripción del producto test 4',
                'price' => 99.99,
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'status' => 'active',
                'is_featured' => false,
                'stock' => 15,
            ],
            [
                'name' => 'Producto Test 5',
                'slug' => 'producto-test-5',
                'sku' => 'PT-005',
                'description' => 'Descripción del producto test 5',
                'price' => 149.99,
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'status' => 'active',
                'is_featured' => true,
                'stock' => 10,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $this->command->info('✅ Productos de prueba creados exitosamente!');
        $this->command->info('📊 Total: ' . count($products) . ' productos');
    }
}
