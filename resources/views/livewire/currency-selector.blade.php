@php
    $currentCurrency = $currentCurrency ?? null;
    $currencies = $currencies ?? collect();
@endphp

<div class="fi-currency-selector">
    <div class="flex items-center space-x-3">
        <!-- Indicador de moneda activa compacto -->
        <div class="flex items-center space-x-2 px-3 py-1.5 bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/30 border border-primary-200 dark:border-primary-700 rounded-md shadow-sm">
            <div class="flex items-center space-x-1">
                <div class="w-1.5 h-1.5 bg-primary-500 rounded-full animate-pulse"></div>
                <span class="text-xs font-semibold text-primary-700 dark:text-primary-300 uppercase tracking-wider">
                    {{ $currentCurrency->code ?? 'USD' }}
                </span>
            </div>
            
            <span class="text-sm font-bold text-primary-900 dark:text-primary-100">
                {{ $currentCurrency->symbol ?? '$' }}
            </span>
        </div>

        <!-- Selector compacto y elegante -->
        <div class="relative">
            <form method="POST" action="{{ route('currency.update') }}">
                @csrf
                <select name="currency_id" onchange="this.form.submit()" 
                        class="appearance-none bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md px-3 py-1.5 pr-6 text-sm font-medium text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all duration-200 cursor-pointer min-w-[140px]">
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" 
                                {{ $currentCurrency && $currentCurrency->id == $currency->id ? 'selected' : '' }}>
                            {{ $currency->symbol }} {{ $currency->code }}
                        </option>
                    @endforeach
                </select>
            </form>
            
            <!-- Icono de flecha personalizado -->
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <svg class="w-3 h-3 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </div>

        <!-- Información de tasa compacta -->
        <div class="hidden xl:flex items-center space-x-1 text-xs text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 px-2 py-1 rounded">
            <span class="font-medium">Tasa:</span>
            <span class="font-mono text-primary-600 dark:text-primary-400">{{ number_format($currentCurrency->exchange_rate ?? 1, 4) }}</span>
        </div>
    </div>
</div>

<style>
.fi-currency-selector select {
    background-image: none;
}

.fi-currency-selector select:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.fi-currency-selector:hover .bg-primary-50 {
    background-color: rgba(59, 130, 246, 0.1);
}

/* Animación suave para el cambio */
.fi-currency-selector {
    transition: all 0.3s ease-in-out;
}

/* Efecto hover en el selector */
.fi-currency-selector select:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}
</style>
