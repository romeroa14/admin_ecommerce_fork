<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Auth\ClientAuthController;
use Inertia\Inertia;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', function () {
    $products = Product::active()
        ->with(['category', 'productImages'])
        ->latest()
        ->paginate(12);

    $banners = \App\Models\Banner::valid()
        ->byPosition('home_hero')
        ->orderBy('order')
        ->get();

    $categories = \App\Models\Category::orderBy('name')->get();

    return Inertia::render('Home', [
        'products' => $products,
        'banners' => $banners,
        'categories' => $categories,
    ]);
})->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Reviews
Route::post('/products/{product:slug}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::post('/reviews/{review}/helpful', [ReviewController::class, 'markHelpful'])->name('reviews.helpful');
Route::post('/reviews/{review}/unhelpful', [ReviewController::class, 'markUnhelpful'])->name('reviews.unhelpful');

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Tags
Route::get('/tags/{tag:slug}', [\App\Http\Controllers\TagController::class, 'show'])->name('tags.show');

// Cart (Public - Session based)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

// Currency
Route::post('/currency/update', function () {
    $currencyId = request('currency_id');

    if ($currencyId) {
        $currency = \App\Models\Currency::find($currencyId);
        if ($currency) {
            \Illuminate\Support\Facades\Session::put('selected_currency', $currency->id);
            \Illuminate\Support\Facades\Session::put('selected_currency_code', $currency->code);
            \Illuminate\Support\Facades\Session::put('selected_currency_symbol', $currency->symbol);
            \Illuminate\Support\Facades\Session::put('selected_currency_position', $currency->symbol_position);
        }
    }

    return redirect()->back();
})->name('currency.update');

/*
|--------------------------------------------------------------------------
| Guest Routes (Only for non-authenticated users)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [ClientAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ClientAuthController::class, 'login']);

    // Register
    Route::get('/register', [ClientAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [ClientAuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Require login)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('logout');

    // My Account
    Route::get('/mi-cuenta', function () {
        return Inertia::render('Account/Dashboard');
    })->name('account.dashboard');

    // Orders
    Route::get('/mis-pedidos', function () {
        $orders = auth()->user()->orders()->with('items')->latest()->paginate(10);
        return Inertia::render('Account/Orders', ['orders' => $orders]);
    })->name('account.orders');
});

// Checkout Routes (Guest & Auth)
Route::get('/checkout/init', [CheckoutController::class, 'init'])->name('checkout.init');
Route::get('/checkout/address', [CheckoutController::class, 'address'])->name('checkout.address');
Route::post('/checkout/address', [CheckoutController::class, 'storeAddress'])->name('checkout.address.store');
Route::get('/checkout/shipping', [CheckoutController::class, 'shipping'])->name('checkout.shipping');
Route::post('/checkout/shipping', [CheckoutController::class, 'storeShipping'])->name('checkout.shipping.store');
Route::get('/checkout/payment', [CheckoutController::class, 'payment'])->name('checkout.payment');
Route::post('/checkout/payment', [CheckoutController::class, 'storePayment'])->name('checkout.payment.store');
Route::get('/checkout/success/{order}', function ($order) {
    $orderInstance = \App\Models\Order::with(['items.product', 'shippingAddress'])->findOrFail($order);
    return Inertia::render('Checkout/Success', [
        'orderId' => $order,
        'orderData' => $orderInstance
    ]);
})->name('checkout.success');
