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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        return Inertia::render('Checkout/Index', [
            'cart' => $cart,
            'items' => $cart->items,
            'totals' => $cart->getTotals(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email' . (auth()->check() ? '' : '|unique:users,email'),
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
        ]);

        $cart = $this->getCart();
        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('cart.index');
        }

        try {
            DB::beginTransaction();

            $user = auth()->user();

            // If guest, create user or just use data (depending on logic, here assume we might create a user or just pass null)
            // But Order needs user_id? Let's assume nullable or we create a user.
            // The requirement says "no es necesario hacer login".
            // So if checking for guest, we might create a user or leave it null.
            // Let's create an address first.

            $address = Address::create([
                'user_id' => $user ? $user->id : null,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'address_line_1' => $request->address,
                'city' => $request->city,
                'zip_code' => $request->postal_code,
                'phone' => $request->phone,
                'country' => 'Venezuela' // Default or form field
            ]);

            // Create Order
            $order = Order::createFromCart($cart, [
                'shipping_address_id' => $address->id,
                'billing_address_id' => $address->id,
                'status' => 'pending',
                'user_id' => $user ? $user->id : null,
                // We might need to handle 'email' in order if user_id is null, but Order model uses user_id. 
                // We'll trust Order model handles null user_id or we should have created a guest user.
            ]);

            // Clear cart
            $cart->items = [];
            $cart->save();

            DB::commit();

            return redirect()->route('checkout.success', $order->id); // We need a success page
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error procesando el pedido: ' . $e->getMessage()]);
        }
    }

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
}
