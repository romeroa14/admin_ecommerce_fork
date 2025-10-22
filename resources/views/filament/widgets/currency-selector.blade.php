@php
    $currentCurrency = $currentCurrency ?? null;
    $currencies = $currencies ?? collect();
@endphp

<div class="fi-currency-selector bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-4 py-3">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Moneda Activa:
                </span>
            </div>
            
            <div class="flex items-center space-x-2">
                <span class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ $currentCurrency->symbol ?? '$' }}
                </span>
                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ $currentCurrency->code ?? 'USD' }}
                </span>
            </div>
        </div>
        
        <div class="flex items-center space-x-3">
            <form method="POST" action="{{ route('currency.update') }}">
                @csrf
                <select name="currency_id" onchange="this.form.submit()" 
                        class="text-sm bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:text-white">
                    <option value="">Cambiar...</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" 
                                {{ $currentCurrency && $currentCurrency->id == $currency->id ? 'selected' : '' }}>
                            {{ $currency->symbol }} {{ $currency->code }}
                        </option>
                    @endforeach
                </select>
            </form>
            
            <div class="text-xs text-gray-500 dark:text-gray-400">
                Tasa: {{ number_format($currentCurrency->exchange_rate ?? 1, 4) }}
            </div>
        </div>
    </div>
</div>
