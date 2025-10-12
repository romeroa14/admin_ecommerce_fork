<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VariantGroup;

class VariantGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Color',
                'slug' => 'color',
                'description' => 'Variantes de color para productos',
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'name' => 'Talla',
                'slug' => 'talla',
                'description' => 'Variantes de talla para productos',
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'name' => 'Material',
                'slug' => 'material',
                'description' => 'Variantes de material para productos',
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'name' => 'Estilo',
                'slug' => 'estilo',
                'description' => 'Variantes de estilo para productos',
                'status' => 'active',
                'sort_order' => 4,
            ],
        ];

        foreach ($groups as $group) {
            VariantGroup::create($group);
        }

        $this->command->info('✅ Grupos de variantes creados exitosamente!');
        $this->command->info('📊 Total: ' . count($groups) . ' grupos');
    }
}