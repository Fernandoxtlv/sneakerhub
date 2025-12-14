<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Boleta {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #1f2937;
            margin: 0;
            padding: 40px;
        }

        /* Premium Header */
        .brand-header {
            background: #ffffff;
            padding: 40px;
            margin: -40px -40px 20px -40px;
            border-bottom: 4px solid #4f46e5;
        }

        .brand-logo {
            float: left;
            width: 180px;
            /* Adjust size as needed */
        }

        .brand-logo img {
            max-width: 100%;
            height: auto;
        }

        .invoice-title {
            float: right;
            text-align: right;
            margin-top: 10px;
        }

        .invoice-title h1 {
            font-size: 28px;
            margin: 0;
            font-weight: 800;
            color: #4f46e5;
            text-transform: uppercase;
        }

        .invoice-title h2 {
            font-size: 16px;
            margin: 5px 0 0;
            font-weight: bold;
            color: #374151;
        }

        .badge-paid {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 4px 15px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 8px;
        }

        /* Company Info */
        .company-info {
            margin-bottom: 40px;
            color: #6b7280;
            font-size: 12px;
        }

        /* Addresses Grid */
        .billing-grid {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            margin-top: 20px;
        }

        .billing-col {
            display: table-cell;
            width: 45%;
            vertical-align: top;
            padding: 20px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .billing-col h3 {
            color: #4f46e5;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
            margin-top: 0;
            margin-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .spacer {
            width: 10%;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            background-color: #4f46e5;
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table th:first-child {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .items-table th:last-child {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .items-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .item-name {
            font-weight: bold;
            color: #111827;
        }

        .item-meta {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* Totals */
        .totals-table {
            width: 40%;
            float: right;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .totals-table td {
            padding: 8px 15px;
            text-align: right;
        }

        .totals-table .label {
            color: #6b7280;
        }

        .totals-table .value {
            font-weight: bold;
            color: #374151;
        }

        .totals-table .grand-total {
            background-color: #4f46e5;
            color: white;
            border-radius: 8px;
            padding: 12px 15px;
            margin-top: 10px;
        }

        .grand-total .label {
            color: rgba(255, 255, 255, 0.8);
        }

        .grand-total .value {
            color: white;
            font-size: 16px;
        }

        /* Footer */
        .footer {
            margin-top: 80px;
            text-align: center;
            color: #9ca3af;
            font-size: 11px;
            border-top: 1px solid #f3f4f6;
            padding-top: 30px;
        }

        .badge-paid {
            display: inline-block;
            background: #d1fae5;
            color: #047857;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Premium Brand Header -->
    <div class="brand-header">
        <div class="brand-logo">
            <img src="{{ public_path('images/logo.png') }}" alt="SneakerHub">
        </div>
        <div class="invoice-title">
            <h1>BOLETA DE VENTA</h1>
            <h2>{{ $invoice->invoice_number }}</h2>
            <div class="badge-paid">PAGADO</div>
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- Info Grid -->
    <div class="billing-grid">
        <div class="billing-col">
            <h3>Empresa</h3>
            <strong>{{ $store['name'] }}</strong><br>
            RUC: {{ $store['ruc'] }}<br>
            {{ $store['address'] }}<br>
            {{ $store['email'] }}
        </div>
        <div class="spacer"></div>
        <div class="billing-col">
            <h3>Cliente</h3>
            <strong>{{ $customer['name'] }}</strong><br>
            {{ $customer['address'] }}<br>
            {{ $customer['city'] }}<br>
            {{ $customer['phone'] }}
        </div>
    </div>

    <!-- Order Meta -->
    <div style="margin-bottom: 30px; font-size: 12px; color: #4b5563;">
        <table width="100%">
            <tr>
                <td><strong>Fecha de Emisión:</strong> {{ $invoice->issued_at->format('d/m/Y h:i A') }}</td>
                <td align="center"><strong>Pedido Ref:</strong> {{ $order->order_number }}</td>
                <td align="right"><strong>Método de Pago:</strong> {{ $payment_method }}</td>
            </tr>
        </table>
    </div>

    <!-- Items -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="45%">Producto / Descripción</th>
                <th width="15%" class="text-center">Talla</th>
                <th width="10%" class="text-center">Cant.</th>
                <th width="15%" class="text-right">Precio Unit.</th>
                <th width="15%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->name }}</div>
                        <div class="item-meta">SKU: {{ $item->sku }}</div>
                    </td>
                    <td class="text-center">{{ $item->size ?? '-' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $currency_symbol }} {{ number_format($item->price, 2) }}</td>
                    <td class="text-right">{{ $currency_symbol }} {{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <table class="totals-table">
        <tr>
            <td class="label">Subtotal</td>
            <td class="value">{{ $currency_symbol }} {{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if($order->discount > 0)
            <tr>
                <td class="label">Descuento</td>
                <td class="value" style="color: #059669;">- {{ $currency_symbol }} {{ number_format($order->discount, 2) }}
                </td>
            </tr>
        @endif
        <tr>
            <td class="label">{{ $tax_name }} ({{ $order->tax_rate }}%)</td>
            <td class="value">{{ $currency_symbol }} {{ number_format($order->tax, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Envío</td>
            <td class="value">{{ $currency_symbol }} {{ number_format($order->delivery_fee, 2) }}</td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 0;">
                <div class="grand-total">
                    <table width="100%">
                        <tr>
                            <td class="label" style="text-align: left; padding: 0;">TOTAL A PAGAR</td>
                            <td class="value" style="text-align: right; padding: 0;">{{ $currency_symbol }}
                                {{ number_format($order->total, 2) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <div style="clear: both;"></div>

    <!-- Footer -->
    <div class="footer">
        <p>Gracias por elegir <strong>SneakerHub</strong>. ¡Esperamos verte pronto!</p>
        <p style="margin-top: 5px; font-size: 10px;">Este documento es un comprobante de pago electrónico válido. Para
            cualquier consulta contáctenos en {{ $store['email'] }}</p>
    </div>
</body>

</html>