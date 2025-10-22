<?php

namespace App\Filament\Widgets;

use App\Models\Currency;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Session;

class CurrencySelector extends Widget
{
    protected string $view = 'filament.widgets.currency-selector';

    protected int | string | array $columnSpan = 'full';

    public function getCurrentCurrency()
    {
        $currencyId = Session::get('selected_currency');
        if ($currencyId) {
            return Currency::find($currencyId);
        }
        
        return Currency::default()->first();
    }

    public function getCurrencies()
    {
        return Currency::active()->ordered()->get();
    }
}

