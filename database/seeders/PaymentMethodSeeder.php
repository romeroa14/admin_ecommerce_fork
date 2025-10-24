<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $paymentMethods = [
            [
                'name' => 'Tarjeta de Crédito',
                'code' => 'credit_card',
                'description' => 'Pago con tarjeta de crédito',
                'is_active' => true,
                'sort_order' => 1,
                'icon' => 'credit-card',
                'color' => 'success',
                'requires_gateway' => true,
                'gateway_config' => [
                    'provider' => 'stripe',
                    'api_key' => 'sk_test_...',
                    'webhook_secret' => 'whsec_...',
                ],
            ],
            [
                'name' => 'Tarjeta de Débito',
                'code' => 'debit_card',
                'description' => 'Pago con tarjeta de débito',
                'is_active' => true,
                'sort_order' => 2,
                'icon' => 'credit-card',
                'color' => 'info',
                'requires_gateway' => true,
                'gateway_config' => [
                    'provider' => 'stripe',
                    'api_key' => 'sk_test_...',
                    'webhook_secret' => 'whsec_...',
                ],
            ],
            [
                'name' => 'PayPal',
                'code' => 'paypal',
                'description' => 'Pago a través de PayPal',
                'is_active' => true,
                'sort_order' => 3,
                'icon' => 'globe-alt',
                'color' => 'warning',
                'requires_gateway' => true,
                'gateway_config' => [
                    'provider' => 'paypal',
                    'client_id' => 'your_client_id',
                    'client_secret' => 'your_client_secret',
                ],
            ],
            [
                'name' => 'Transferencia Bancaria',
                'code' => 'bank_transfer',
                'description' => 'Transferencia bancaria directa',
                'is_active' => true,
                'sort_order' => 4,
                'icon' => 'building-library',
                'color' => 'primary',
                'requires_gateway' => false,
                'gateway_config' => null,
            ],
            [
                'name' => 'Efectivo',
                'code' => 'cash',
                'description' => 'Pago en efectivo',
                'is_active' => true,
                'sort_order' => 5,
                'icon' => 'banknotes',
                'color' => 'gray',
                'requires_gateway' => false,
                'gateway_config' => null,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}
