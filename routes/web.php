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
        ->with(['category', 'images'])
        ->latest()
        ->paginate(12);

    return Inertia::render('Home', [
        'products' => $products,
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

    // Checkout (Requires authentication)
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', function ($order) {
        return Inertia::render('Checkout/Success', ['orderId' => $order]);
    })->name('checkout.success');

    // Orders
    Route::get('/mis-pedidos', function () {
        $orders = auth()->user()->orders()->with('items')->latest()->paginate(10);
        return Inertia::render('Account/Orders', ['orders' => $orders]);
    })->name('account.orders');
});
