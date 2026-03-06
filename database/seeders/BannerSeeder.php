<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Gran Venta de Temporada',
                'description' => 'Hasta 50% de descuento en productos seleccionados. ¡No te lo pierdas!',
                'image' => 'banners/banner-sale.svg',
                'link' => '/products',
                'button_text' => 'Ver Ofertas',
                'position' => 'home_hero',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Nuevos Equipos Disponibles',
                'description' => 'Descubre nuestra nueva línea de equipos de última generación para tu negocio.',
                'image' => 'banners/banner-new.svg',
                'link' => '/products',
                'button_text' => 'Explorar',
                'position' => 'home_hero',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Envío Gratis en Compras +$50',
                'description' => 'Aprovecha el envío gratuito en todas las compras mayores a $50.',
                'image' => 'banners/banner-shipping.svg',
                'link' => '/products',
                'button_text' => 'Comprar Ahora',
                'position' => 'home_hero',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        // Create placeholder banner images in storage
        foreach ($banners as $bannerData) {
            $imagePath = $bannerData['image'];
            $fullPath = storage_path('app/public/' . $imagePath);
            $dir = dirname($fullPath);
            
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            if (!file_exists($fullPath)) {
                // Create gradient SVG banner placeholders
                $colors = match($bannerData['order']) {
                    1 => ['#F41D27', '#ff6b35'],
                    2 => ['#040054', '#1a0a7e'],
                    3 => ['#059669', '#10b981'],
                    default => ['#6366f1', '#8b5cf6'],
                };
                
                $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1920" height="600" viewBox="0 0 1920 600">
  <defs>
    <linearGradient id="bg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:{$colors[0]}"/>
      <stop offset="100%" style="stop-color:{$colors[1]}"/>
    </linearGradient>
  </defs>
  <rect width="1920" height="600" fill="url(#bg)"/>
  <circle cx="1600" cy="300" r="250" fill="white" fill-opacity="0.08"/>
  <circle cx="1450" cy="450" r="180" fill="white" fill-opacity="0.05"/>
  <circle cx="200" cy="100" r="120" fill="white" fill-opacity="0.06"/>
</svg>
SVG;
                file_put_contents($fullPath, $svg);
            }
            
            Banner::firstOrCreate(
                ['title' => $bannerData['title']],
                $bannerData
            );
        }

        $this->command->info('Banners seeded successfully!');
    }
}
