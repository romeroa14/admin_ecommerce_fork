<?php

namespace App\Http\Middleware;

use App\Models\Currency;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class SetCurrency
{
    public function handle(Request $request, Closure $next)
    {
        // Si no hay moneda seleccionada, usar la por defecto
        if (!Session::has('selected_currency')) {
            $defaultCurrency = Currency::default()->first();
            if ($defaultCurrency) {
                Session::put('selected_currency', $defaultCurrency->id);
                Session::put('selected_currency_code', $defaultCurrency->code);
                Session::put('selected_currency_symbol', $defaultCurrency->symbol);
                Session::put('selected_currency_position', $defaultCurrency->symbol_position);
            }
        }

        // Compartir la moneda actual con todas las vistas
        $currentCurrency = Currency::find(Session::get('selected_currency')) ?? Currency::default()->first();
        View::share('currentCurrency', $currentCurrency);
        View::share('currencySymbol', $currentCurrency?->symbol ?? '$');
        View::share('currencyCode', $currentCurrency?->code ?? 'USD');

        return $next($request);
    }
}
