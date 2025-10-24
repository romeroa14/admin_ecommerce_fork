<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Payment;
use App\Observers\OrderObserver;
use App\Observers\PaymentObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar observers
        Order::observe(OrderObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
