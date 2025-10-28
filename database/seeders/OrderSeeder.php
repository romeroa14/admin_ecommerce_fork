<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Shipment;
use App\Models\Invoice;
use App\Models\Refund;
use App\Models\Tax;
use App\Models\User;
use App\Models\Product;
use App\Models\Address;
use App\Models\Coupon;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear impuestos
        $taxes = [
            [
                'name' => 'IVA',
                'country' => 'ES',
                'rate' => 21.00,
                'is_compound' => false,
                'priority' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Sales Tax',
                'country' => 'US',
                'state' => 'CA',
                'rate' => 8.25,
                'is_compound' => false,
                'priority' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($taxes as $taxData) {
            Tax::create($taxData);
        }

        // Crear cupones
        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount',
                'description' => '10% off for new customers',
                'type' => 'percentage',
                'amount' => 10.00,
                'min_purchase_amount' => 50.00,
                'usage_limit' => 100,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'SAVE20',
                'name' => 'Save $20',
                'description' => '$20 off orders over $100',
                'type' => 'fixed_cart',
                'amount' => 20.00,
                'min_purchase_amount' => 100.00,
                'usage_limit' => 50,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(1),
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $couponData) {
            Coupon::firstOrCreate(['code' => $couponData['code']], $couponData);
        }

        // Crear usuarios de ejemplo si no existen
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Crear direcciones
        $shippingAddress = Address::create([
            'user_id' => $user->id,
            'type' => 'shipping',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'company' => null,
            'address_line_1' => '123 Main St',
            'address_line_2' => 'Apt 4B',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'US',
            'phone' => '+1-555-123-4567',
            'is_default' => true,
        ]);

        $billingAddress = Address::create([
            'user_id' => $user->id,
            'type' => 'billing',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'company' => null,
            'address_line_1' => '123 Main St',
            'address_line_2' => 'Apt 4B',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'US',
            'phone' => '+1-555-123-4567',
            'is_default' => true,
        ]);

        // Obtener productos existentes
        $products = Product::with('variants')->get();
        
        if ($products->isEmpty()) {
            $this->command->info('No products found. Please run ProductSeeder first.');
            return;
        }

        // Crear pedidos de ejemplo
        $orders = [
            [
                'order_number' => 'ORD-' . str_pad(1, 6, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
                'coupon_id' => Coupon::first()->id,
                'status' => 'confirmed',
                'is_paid' => true,
                'paid_at' => now()->subDays(5),
                'subtotal' => 299.98,
                'discount_amount' => 29.99,
                'tax_amount' => 22.50,
                'shipping_amount' => 9.99,
                'total_amount' => 302.48,
                'customer_notes' => 'Please deliver after 5 PM',
                'admin_notes' => 'Customer requested evening delivery',
                'confirmed_at' => now()->subDays(5),
                'shipped_at' => now()->subDays(3),
                'delivered_at' => now()->subDays(1),
            ],
            [
                'order_number' => 'ORD-' . str_pad(2, 6, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
                'status' => 'processing',
                'is_paid' => true,
                'paid_at' => now()->subDays(3),
                'subtotal' => 149.99,
                'discount_amount' => 0.00,
                'tax_amount' => 12.00,
                'shipping_amount' => 5.99,
                'total_amount' => 167.98,
                'customer_notes' => null,
                'admin_notes' => null,
                'confirmed_at' => now()->subDays(2),
                'shipped_at' => now()->subDays(1),
            ],
            [
                'order_number' => 'ORD-' . str_pad(3, 6, '0', STR_PAD_LEFT),
                'user_id' => $user->id,
                'shipping_address_id' => $shippingAddress->id,
                'billing_address_id' => $billingAddress->id,
                'status' => 'pending',
                'is_paid' => false,
                'paid_at' => null,
                'subtotal' => 79.99,
                'discount_amount' => 0.00,
                'tax_amount' => 6.40,
                'shipping_amount' => 4.99,
                'total_amount' => 91.38,
                'customer_notes' => null,
                'admin_notes' => null,
            ],
        ];

        foreach ($orders as $orderData) {
            $order = Order::create($orderData);

            // Crear items del pedido
            $selectedProducts = $products->random(rand(1, 3));
            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $unitPrice = $product->price;
                $subtotal = $unitPrice * $quantity;
                $discountAmount = $order->discount_amount > 0 ? ($subtotal * 0.1) : 0; // 10% discount
                $taxAmount = $subtotal * 0.08; // 8% tax
                $total = $subtotal - $discountAmount + $taxAmount;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'product_attributes' => null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'tax_amount' => $taxAmount,
                    'total' => $total,
                ]);
            }

            // Crear pago si el pedido está pagado
            if ($order->is_paid) {
                Payment::create([
                    'order_id' => $order->id,
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                    'payment_method' => 'credit_card',
                    'amount' => $order->total_amount,
                    'currency' => 'USD',
                    'status' => 'completed',
                    'payment_details' => [
                        'card_last4' => '4242',
                        'card_brand' => 'visa',
                        'exp_month' => 12,
                        'exp_year' => 2025,
                    ],
                    'paid_at' => $order->confirmed_at,
                ]);
            }

            // Crear envío si el pedido está enviado o entregado
            if (in_array($order->status, ['shipped', 'delivered'])) {
                $carriers = ['FedEx', 'UPS', 'DHL', 'USPS'];
                $carrier = $carriers[array_rand($carriers)];
                
                Shipment::create([
                    'order_id' => $order->id,
                    'tracking_number' => strtoupper(Str::random(12)),
                    'carrier' => $carrier,
                    'service_level' => 'Standard',
                    'status' => $order->status === 'delivered' ? 'delivered' : 'in_transit',
                    'shipping_cost' => $order->shipping_amount,
                    'shipping_address' => $shippingAddress->toArray(),
                    'notes' => 'Package shipped via ' . $carrier,
                    'shipped_at' => $order->shipped_at,
                    'delivered_at' => $order->delivered_at,
                    'estimated_delivery' => $order->shipped_at ? $order->shipped_at->addDays(3) : null,
                ]);
            }

            // Crear factura si el pedido está pagado
            if ($order->is_paid) {
                Invoice::create([
                    'order_id' => $order->id,
                    'invoice_number' => Invoice::generateInvoiceNumber(),
                    'invoice_date' => $order->confirmed_at->toDateString(),
                    'due_date' => $order->confirmed_at->addDays(30)->toDateString(),
                    'subtotal' => $order->subtotal,
                    'tax_amount' => $order->tax_amount,
                    'discount_amount' => $order->discount_amount,
                    'total_amount' => $order->total_amount,
                    'status' => $order->status === 'delivered' ? 'paid' : 'sent',
                    'billing_address' => $billingAddress->toArray(),
                    'notes' => 'Thank you for your purchase!',
                ]);
            }

            // Crear reembolso para el primer pedido (ejemplo)
            if ($order->status === 'delivered' && $order->id === 1) {
                Refund::create([
                    'order_id' => $order->id,
                    'payment_id' => Payment::where('order_id', $order->id)->first()->id,
                    'refund_number' => Refund::generateRefundNumber(),
                    'amount' => 50.00,
                    'type' => 'partial',
                    'status' => 'completed',
                    'reason' => 'Customer requested partial refund for damaged item',
                    'admin_notes' => 'Partial refund processed for damaged product',
                    'processed_at' => now()->subDays(1),
                ]);
            }
        }

        $this->command->info('Orders seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . Order::count() . ' orders');
        $this->command->info('- ' . OrderItem::count() . ' order items');
        $this->command->info('- ' . Payment::count() . ' payments');
        $this->command->info('- ' . Shipment::count() . ' shipments');
        $this->command->info('- ' . Invoice::count() . ' invoices');
        $this->command->info('- ' . Refund::count() . ' refunds');
        $this->command->info('- ' . Tax::count() . ' taxes');
        $this->command->info('- ' . Coupon::count() . ' coupons');
    }
}