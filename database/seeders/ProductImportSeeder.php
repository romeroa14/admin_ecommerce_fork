<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductImportSeeder extends Seeder
{
    public function run(): void
    {
        // Crear categorías de ejemplo
        $categories = [
            ['name' => 'Electrónicos', 'slug' => 'electronicos'],
            ['name' => 'Computadoras', 'slug' => 'computadoras'],
            ['name' => 'Gaming', 'slug' => 'gaming'],
            ['name' => 'Audio', 'slug' => 'audio'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Crear marcas de ejemplo
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple'],
            ['name' => 'Samsung', 'slug' => 'samsung'],
            ['name' => 'Sony', 'slug' => 'sony'],
        ];

        foreach ($brands as $brandData) {
            Brand::firstOrCreate(
                ['slug' => $brandData['slug']],
                $brandData
            );
        }
    }
}
