<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate');

        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    // ─── T1: markAsShipped() status transitions ───────────────────────────

    public function test_mark_as_shipped_from_confirmed_transitions_to_shipped(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-T1A',
            'user_id' => $this->user->id,
            'status' => 'confirmed',
            'subtotal' => 100,
            'total_amount' => 100,
        ]);

        $result = $order->markAsShipped();

        $this->assertNotNull($result);
        $this->assertEquals('shipped', $result->status);
        $this->assertNotNull($result->shipped_at);

        $fresh = $order->fresh();
        $this->assertEquals('shipped', $fresh->status);
        $this->assertNotNull($fresh->shipped_at);
    }

    public function test_mark_as_shipped_from_pending_throws_exception(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-T1B',
            'user_id' => $this->user->id,
            'status' => 'pending',
            'subtotal' => 100,
            'total_amount' => 100,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Solo se pueden enviar pedidos confirmados');

        $order->markAsShipped();
    }

    // ─── T2: markAsDelivered() status transitions ─────────────────────────

    public function test_mark_as_delivered_from_shipped_transitions_to_delivered(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-T2A',
            'user_id' => $this->user->id,
            'status' => 'shipped',
            'subtotal' => 100,
            'total_amount' => 100,
        ]);

        $result = $order->markAsDelivered();

        $this->assertNotNull($result);
        $this->assertEquals('delivered', $result->status);
        $this->assertNotNull($result->delivered_at);

        $fresh = $order->fresh();
        $this->assertEquals('delivered', $fresh->status);
        $this->assertNotNull($fresh->delivered_at);
    }

    public function test_mark_as_delivered_from_confirmed_throws_exception(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-T2B',
            'user_id' => $this->user->id,
            'status' => 'confirmed',
            'subtotal' => 100,
            'total_amount' => 100,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Solo se pueden entregar pedidos enviados');

        $order->markAsDelivered();
    }

    // ─── T3: Guest checkout (null user_id) ─────────────────────────────────

    public function test_guest_checkout_creates_order_with_null_user_id(): void
    {
        Product::create([
            'name' => 'Test Product',
            'sku' => 'TEST-SKU',
            'slug' => 'test-product',
            'price' => 50.00,
            'stock' => 10,
        ]);

        $cart = Cart::create([
            'session_id' => 'guest-session-123',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'price' => 50.00,
                    'discount_percentage' => 0,
                    'variants' => [],
                    'added_at' => now()->toISOString(),
                ],
            ],
        ]);

        $order = Order::createFromCart($cart, ['user_id' => null]);

        $this->assertNotNull($order);
        $this->assertNull($order->user_id);
        $this->assertEquals('pending', $order->status);

        $fresh = Order::find($order->id);
        $this->assertNull($fresh->user_id);
    }

    public function test_authenticated_checkout_creates_order_with_user_id(): void
    {
        Product::create([
            'name' => 'Test Product 2',
            'sku' => 'TEST-SKU2',
            'slug' => 'test-product-2',
            'price' => 50.00,
            'stock' => 10,
        ]);

        $cart = Cart::create([
            'session_id' => 'auth-session-456',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 1,
                    'price' => 50.00,
                    'discount_percentage' => 0,
                    'variants' => [],
                    'added_at' => now()->toISOString(),
                ],
            ],
        ]);

        $order = Order::createFromCart($cart, ['user_id' => $this->user->id]);

        $this->assertNotNull($order);
        $this->assertEquals($this->user->id, $order->user_id);

        $fresh = Order::find($order->id);
        $this->assertEquals($this->user->id, $fresh->user_id);
    }

    // ─── T4: All statuses preserved ────────────────────────────────────────

    public function test_all_seven_statuses_work_correctly(): void
    {
        $statuses = ['pending', 'processing', 'confirmed', 'shipped', 'delivered', 'cancelled', 'refunded'];

        foreach ($statuses as $i => $status) {
            Order::create([
                'order_number' => 'ORD-STATUS-' . $i,
                'user_id' => $this->user->id,
                'status' => $status,
                'subtotal' => 100,
                'total_amount' => 100,
            ]);
        }

        // All 7 orders exist
        $this->assertEquals(7, Order::count());

        // Status labels resolve correctly
        $pending = Order::where('status', 'pending')->first();
        $this->assertEquals('Pendiente', $pending->getStatusLabelAttribute());

        $shipped = Order::where('status', 'shipped')->first();
        $this->assertEquals('Enviado', $shipped->getStatusLabelAttribute());

        $delivered = Order::where('status', 'delivered')->first();
        $this->assertEquals('Entregado', $delivered->getStatusLabelAttribute());

        $cancelled = Order::where('status', 'cancelled')->first();
        $this->assertEquals('Cancelado', $cancelled->getStatusLabelAttribute());

        $refunded = Order::where('status', 'refunded')->first();
        $this->assertEquals('Reembolsado', $refunded->getStatusLabelAttribute());

        // isInProgress: true for pending/processing/confirmed/shipped
        foreach (['pending', 'processing', 'confirmed', 'shipped'] as $status) {
            $order = Order::where('status', $status)->first();
            $this->assertTrue($order->isInProgress(), "Expected isInProgress() true for status '{$status}'");
        }

        foreach (['delivered', 'cancelled', 'refunded'] as $status) {
            $order = Order::where('status', $status)->first();
            $this->assertFalse($order->isInProgress(), "Expected isInProgress() false for status '{$status}'");
        }

        // isCompleted: true only for delivered
        $this->assertTrue($delivered->isCompleted());
        foreach (['pending', 'processing', 'confirmed', 'shipped', 'cancelled', 'refunded'] as $status) {
            $order = Order::where('status', $status)->first();
            $this->assertFalse($order->isCompleted(), "Expected isCompleted() false for status '{$status}'");
        }

        // isFinalized: true for delivered/cancelled/refunded
        foreach (['delivered', 'cancelled', 'refunded'] as $status) {
            $order = Order::where('status', $status)->first();
            $this->assertTrue($order->isFinalized(), "Expected isFinalized() true for status '{$status}'");
        }

        foreach (['pending', 'processing', 'confirmed', 'shipped'] as $status) {
            $order = Order::where('status', $status)->first();
            $this->assertFalse($order->isFinalized(), "Expected isFinalized() false for status '{$status}'");
        }
    }
}
