<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        $enrichedItems = [];

        if ($cart && !empty($cart->items)) {
            foreach ($cart->items as $item) {
                $product = Product::with(['category', 'productImages'])->find($item['product_id']);
                if ($product) {
                    $image = null;
                    if ($product->productImages && $product->productImages->count() > 0) {
                        $image = '/storage/' . $product->productImages->first()->image;
                    } elseif ($product->image) {
                        $image = '/storage/' . $product->image;
                    }

                    $enrichedItems[] = [
                        'product_id'          => $item['product_id'],
                        'name'                => $product->name,
                        'slug'                => $product->slug,
                        'image'               => $image,
                        'price'               => $item['price'],
                        'quantity'            => $item['quantity'],
                        'discount_percentage' => $item['discount_percentage'] ?? 0,
                        'variants'            => $item['variants'] ?? [],
                        'category'            => $product->category?->name,
                        'stock'               => $product->stock,
                    ];
                }
            }
        }

        return Inertia::render('Cart/Index', [
            'cart'   => $cart,
            'items'  => $enrichedItems,
            'totals' => $cart ? $cart->getTotals() : ['subtotal' => 0, 'discount_amount' => 0, 'tax_amount' => 0, 'total' => 0],
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'variants'   => 'nullable|array',
        ]);

        $cart = $this->getOrCreateCart();
        $product = Product::find($request->product_id);

        if (!$product->isAvailable($request->quantity)) {
            return back()->withErrors(['quantity' => 'No hay suficiente stock disponible.']);
        }

        $cart->addProduct($product->id, $request->quantity, $request->variants ?? []);

        return redirect()->back()->with('success', 'Producto agregado al carrito.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'index'    => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = $this->getCart();
        if ($cart && isset($cart->items[$request->index])) {
            $items = $cart->items;
            $items[$request->index]['quantity'] = $request->quantity;
            $cart->items = $items;
            $cart->save();
        }

        return redirect()->back();
    }

    public function remove(Request $request)
    {
        $request->validate([
            'index' => 'required|integer',
        ]);

        $cart = $this->getCart();
        if ($cart && isset($cart->items[$request->index])) {
            $items = $cart->items;
            unset($items[$request->index]);
            $cart->items = array_values($items);
            $cart->save();
        }

        return redirect()->back();
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

    private function getOrCreateCart()
    {
        $cart = $this->getCart();

        if (!$cart) {
            $cart = new Cart();
            $cart->session_id = Session::getId();
            if (auth()->check()) {
                $cart->user_id = auth()->id();
            }
            $cart->save();
        }

        return $cart;
    }
}

