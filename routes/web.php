<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use Inertia\Inertia;
use App\Models\Product;

Route::get('/', function () {
    $products = Product::active()
        ->with(['category', 'images'])
        ->latest()
        ->paginate(12);

    return Inertia::render('Home', [
        'products' => $products,
    ]);
})->name('home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/tags/{tag:slug}', [\App\Http\Controllers\TagController::class, 'show'])->name('tags.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/success/{order}', function ($order) {
    return Inertia::render('Checkout/Success', ['orderId' => $order]);
})->name('checkout.success');

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
