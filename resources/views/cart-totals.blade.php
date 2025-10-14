@php
    $totals = $record->getTotals();
@endphp

<div class="cart-totals-container">
    <div class="cart-totals-header">
        <h3>Resumen del Carrito</h3>
    </div>
    
    <div class="cart-totals-grid">
        <div class="totals-row">
            <span class="totals-label">Subtotal:</span>
            <span class="totals-value">€{{ number_format($totals['subtotal'], 2) }}</span>
        </div>
        
        <div class="totals-row discount">
            <span class="totals-label">Descuento:</span>
            <span class="totals-value">-€{{ number_format($totals['discount_amount'], 2) }}</span>
        </div>
        
        <div class="totals-row">
            <span class="totals-label">Impuestos (21%):</span>
            <span class="totals-value">€{{ number_format($totals['tax_amount'], 2) }}</span>
        </div>
    </div>
    
    <div class="totals-row total-row">
        <span class="totals-label total-label">Total:</span>
        <span class="totals-value total-value">€{{ number_format($totals['total'], 2) }}</span>
    </div>
</div>

<style>
.cart-totals-container {
    background: #f9fafb;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    margin-top: 20px;
}

.cart-totals-header h3 {
    margin: 0 0 15px 0;
    color: #374151;
    font-size: 18px;
    font-weight: 600;
}

.cart-totals-grid {
    margin-bottom: 15px;
}

.totals-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
}

.totals-row:last-child {
    border-bottom: none;
}

.totals-row.discount .totals-value {
    color: #f59e0b;
    font-weight: 600;
}

.totals-label {
    font-weight: 500;
    color: #374151;
}

.totals-value {
    font-weight: 600;
    color: #374151;
}

.total-row {
    border-top: 2px solid #22c55e;
    background: #f0fdf4;
    border-radius: 6px;
    padding: 12px;
    margin-top: 10px;
}

.total-label {
    font-weight: bold;
    font-size: 18px;
    color: #059669;
}

.total-value {
    font-weight: bold;
    font-size: 20px;
    color: #059669;
}
</style>
