<?php

namespace App\Helpers;

use App\Models\Currency;
use Illuminate\Support\Facades\Session;

class CurrencyHelper
{
    public static function getCurrentCurrency(): ?Currency
    {
        $currencyId = Session::get('selected_currency');
        if ($currencyId) {
            return Currency::find($currencyId);
        }
        
        return Currency::default()->first();
    }

    public static function formatAmount($amount, ?Currency $currency = null): string
    {
        $currency = $currency ?? self::getCurrentCurrency();
        
        if (!$currency) {
            return '$' . number_format($amount, 2);
        }

        $formatted = number_format($amount, $currency->decimal_places);
        
        if ($currency->symbol_position === 'before') {
            return $currency->symbol . $formatted;
        }
        
        return $formatted . ' ' . $currency->symbol;
    }

    public static function convertAmount($amount, ?Currency $fromCurrency = null, ?Currency $toCurrency = null): float
    {
        $fromCurrency = $fromCurrency ?? self::getCurrentCurrency();
        $toCurrency = $toCurrency ?? self::getCurrentCurrency();
        
        if (!$fromCurrency || !$toCurrency) {
            return $amount;
        }

        if ($fromCurrency->id === $toCurrency->id) {
            return $amount;
        }

        // Convertir a la moneda base (USD) y luego a la moneda objetivo
        $baseAmount = $amount / $fromCurrency->exchange_rate;
        return $baseAmount * $toCurrency->exchange_rate;
    }

    public static function getCurrentCurrencyCode(): string
    {
        $currency = self::getCurrentCurrency();
        return $currency ? $currency->code : 'USD';
    }

    public static function getCurrentCurrencySymbol(): string
    {
        $currency = self::getCurrentCurrency();
        return $currency ? $currency->symbol : '$';
    }

    public static function getCurrentCurrencyPosition(): string
    {
        $currency = self::getCurrentCurrency();
        return $currency ? $currency->symbol_position : 'before';
    }
}
