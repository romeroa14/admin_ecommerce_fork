<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            // Etiquetas Generales
            ['name' => 'Nuevo', 'type' => 'new', 'color' => '#28a745'],
            ['name' => 'Destacado', 'type' => 'featured', 'color' => '#ffc107'],
            ['name' => 'Popular', 'type' => 'general', 'color' => '#dc3545'],
            ['name' => 'Recomendado', 'type' => 'general', 'color' => '#17a2b8'],
            
            // Promociones
            ['name' => 'Oferta', 'type' => 'promotion', 'color' => '#fd7e14'],
            ['name' => 'Descuento', 'type' => 'promotion', 'color' => '#e83e8c'],
            ['name' => 'Black Friday', 'type' => 'promotion', 'color' => '#000000'],
            ['name' => 'Cyber Monday', 'type' => 'promotion', 'color' => '#6f42c1'],
            
            // Temporadas
            ['name' => 'Verano', 'type' => 'season', 'color' => '#ff6b35'],
            ['name' => 'Invierno', 'type' => 'season', 'color' => '#4a90e2'],
            ['name' => 'Primavera', 'type' => 'season', 'color' => '#7ed321'],
            ['name' => 'Otoño', 'type' => 'season', 'color' => '#f5a623'],
            
            // Características
            ['name' => 'Ecológico', 'type' => 'general', 'color' => '#28a745'],
            ['name' => 'Orgánico', 'type' => 'general', 'color' => '#8bc34a'],
            ['name' => 'Premium', 'type' => 'general', 'color' => '#ffd700'],
            ['name' => 'Básico', 'type' => 'general', 'color' => '#6c757d'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($tag['name'])],
                [
                    'name' => $tag['name'],
                    'slug' => Str::slug($tag['name']),
                    'type' => $tag['type'],
                    'color' => $tag['color'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('✅ Etiquetas creadas exitosamente!');
        $this->command->info('📊 Total: ' . count($tags) . ' etiquetas');
        $this->command->info('🏷️ Tipos: General, Promoción, Temporada, Nuevo, Destacado');
    }
}