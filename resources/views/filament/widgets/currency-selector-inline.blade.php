<div class="fi-currency-selector-inline">
    <div class="flex items-center space-x-3 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
        

        <!-- Selector de moneda -->
        <div class="flex items-center space-x-2">
            <form method="POST" action="{{ route('currency.update') }}" class="flex items-center space-x-2">
                @csrf
                <select name="currency_id" onchange="this.form.submit()" 
                        class="text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                    <option value="">Cambiar moneda...</option>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" 
                                {{ $currentCurrency && $currentCurrency->id == $currency->id ? 'selected' : '' }}>
                            {{ $currency->symbol }} {{ $currency->code }} - {{ $currency->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        
    </div>
</div>

<style>
.fi-currency-selector-inline select {
    background-image: none;
    min-width: 180px;
}

.fi-currency-selector-inline select:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
}
</style>
