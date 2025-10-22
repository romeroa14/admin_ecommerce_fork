<?php

namespace App\Filament\Widgets;

use App\Models\Currency;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Session;

class CurrencySelector extends Widget
{
    protected string $view = 'filament.widgets.currency-selector';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        return [
            'currencies' => Currency::active()->ordered()->get(),
            'currentCurrency' => $this->getCurrentCurrency(),
        ];
    }

    public function getCurrentCurrency()
    {
        $currencyId = Session::get('selected_currency');
        if ($currencyId) {
            return Currency::find($currencyId);
        }
        
        return Currency::default()->first();
    }

    public function updateCurrency($currencyId)
    {
        if ($currencyId) {
            $currency = Currency::find($currencyId);
            if ($currency) {
                Session::put('selected_currency', $currency->id);
                Session::put('selected_currency_code', $currency->code);
                Session::put('selected_currency_symbol', $currency->symbol);
                Session::put('selected_currency_position', $currency->symbol_position);
                
                return redirect()->to(request()->url());
            }
        }
    }
}
