<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        // Dólar Americano (USD) - Moneda base
        Currency::create([
            'name' => 'Dólar Americano',
            'code' => 'USD',
            'symbol' => '$',
            'symbol_position' => 'before',
            'decimal_places' => 2,
            'is_active' => true,
            'is_default' => true,
            'exchange_rate' => 1.0000,
            'sort_order' => 10,
        ]);

        // Euro (EUR)
        Currency::create([
            'name' => 'Euro',
            'code' => 'EUR',
            'symbol' => '€',
            'symbol_position' => 'after',
            'decimal_places' => 2,
            'is_active' => true,
            'is_default' => false,
            'exchange_rate' => 0.9200, // 1 USD = 0.92 EUR (aproximado)
            'sort_order' => 20,
        ]);

        // Bolívar Venezolano (VES)
        Currency::create([
            'name' => 'Bolívar Venezolano',
            'code' => 'VES',
            'symbol' => 'Bs',
            'symbol_position' => 'before',
            'decimal_places' => 2,
            'is_active' => true,
            'is_default' => false,
            'exchange_rate' => 36.5000, // 1 USD = 36.50 VES (aproximado)
            'sort_order' => 30,
        ]);

        $this->command->info('✅ Monedas creadas exitosamente!');
        $this->command->info('💰 Total: ' . Currency::count() . ' monedas configuradas');
        $this->command->info('💵 USD (Por defecto), € EUR, Bs VES');
    }
}
