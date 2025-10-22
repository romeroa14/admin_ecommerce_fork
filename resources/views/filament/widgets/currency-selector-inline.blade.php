<div class="fi-currency-selector-inline flex justify-center items-center w-full">
    <div class="flex items-center space-x-2">
        <!-- Selector de moneda integrado -->
        <form method="POST" action="{{ route('currency.update') }}" class="flex items-center">
            @csrf
            <select name="currency_id" onchange="this.form.submit()" 
                    class="text-sm bg-transparent border-0 text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 focus:outline-none focus:ring-0 cursor-pointer font-medium">
                @foreach($currencies as $currency)
                    <option value="{{ $currency->id }}" 
                            {{ $currentCurrency && $currentCurrency->id == $currency->id ? 'selected' : '' }}>
                        {{ $currency->symbol }} {{ $currency->code }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<style>


.fi-currency-selector-inline select {
    background-image: none;
    min-width: 140px;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    text-align: center;
}

.fi-currency-selector-inline select:focus {
    outline: none;
    box-shadow: none;
}

.fi-currency-selector-inline select:hover {
    color: rgb(59 130 246);
}
</style>
