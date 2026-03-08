import { usePage } from '@inertiajs/vue3';

export function useCurrency() {
    const page = usePage();

    const formatCurrency = (amount: number | string | undefined | null) => {
        if (amount === undefined || amount === null) return '';

        let numericAmount = typeof amount === 'string' ? parseFloat(amount) : amount;
        if (isNaN(numericAmount)) return '';

        const currentCurrency = (page.props.currentCurrency as any) || {
            code: 'USD',
            symbol: '$',
            exchange_rate: 1,
            symbol_position: 'before',
            decimal_places: 2
        };

        const rate = parseFloat(currentCurrency.exchange_rate) || 1;
        const decimals = parseInt(currentCurrency.decimal_places) || 2;

        // Convert
        const converted = numericAmount * rate;

        // Format
        const formatted = converted.toLocaleString('en-US', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        });

        // Add symbol
        if (currentCurrency.symbol_position === 'before' || currentCurrency.code === 'USD' || currentCurrency.code === 'VES') {
            return `${currentCurrency.symbol} ${formatted}`;
        }
        return `${formatted} ${currentCurrency.symbol}`;
    };

    return {
        formatCurrency
    };
}
