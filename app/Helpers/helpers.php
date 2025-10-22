<?php

use App\Helpers\CurrencyHelper;

if (!function_exists('currency')) {
    function currency($amount, ?\App\Models\Currency $currency = null): string
    {
        return CurrencyHelper::formatAmount($amount, $currency);
    }
}

if (!function_exists('current_currency')) {
    function current_currency(): ?\App\Models\Currency
    {
        return CurrencyHelper::getCurrentCurrency();
    }
}

if (!function_exists('current_currency_code')) {
    function current_currency_code(): string
    {
        return CurrencyHelper::getCurrentCurrencyCode();
    }
}

if (!function_exists('current_currency_symbol')) {
    function current_currency_symbol(): string
    {
        return CurrencyHelper::getCurrentCurrencySymbol();
    }
}

if (!function_exists('convert_currency')) {
    function convert_currency($amount, ?\App\Models\Currency $fromCurrency = null, ?\App\Models\Currency $toCurrency = null): float
    {
        return CurrencyHelper::convertAmount($amount, $fromCurrency, $toCurrency);
    }
}

if (!function_exists('format_money')) {
    function format_money($amount): string
    {
        return currency($amount);
    }
}
