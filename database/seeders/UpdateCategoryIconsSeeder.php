<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class UpdateCategoryIconsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIcons = [
            'electronica' => '📱',
            'deportes' => '⚽',
            'ropa' => '👕',
            'hogar' => '🏠',
            'juguetes' => '🧸',
            'libros' => '📚',
            'alimentos' => '🍕',
            'belleza' => '💄',
            'tecnologia' => '💻',
            'oficina' => '🖊️',
            'jardin' => '🌿',
            'mascotas' => '🐶',
            'musica' => '🎵',
            'automovil' => '🚗',
            'herramientas' => '🔧',
        ];

        foreach ($categoryIcons as $slug => $icon) {
            Category::where('slug', 'like', "%{$slug}%")
                ->update(['icon' => $icon]);
        }

        // Default icon for categories without specific icon
        Category::whereNull('icon')->update(['icon' => '📦']);
    }
}
