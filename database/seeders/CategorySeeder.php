<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electrónicos', 'slug' => 'electronicos', 'description' => 'Dispositivos electrónicos y tecnología'],
            ['name' => 'Ropa', 'slug' => 'ropa', 'description' => 'Ropa y accesorios de moda'],
            ['name' => 'Hogar', 'slug' => 'hogar', 'description' => 'Artículos para el hogar'],
            ['name' => 'Deportes', 'slug' => 'deportes', 'description' => 'Equipos y accesorios deportivos'],
            ['name' => 'Libros', 'slug' => 'libros', 'description' => 'Libros y material educativo'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']], // Buscar por slug único
                $category // Crear si no existe
            );
        }

        $this->command->info('✅ Categorías creadas exitosamente!');
        $this->command->info('📊 Total: ' . count($categories) . ' categorías');
    }
}