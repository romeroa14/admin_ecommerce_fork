<?php

use Illuminate\Support\Facades\Route;
use App\Models\Currency;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/currency/update', function () {
    $currencyId = request('currency_id');
    
    if ($currencyId) {
        $currency = Currency::find($currencyId);
        if ($currency) {
            Session::put('selected_currency', $currency->id);
            Session::put('selected_currency_code', $currency->code);
            Session::put('selected_currency_symbol', $currency->symbol);
            Session::put('selected_currency_position', $currency->symbol_position);
        }
    }
    
    return redirect()->back();
})->name('currency.update');

// Rutas para facturas
Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
Route::get('/invoices/{invoice}/view', [InvoiceController::class, 'view'])->name('invoices.view');
