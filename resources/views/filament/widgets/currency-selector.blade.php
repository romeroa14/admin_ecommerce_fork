<div class="fi-currency-selector">
    <div class="flex items-center space-x-3 p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
        <!-- Indicador de moneda activa -->
        <div class="flex items-center space-x-2">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Moneda:</span>
            <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                {{ $this->getCurrentCurrency()->symbol ?? '$' }}
            </span>
            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                {{ $this->getCurrentCurrency()->code ?? 'USD' }}
            </span>
        </div>

        <!-- Selector de moneda -->
        <div class="flex items-center space-x-2">
            <form method="POST" action="{{ route('currency.update') }}" class="flex items-center space-x-2">
                @csrf
                <select name="currency_id" onchange="this.form.submit()" 
                        class="text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                    <option value="">Cambiar moneda...</option>
                    @foreach($this->getCurrencies() as $currency)
                        <option value="{{ $currency->id }}" 
                                {{ $this->getCurrentCurrency() && $this->getCurrentCurrency()->id == $currency->id ? 'selected' : '' }}>
                            {{ $currency->symbol }} {{ $currency->code }} - {{ $currency->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Información de tasa -->
        <div class="text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 px-2 py-1 rounded">
            <div class="text-center">
                <div class="font-medium">Tasa</div>
                <div class="font-mono text-primary-600 dark:text-primary-400">
                    {{ number_format($this->getCurrentCurrency()->exchange_rate ?? 1, 4) }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.fi-currency-selector select {
    background-image: none;
    min-width: 180px;
}

.fi-currency-selector select:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}
</style>
