@php
    $restaurant      = $order->restaurant;
    $table           = $order->table;
    $currency_symbol = $restaurant ? $restaurant->currencySymbol() : '₹';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOT – {{ $order->order_number }}</title>
    <style>
        @page { size: auto; margin: 12mm; }
        html { -webkit-text-size-adjust: 100%; }
        html, body {
            margin: 0; padding: 0;
            width: 100%; max-width: 100%;
            box-sizing: border-box;
        }
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            padding: 16px 14px 88px;
            color: #111;
            font-size: 14px;
            line-height: 1.35;
            max-width: 420px;
            margin-left: auto;
            margin-right: auto;
        }
        @media print {
            @page { size: 80mm auto; margin: 4mm; }
            html, body {
                width: 72mm; max-width: 72mm;
                padding: 4mm 3mm 4mm;
                font-size: 12px;
                color: #000 !important;
                background: #fff !important;
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print { display: none !important; }
            .item { page-break-inside: avoid; break-inside: avoid; }
        }
        .center { text-align: center; }
        .muted  { color: #555; font-size: 11px; }
        .hr       { border: none; border-top: 1px dashed #9ca3af; margin: 8px 0; }
        .hr-solid { border: none; border-top: 2px solid #111; margin: 6px 0; }
        .row { display: flex; justify-content: space-between; gap: 8px; }
        .row span:last-child { flex-shrink: 0; }
        .title { font-weight: 800; font-size: 13px; margin-bottom: 2px; }
        .kot-label {
            display: inline-block;
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            border: 2px solid #111;
            padding: 2px 10px;
            margin: 4px 0 2px;
        }
        .logo img { max-width: 26mm; max-height: 26mm; object-fit: contain; display: inline-block; }
        .items { margin-top: 6px; }
        .item { display: flex; justify-content: space-between; gap: 8px; padding: 2px 0; }
        .item .name { flex: 1; min-width: 0; word-break: break-word; }
        .item .price { flex-shrink: 0; }
        .item-note  { font-size: 11px; color: #555; font-style: italic; margin-top: 1px; }
        .totals .row { margin: 4px 0; }
        .grand { font-weight: 900; font-size: 13px; }
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 999px;
            border: 1px solid #d1d5db;
            font-size: 11px;
            font-weight: 700;
            margin-top: 4px;
        }
        .badge--success { border-color: #16a34a; color: #166534; }
        .badge--warning { border-color: #f59e0b; color: #92400e; }
        .badge--danger  { border-color: #ef4444; color: #991b1b; }
        .order-note {
            border: 1px solid #111;
            border-left: 3px solid #111;
            padding: 5px 7px;
            font-size: 12px;
            margin-top: 4px;
            word-break: break-word;
        }
        .print-actions {
            position: fixed;
            left: 0; right: 0; bottom: 0;
            padding: 12px 16px;
            padding-bottom: max(12px, env(safe-area-inset-bottom));
            background: rgba(255,255,255,.96);
            border-top: 1px solid #e5e7eb;
            display: flex; gap: 10px; justify-content: center;
            z-index: 10;
        }
        .print-actions button {
            flex: 1; max-width: 280px;
            padding: 12px 16px;
            font-size: 16px; font-weight: 600;
            border: 0; border-radius: 10px;
            background: #111; color: #fff;
            cursor: pointer;
        }
        .print-actions .btn-close {
            flex: 0; max-width: none;
            background: #f3f4f6; color: #111;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="center">
        <div class="logo">
            @if($restaurant && $restaurant->logo)
                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="" width="120" height="120" decoding="async">
            @else
                <img src="{{ asset('build/img/global-tea-cafe-logo.png') }}" alt="" width="120" height="120" decoding="async" style="object-fit:contain;">
            @endif
        </div>
        <div class="title">{{ $restaurant->name ?? 'Restaurant' }}</div>
        <div class="muted">{{ $restaurant->address ?? '' }}</div>
        <div class="muted">{{ $restaurant->phone ? ('Phone: ' . $restaurant->phone) : '' }}</div>
        @if($restaurant->gst_number)
        <div class="muted">GST: {{ $restaurant->gst_number }}</div>
        @endif
        <div><span class="kot-label">Kitchen Order Ticket</span></div>
    </div>

    <div class="hr-solid"></div>

    <div class="row"><span>Order #</span><span><strong>{{ $order->order_number }}</strong></span></div>
    <div class="row"><span>Status</span><span class="badge badge--{{ $order->invoice_payment_status_badge }}">{{ $order->invoice_payment_status_label }}</span></div>
    <div class="row"><span>Date</span><span>{{ $order->created_at?->format('d M Y H:i') }}</span></div>
    @if($table)
    <div class="row"><span>Table</span><span>{{ $table->table_number ?? $table->name }}</span></div>
    @endif
    <div class="row"><span>Type</span><span>{{ ucfirst(str_replace('_', ' ', $order->order_type ?? '')) }}</span></div>
    @if($order->customer_name)
    <div class="row"><span>Customer</span><span>{{ $order->customer_name }}</span></div>
    @endif

    <div class="hr"></div>

    {{-- Items with pricing --}}
    <div class="items">
        @foreach($order->items ?? [] as $it)
        <div>
            <div class="item">
                <span class="name">{{ $it->item_name }} ×{{ (int)($it->quantity ?? 0) }}</span>
                <span class="price">{{ $currency_symbol }}{{ number_format((float)($it->total_price ?? 0), 2) }}</span>
            </div>
            @if($it->notes)
            <div class="item-note">&#9658; {{ $it->notes }}</div>
            @endif
        </div>
        @endforeach
    </div>

    @if($order->notes)
    <div class="hr"></div>
    <div class="order-note"><strong>Note:</strong> {{ $order->notes }}</div>
    @endif

    <div class="hr"></div>

    {{-- Totals --}}
    <div class="totals">
        <div class="row"><span>Sub Total</span><span>{{ $currency_symbol }}{{ number_format((float)($order->subtotal ?? 0), 2) }}</span></div>
        <div class="row"><span>Tax</span><span>{{ $currency_symbol }}{{ number_format((float)($order->tax_amount ?? 0), 2) }}</span></div>
        @if((float)($order->discount_amount ?? 0) > 0)
        <div class="row"><span>Discount</span><span>- {{ $currency_symbol }}{{ number_format((float)($order->discount_amount ?? 0), 2) }}</span></div>
        @endif
        <div class="row grand"><span>Total</span><span>{{ $currency_symbol }}{{ number_format((float)($order->total ?? 0), 2) }}</span></div>
    </div>

    <div class="hr-solid"></div>

    <div class="center muted">
        Powered by IT Softwar<br>
        softwar.in | info@softwar.in
    </div>

    {{-- Screen-only action bar --}}
    <div class="print-actions no-print">
        <button type="button" class="btn-close" onclick="window.history.length > 1 ? history.back() : window.close()">Close</button>
        <button type="button" id="kot-print-btn">Print KOT</button>
    </div>

<script>
(function () {
    var btn = document.getElementById('kot-print-btn');
    function doPrint() { try { window.print(); } catch (e) {} }
    if (btn) btn.addEventListener('click', doPrint);

    var coarse    = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;
    var narrow    = window.matchMedia && window.matchMedia('(max-width: 768px)').matches;
    var isAndroid = /Android/i.test(navigator.userAgent || '');

    if (coarse || narrow || isAndroid) return;

    function afterReady() { setTimeout(doPrint, 500); }
    if (document.readyState === 'complete') { afterReady(); }
    else { window.addEventListener('load', afterReady); }
})();
</script>
</body>
</html>
