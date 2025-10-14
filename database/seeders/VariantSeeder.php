<?php

namespace Database\Seeders;

use App\Models\Variant;
use App\Models\VariantGroup;
use Illuminate\Database\Seeder;

class VariantSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener los grupos de variantes
        $colorGroup = VariantGroup::where('slug', 'color')->first();
        $tallaGroup = VariantGroup::where('slug', 'talla')->first();
        $materialGroup = VariantGroup::where('slug', 'material')->first();
        $estiloGroup = VariantGroup::where('slug', 'estilo')->first();

        $variants = [
            // Tallas
            ['name' => 'XS', 'variant_group_id' => $tallaGroup->id, 'description' => 'Extra Small', 'sort_order' => 1],
            ['name' => 'S', 'variant_group_id' => $tallaGroup->id, 'description' => 'Small', 'sort_order' => 2],
            ['name' => 'M', 'variant_group_id' => $tallaGroup->id, 'description' => 'Medium', 'sort_order' => 3],
            ['name' => 'L', 'variant_group_id' => $tallaGroup->id, 'description' => 'Large', 'sort_order' => 4],
            ['name' => 'XL', 'variant_group_id' => $tallaGroup->id, 'description' => 'Extra Large', 'sort_order' => 5],
            ['name' => 'XXL', 'variant_group_id' => $tallaGroup->id, 'description' => 'Double Extra Large', 'sort_order' => 6],
            
            // Colores
            ['name' => 'Rojo', 'variant_group_id' => $colorGroup->id, 'description' => 'Color rojo', 'sort_order' => 1],
            ['name' => 'Azul', 'variant_group_id' => $colorGroup->id, 'description' => 'Color azul', 'sort_order' => 2],
            ['name' => 'Verde', 'variant_group_id' => $colorGroup->id, 'description' => 'Color verde', 'sort_order' => 3],
            ['name' => 'Negro', 'variant_group_id' => $colorGroup->id, 'description' => 'Color negro', 'sort_order' => 4],
            ['name' => 'Blanco', 'variant_group_id' => $colorGroup->id, 'description' => 'Color blanco', 'sort_order' => 5],
            ['name' => 'Gris', 'variant_group_id' => $colorGroup->id, 'description' => 'Color gris', 'sort_order' => 6],
            ['name' => 'Amarillo', 'variant_group_id' => $colorGroup->id, 'description' => 'Color amarillo', 'sort_order' => 7],
            ['name' => 'Rosa', 'variant_group_id' => $colorGroup->id, 'description' => 'Color rosa', 'sort_order' => 8],
            
            // Materiales
            ['name' => 'Algodón', 'variant_group_id' => $materialGroup->id, 'description' => '100% Algodón', 'sort_order' => 1],
            ['name' => 'Poliester', 'variant_group_id' => $materialGroup->id, 'description' => '100% Poliéster', 'sort_order' => 2],
            ['name' => 'Lino', 'variant_group_id' => $materialGroup->id, 'description' => '100% Lino', 'sort_order' => 3],
            ['name' => 'Seda', 'variant_group_id' => $materialGroup->id, 'description' => '100% Seda', 'sort_order' => 4],
            ['name' => 'Cuero', 'variant_group_id' => $materialGroup->id, 'description' => 'Cuero genuino', 'sort_order' => 5],
            ['name' => 'Denim', 'variant_group_id' => $materialGroup->id, 'description' => 'Denim/Mezclilla', 'sort_order' => 6],
            
            // Estilos
            ['name' => 'Casual', 'variant_group_id' => $estiloGroup->id, 'description' => 'Estilo casual', 'sort_order' => 1],
            ['name' => 'Formal', 'variant_group_id' => $estiloGroup->id, 'description' => 'Estilo formal', 'sort_order' => 2],
            ['name' => 'Deportivo', 'variant_group_id' => $estiloGroup->id, 'description' => 'Estilo deportivo', 'sort_order' => 3],
            ['name' => 'Elegante', 'variant_group_id' => $estiloGroup->id, 'description' => 'Estilo elegante', 'sort_order' => 4],
        ];

        foreach ($variants as $variant) {
            Variant::firstOrCreate(
                [
                    'name' => $variant['name'],
                    'variant_group_id' => $variant['variant_group_id']
                ],
                [
                    'name' => $variant['name'],
                    'variant_group_id' => $variant['variant_group_id'],
                    'description' => $variant['description'],
                    'status' => 'active',
                    'sort_order' => $variant['sort_order'],
                ]
            );
        }

        $this->command->info('✅ Variantes genéricas creadas exitosamente!');
        $this->command->info('📊 Total: ' . count($variants) . ' variantes');
    }
}