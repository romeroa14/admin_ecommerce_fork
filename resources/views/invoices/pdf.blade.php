<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        
        .company-details {
            color: #666;
            line-height: 1.6;
        }
        
        .invoice-info {
            text-align: right;
            flex: 1;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
        }
        
        .invoice-number {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .invoice-date {
            font-size: 14px;
            color: #666;
        }
        
        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        
        .bill-to {
            flex: 1;
            margin-right: 20px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .customer-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #2563eb;
        }
        
        .customer-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .customer-details {
            color: #666;
            line-height: 1.5;
        }
        
        .order-info {
            flex: 1;
            margin-left: 20px;
        }
        
        .order-details {
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #10b981;
        }
        
        .order-number {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .order-details-text {
            color: #666;
            line-height: 1.5;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .items-table th {
            background: #2563eb;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .items-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        
        .items-table tr:nth-child(even) {
            background: #f9fafb;
        }
        
        .items-table tr:hover {
            background: #f3f4f6;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        
        .totals-table {
            width: 300px;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .totals-table .label {
            text-align: left;
            font-weight: 500;
        }
        
        .totals-table .amount {
            text-align: right;
            font-weight: bold;
        }
        
        .total-row {
            background: #2563eb;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        
        .total-row td {
            border-bottom: none;
        }
        
        .notes-section {
            margin-bottom: 30px;
        }
        
        .notes-content {
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #f59e0b;
            font-style: italic;
            color: #666;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-draft { background: #f3f4f6; color: #374151; }
        .status-sent { background: #dbeafe; color: #1e40af; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-overdue { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #f3f4f6; color: #6b7280; }
        
        @media print {
            body { margin: 0; }
            .invoice-container { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header">
            <div class="company-info">
                <div class="company-name">{{ config('app.name', 'Mi Empresa') }}</div>
                <div class="company-details">
                    Dirección de la empresa<br>
                    Ciudad, País<br>
                    Teléfono: +1 234 567 890<br>
                    Email: info@empresa.com<br>
                    Web: www.empresa.com
                </div>
            </div>
            <div class="invoice-info">
                <div class="invoice-title">FACTURA</div>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                <div class="invoice-date">{{ $invoice->invoice_date->format('d/m/Y') }}</div>
                <div style="margin-top: 10px;">
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Billing Section -->
        <div class="billing-section">
            <div class="bill-to">
                <div class="section-title">Facturar a:</div>
                <div class="customer-info">
                    <div class="customer-name">{{ $invoice->order->user->name }}</div>
                <div class="customer-details">
                    @if(is_array($invoice->billing_address))
                        @foreach($invoice->billing_address as $line)
                            {{ $line }}<br>
                        @endforeach
                    @else
                        {{ $invoice->billing_address }}
                    @endif
                </div>
                </div>
            </div>
            <div class="order-info">
                <div class="section-title">Información del Pedido:</div>
                <div class="order-details">
                    <div class="order-number">Pedido: {{ $invoice->order->order_number }}</div>
                    <div class="order-details-text">
                        Fecha del pedido: {{ $invoice->order->created_at->format('d/m/Y') }}<br>
                        Estado: {{ ucfirst($invoice->order->status) }}<br>
                        @if($invoice->order->is_paid)
                            <span style="color: #10b981; font-weight: bold;">✓ Pagado</span>
                        @else
                            <span style="color: #f59e0b; font-weight: bold;">⏳ Pendiente de pago</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Descripción</th>
                    <th style="width: 15%;" class="text-center">Cantidad</th>
                    <th style="width: 15%;" class="text-right">Precio Unit.</th>
                    <th style="width: 20%;" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product->name }}</strong>
                        @if($item->productVariant)
                            <br><small style="color: #666;">{{ $item->productVariant->name }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }} {{ current_currency_code() }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }} {{ current_currency_code() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Section -->
        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="label">Subtotal:</td>
                    <td class="amount">{{ number_format($invoice->subtotal, 2) }} {{ current_currency_code() }}</td>
                </tr>
                @if($invoice->discount_amount > 0)
                <tr>
                    <td class="label">Descuento:</td>
                    <td class="amount">-{{ number_format($invoice->discount_amount, 2) }} {{ current_currency_code() }}</td>
                </tr>
                @endif
                @if($invoice->tax_amount > 0)
                <tr>
                    <td class="label">Impuestos:</td>
                    <td class="amount">{{ number_format($invoice->tax_amount, 2) }} {{ current_currency_code() }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label">TOTAL:</td>
                    <td class="amount">{{ number_format($invoice->total_amount, 2) }} {{ current_currency_code() }}</td>
                </tr>
            </table>
        </div>

        <!-- Notes Section -->
        @if($invoice->notes)
        <div class="notes-section">
            <div class="section-title">Notas:</div>
            <div class="notes-content">
                {{ $invoice->notes }}
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p>Gracias por su compra. Esta factura fue generada automáticamente.</p>
            <p>Para consultas sobre esta factura, contacte con nuestro servicio al cliente.</p>
        </div>
    </div>
</body>
</html>
