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
    $bannerHero = \App\Models\Banner::valid()->byPosition('home_hero')->orderBy('order')->get();
    $bannerMiddle = \App\Models\Banner::valid()->byPosition('home_middle')->orderBy('order')->first();
    $bannerMiddle2 = \App\Models\Banner::valid()->byPosition('home_middle_2')->orderBy('order')->first();
    $bannerBottom = \App\Models\Banner::valid()->byPosition('home_bottom')->orderBy('order')->first();

    $saleProducts = Product::active()
        ->with(['category', 'productImages'])
        ->where('discount_percentage', '>', 0)
        ->orderByDesc('discount_percentage')
        ->limit(4)->get();

    $newProducts = Product::active()
        ->with(['category', 'productImages'])
        ->latest()->limit(4)->get();

    $bestSellers = Product::active()
        ->with(['category', 'productImages'])
        ->orderByDesc('stock')
        ->limit(4)->get();

    $categories = \App\Models\Category::active()
        ->ordered()
        ->with(['products' => function ($q) {
            $q->active()->with('productImages')->limit(1);
        }])
        ->get()
        ->map(function ($cat) {
            $firstProduct = $cat->products->first();
            $cat->preview_product = $firstProduct;
            return $cat;
        });

    return Inertia::render('Home', [
        'bannerHero'   => $bannerHero,
        'bannerMiddle' => $bannerMiddle,
        'bannerMiddle2' => $bannerMiddle2,
        'bannerBottom' => $bannerBottom,
        'saleProducts' => $saleProducts,
        'newProducts'  => $newProducts,
        'bestSellers'  => $bestSellers,
        'categories'   => $categories,
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
    $orderInstance = \App\Models\Order::with(['items.product', 'shippingAddress', 'shipping', 'payments.paymentMethod'])->findOrFail($order);
    return Inertia::render('Checkout/Success', [
        'orderId' => $order,
        'orderData' => $orderInstance
    ]);
})->name('checkout.success');
