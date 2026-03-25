<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    private function getCart()
    {
        $sessionId = Session::getId();
        $user = auth()->user();

        $query = Cart::query();

        if ($user) {
            $query->where('user_id', $user->id);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->first();
    }

    public function init()
    {
        $cart = $this->getCart();
        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        if (auth()->check()) {
            return redirect()->route('checkout.address');
        }

        // If guest, send to login but indicate intent to checkout
        return redirect()->route('login', ['checkout' => 1]);
    }

    public function address()
    {
        $cart = $this->getCart();
        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $sessionData = Session::get('checkout_data', []);
        
        return Inertia::render('Checkout/Address', [
            'cart' => $cart,
            'items' => $this->enrichItemsWithProducts($cart->items),
            'totals' => $cart->getTotals(),
            'defaultAddress' => $sessionData['address'] ?? (auth()->user() ? auth()->user()->toArray() : [])
        ]);
    }

    private function enrichItemsWithProducts($items)
    {
        if (empty($items)) return [];
        
        $productIds = array_column($items, 'product_id');
        $products = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');
        
        foreach ($items as &$item) {
            $product = $products->get($item['product_id']);
            $item['product'] = $product ? $product->toArray() : null;
        }
        
        \Illuminate\Support\Facades\Log::info('Checkout Items Enriched:', ['items' => $items]);
        
        return $items;
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
        ]);

        $sessionData = Session::get('checkout_data', []);
        $sessionData['address'] = $request->except('_token');
        Session::put('checkout_data', $sessionData);

        return redirect()->route('checkout.shipping');
    }

    public function shipping()
    {
        $cart = $this->getCart();
        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $sessionData = Session::get('checkout_data', []);
        if (empty($sessionData['address'])) {
            return redirect()->route('checkout.address');
        }

        return Inertia::render('Checkout/Shipping', [
            'cart' => $cart,
            'items' => $this->enrichItemsWithProducts($cart->items),
            'totals' => $cart->getTotals(),
            'shipping' => $sessionData['shipping'] ?? [],
            'shippingMethods' => \App\Models\Shipping::active()->ordered()->get()->map(function($sm) {
                return array_merge($sm->toArray(), [
                    'estimated_delivery' => $sm->getEstimatedDeliveryDays()
                ]);
            })
        ]);
    }

    public function storeShipping(Request $request)
    {
        $request->validate([
            'shipping_method' => 'required|string',
        ]);

        $sessionData = Session::get('checkout_data', []);
        
        $shipping = \App\Models\Shipping::where('code', $request->shipping_method)->first();
        
        $sessionData['shipping'] = $request->except('_token');
        if ($shipping) {
            $sessionData['shipping']['shipping_id'] = $shipping->id;
        }
        
        Session::put('checkout_data', $sessionData);

        return redirect()->route('checkout.payment');
    }

    public function payment()
    {
        $cart = $this->getCart();
        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $sessionData = Session::get('checkout_data', []);
        if (empty($sessionData['shipping'])) {
            return redirect()->route('checkout.shipping');
        }

        return Inertia::render('Checkout/Payment', [
            'cart' => $cart,
            'items' => $this->enrichItemsWithProducts($cart->items),
            'totals' => $cart->getTotals(),
            'sessionData' => $sessionData,
            'shippingMethods' => \App\Models\Shipping::active()->ordered()->get(),
            'paymentMethods' => \App\Models\PaymentMethod::active()->ordered()->get()
        ]);
    }

    public function storePayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
        ]);

        $cart = $this->getCart();
        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        $sessionData = Session::get('checkout_data', []);
        
        if (empty($sessionData['address']) || empty($sessionData['shipping'])) {
            return redirect()->route('checkout.address');
        }

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $addressData = $sessionData['address'];
            
            // Create user logic for guests if needed, or leave user_id as null
            
            $address = Address::create([
                'user_id' => $user ? $user->id : null,
                'first_name' => $addressData['first_name'],
                'last_name' => $addressData['last_name'],
                'email' => $addressData['email'],
                'address_line_1' => $addressData['address'],
                'city' => $addressData['city'],
                'postal_code' => $addressData['postal_code'],
                'phone' => $addressData['phone'],
                'country' => 'Venezuela'
            ]);

            $order = Order::createFromCart($cart, [
                'shipping_address_id' => $address->id,
                'billing_address_id' => $address->id,
                'status' => 'pending',
                'user_id' => $user ? $user->id : null,
                'shipping_id' => $sessionData['shipping']['shipping_id'] ?? null,
            ]);

            $paymentMethodCode = $request->payment_method ?? 'whatsapp';
            $pm = \App\Models\PaymentMethod::where('code', $paymentMethodCode)->first();

            $payment = \App\Models\Payment::create([
                'order_id' => $order->id,
                'payment_method_id' => $pm ? $pm->id : null,
                'payment_method' => $paymentMethodCode,
                'amount' => $order->total_amount,
                'status' => 'pending'
            ]);

            $order->update(['payment_id' => $payment->id]);

            // Clear cart & session
            $cart->items = [];
            $cart->save();
            Session::forget('checkout_data');

            DB::commit();

            return redirect()->route('checkout.success', $order->id);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing checkout: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al procesar el pago. Por favor, inténtelo de nuevo.']);
        }
    }
}
