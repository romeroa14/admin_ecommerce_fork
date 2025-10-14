<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple', 'description' => 'Tecnología Apple'],
            ['name' => 'Samsung', 'slug' => 'samsung', 'description' => 'Electrónicos Samsung'],
            ['name' => 'Nike', 'slug' => 'nike', 'description' => 'Ropa y calzado deportivo'],
            ['name' => 'Adidas', 'slug' => 'adidas', 'description' => 'Ropa y calzado deportivo'],
            ['name' => 'Sony', 'slug' => 'sony', 'description' => 'Electrónicos y entretenimiento'],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(
                ['slug' => $brand['slug']], // Buscar por slug único
                $brand // Crear si no existe
            );
        }

        $this->command->info('✅ Marcas creadas exitosamente!');
        $this->command->info('📊 Total: ' . count($brands) . ' marcas');
    }
}