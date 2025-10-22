<?php

namespace App\Livewire;

use App\Models\Currency;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class CurrencySelector extends Component
{
    public $selectedCurrency;
    public $currencies;

    public function mount()
    {
        $this->currencies = Currency::active()->ordered()->get();
        $this->selectedCurrency = Session::get('selected_currency', Currency::default()->first()?->id);
    }

    public function updatedSelectedCurrency()
    {
        if ($this->selectedCurrency) {
            $currency = Currency::find($this->selectedCurrency);
            if ($currency) {
                Session::put('selected_currency', $currency->id);
                Session::put('selected_currency_code', $currency->code);
                Session::put('selected_currency_symbol', $currency->symbol);
                Session::put('selected_currency_position', $currency->symbol_position);
                
                // Emitir evento para actualizar la página
                $this->dispatch('currency-updated', [
                    'currency_id' => $currency->id,
                    'currency_code' => $currency->code,
                    'currency_symbol' => $currency->symbol,
                ]);
                
                // Redirigir para actualizar todos los componentes
                return redirect()->to(request()->url());
            }
        }
    }

    public function getCurrentCurrency()
    {
        return Currency::find($this->selectedCurrency) ?? Currency::default()->first();
    }

    public function render()
    {
        return view('livewire.currency-selector');
    }
}
