<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Tu pedido #{{ $orderNumber }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f0f0f0;
            color: #111111;
            -webkit-font-smoothing: antialiased;
        }

        .preheader {
            display: none;
            max-height: 0;
            overflow: hidden;
            mso-hide: all;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f0f0f0;
            padding: 32px 0 48px;
        }

        .email-container {
            max-width: 580px;
            margin: 0 auto;
        }

        /* Top brand bar */
        .brand-bar {
            text-align: center;
            padding-bottom: 24px;
        }
        .brand-name {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #040054;
        }

        /* Main card */
        .card {
            background: #ffffff;
            border-radius: 4px;
            overflow: hidden;
        }

        /* Status strip */
        .status-strip {
            background: #040054;
            padding: 28px 40px;
        }
        .status-strip .label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.55);
            margin-bottom: 6px;
        }
        .status-strip h1 {
            font-size: 22px;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: -0.02em;
        }
        .status-strip .order-num {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
            margin-top: 2px;
        }
        .status-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            background: #4ade80;
            border-radius: 50%;
            margin-right: 6px;
            vertical-align: middle;
        }

        /* Body content */
        .body-content {
            padding: 36px 40px;
        }

        .greeting-text {
            font-size: 15px;
            color: #3d3d3d;
            line-height: 1.65;
            margin-bottom: 32px;
        }
        .greeting-text strong { color: #111111; }

        /* Divider */
        .divider {
            height: 1px;
            background: #f0f0f0;
            margin: 0 0 28px 0;
        }

        /* Section heading */
        .section-heading {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 16px;
        }

        /* Items list */
        .items-list {
            margin-bottom: 28px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 14px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .item-row:last-child { border-bottom: none; }
        .item-name {
            font-size: 14px;
            font-weight: 500;
            color: #111111;
            margin-bottom: 3px;
        }
        .item-meta {
            font-size: 12px;
            color: #9ca3af;
        }
        .item-price {
            font-size: 14px;
            font-weight: 600;
            color: #111111;
            white-space: nowrap;
            padding-left: 16px;
        }

        /* Totals section */
        .totals-block {
            background: #fafafa;
            border: 1px solid #f0f0f0;
            border-radius: 4px;
            padding: 20px 24px;
            margin-bottom: 32px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #6b7280;
            padding: 5px 0;
        }
        .totals-row.grand-total {
            font-size: 16px;
            font-weight: 700;
            color: #111111;
            padding-top: 12px;
            margin-top: 8px;
            border-top: 1px solid #e5e7eb;
        }
        .totals-row.grand-total .amount {
            color: #040054;
        }

        /* Info grid */
        .info-grid {
            display: flex;
            gap: 24px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }
        .info-block {
            flex: 1;
            min-width: 200px;
        }
        .info-block .label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 8px;
        }
        .info-block p {
            font-size: 13px;
            line-height: 1.7;
            color: #374151;
        }

        /* CTA section */
        .cta-section {
            border-top: 1px solid #f0f0f0;
            padding-top: 28px;
            margin-top: 4px;
        }
        .cta-text {
            font-size: 14px;
            color: #4b5563;
            margin-bottom: 20px;
            line-height: 1.65;
        }
        .cta-button {
            display: inline-block;
            background-color: #25d366;
            color: #ffffff !important;
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.04em;
            padding: 14px 28px;
            border-radius: 3px;
        }

        /* Footer */
        .email-footer {
            padding: 28px 40px;
            border-top: 1px solid #f0f0f0;
        }
        .footer-text {
            font-size: 11px;
            color: #9ca3af;
            line-height: 2;
        }
        .footer-text a {
            color: #040054;
            text-decoration: none;
        }
        .footer-brand {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #040054;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
<!-- Preheader text (invisible, shows in inbox preview) -->
<div class="preheader">
    Pedido #{{ $orderNumber }} confirmado · Total ${{ number_format($total, 2) }} · Gracias por tu compra en EquipoContainer.
</div>

<div class="email-wrapper">
    <div class="email-container">

        <!-- Brand bar -->
        <div class="brand-bar">
            <img src="{{ config('app.url') }}/storage/Logos/econtainer.png"
                 alt="EquipoContainer"
                 width="160"
                 style="height:auto; display:inline-block;">
        </div>

        <!-- Main card -->
        <div class="card">

            <!-- Status strip -->
            <div class="status-strip">
                <div class="label"><span class="status-dot"></span>Pedido recibido</div>
                <h1>Gracias, {{ $address?->first_name ?? 'Cliente' }}.</h1>
                <div class="order-num">Pedido #{{ $orderNumber }} · {{ $order->created_at->format('d \d\e F \d\e Y') }}</div>
            </div>

            <!-- Body content -->
            <div class="body-content">

                <p class="greeting-text">
                    Tu pedido ha sido registrado correctamente.
                    Revisá el resumen a continuación y comunicate por WhatsApp para coordinar el pago y la entrega.
                </p>

                <!-- Items -->
                <div class="section-heading">Productos</div>
                <div class="items-list">
                    @foreach($items as $item)
                    <div class="item-row">
                        <div>
                            <div class="item-name">{{ $item->product?->name ?? $item->product_name ?? 'Producto' }}</div>
                            <div class="item-meta">
                                Cant. {{ $item->quantity }}
                                @if(!empty($item->variants))
                                    &nbsp;·&nbsp;{{ collect($item->variants)->map(fn($v, $k) => "$k: $v")->implode(', ') }}
                                @endif
                            </div>
                        </div>
                        <div class="item-price">${{ number_format($item->price * $item->quantity, 2) }}</div>
                    </div>
                    @endforeach
                </div>

                <!-- Totals -->
                <div class="totals-block">
                    @if($order->shipping_cost > 0)
                    <div class="totals-row">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->subtotal ?? ($total - $order->shipping_cost), 2) }}</span>
                    </div>
                    <div class="totals-row">
                        <span>Envío — {{ $shipping?->name }}</span>
                        <span>${{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    @endif
                    <div class="totals-row grand-total">
                        <span>Total</span>
                        <span class="amount">${{ number_format($total, 2) }}</span>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Info grid -->
                <div class="info-grid">
                    @if($address)
                    <div class="info-block">
                        <div class="label">Dirección de entrega</div>
                        <p>
                            {{ $address->first_name }} {{ $address->last_name }}<br>
                            {{ $address->address_line_1 }}<br>
                            {{ $address->city }}, {{ $address->country }}<br>
                            @if($address->phone)
                                {{ $address->phone }}
                            @endif
                        </p>
                    </div>
                    @endif
                    @if($shipping)
                    <div class="info-block">
                        <div class="label">Método de envío</div>
                        <p>
                            {{ $shipping->name }}<br>
                            @if($shipping->estimated_days_min && $shipping->estimated_days_max)
                                {{ $shipping->estimated_days_min }}–{{ $shipping->estimated_days_max }} días hábiles
                            @endif
                        </p>
                    </div>
                    @endif
                </div>

                <!-- CTA -->
                <div class="cta-section">
                    <p class="cta-text">
                        Para completar tu pedido, escribinos por WhatsApp indicando tu número de pedido
                        <strong>#{{ $orderNumber }}</strong>. Nuestro equipo te confirmará la disponibilidad y te guiará con el pago.
                    </p>
                    <a href="https://wa.me/584123816330?text=Hola%2C+hice+un+pedido+%23{{ $orderNumber }}+y+quiero+coordinarlo."
                       class="cta-button">
                        Escribir por WhatsApp
                    </a>
                </div>

            </div>

            <!-- Footer inside card -->
            <div class="email-footer">
                <div class="footer-brand">EquipoContainer</div>
                <div class="footer-text">
                    Caracas, Venezuela &mdash;
                    <a href="mailto:equipocontainer.ve@gmail.com">equipocontainer.ve@gmail.com</a><br>
                    Si tenés alguna consulta sobre tu pedido, respondé este correo o escribinos por WhatsApp.<br>
                    &copy; {{ date('Y') }} EquipoContainer. Todos los derechos reservados.
                </div>
            </div>

        </div>
        <!-- / Main card -->

    </div>
</div>

</body>
</html>
