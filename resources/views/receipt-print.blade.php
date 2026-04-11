@php
    $currency_symbol = $currency_symbol ?? (config('app.currency_symbol', '₹'));
    $restaurant = $order->restaurant;
    $table = $order->table;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $order->order_number }}</title>
    <style>
        /*
         * Android Chrome print preview often fails with a fixed @page size in mm and a 58mm-wide body
         * ("There was a problem printing the page"). Screen + Save-as-PDF use friendlier layout; thermal
         * width is applied in @media print where supported.
         */
        @page { size: auto; margin: 12mm; }
        html { -webkit-text-size-adjust: 100%; }
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            max-width: 100%;
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
            html, body { width: 72mm; max-width: 72mm; padding: 4mm 3mm 4mm; font-size: 12px; }
            .no-print { display: none !important; }
        }
        .center { text-align: center; }
        .muted { color: #555; font-size: 11px; }
        .hr { border-top: 1px dashed #9ca3af; margin: 8px 0; }
        .row { display: flex; justify-content: space-between; gap: 8px; }
        .row span:last-child { flex-shrink: 0; }
        .title { font-weight: 800; font-size: 13px; margin-bottom: 2px; }
        .logo img { max-width: 26mm; max-height: 26mm; object-fit: contain; display: inline-block; }
        .items { margin-top: 6px; }
        .item { display: flex; justify-content: space-between; gap: 8px; padding: 2px 0; }
        .item .name { max-width: 36mm; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
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
        .badge--danger { border-color: #ef4444; color: #991b1b; }
        .print-actions {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 12px 16px;
            padding-bottom: max(12px, env(safe-area-inset-bottom));
            background: rgba(255,255,255,.96);
            border-top: 1px solid #e5e7eb;
            display: flex;
            gap: 10px;
            justify-content: center;
            z-index: 10;
        }
        .print-actions button {
            flex: 1;
            max-width: 280px;
            padding: 12px 16px;
            font-size: 16px;
            font-weight: 600;
            border: 0;
            border-radius: 10px;
            background: #111;
            color: #fff;
        }
        .print-actions .btn-close {
            flex: 0;
            max-width: none;
            background: #f3f4f6;
            color: #111;
        }
    </style>
</head>
<body>

    <div class="center">
        <div class="logo">
            @if($restaurant && $restaurant->logo)
                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="" width="120" height="120" decoding="async">
            @else
                <img src="{{ asset('build/img/logo-small.svg') }}" alt="" width="120" height="48" decoding="async">
            @endif
        </div>
        <div class="title">{{ $restaurant->name ?? 'Restaurant' }}</div>
        <div class="muted">{{ $restaurant->address ?? '' }}</div>
        <div class="muted">{{ $restaurant->phone ? ('Phone: ' . $restaurant->phone) : '' }}</div>
    </div>

    <div class="hr"></div>

    <div class="row"><span>Order</span><span>{{ $order->order_number }}</span></div>
    <div class="row"><span>Status</span><span class="badge badge--{{ $order->invoice_payment_status_badge }}">{{ $order->invoice_payment_status_label }}</span></div>
    <div class="row"><span>Date</span><span>{{ $order->created_at?->format('d M Y H:i') }}</span></div>
    @if($table)
        <div class="row"><span>Table</span><span>{{ $table->table_number ?? $table->name }}</span></div>
    @endif
    <div class="row"><span>Type</span><span>{{ ucfirst(str_replace('_',' ', $order->order_type ?? '')) }}</span></div>

    <div class="hr"></div>

    <div class="items">
        @foreach($order->items ?? [] as $it)
            <div class="item">
                <span class="name">{{ $it->item_name }} ×{{ (int)($it->quantity ?? 0) }}</span>
                <span>{{ $currency_symbol }}{{ number_format((float)($it->total_price ?? 0), 2) }}</span>
            </div>
        @endforeach
    </div>

    <div class="hr"></div>

    <div class="totals">
        <div class="row"><span>Sub Total</span><span>{{ $currency_symbol }}{{ number_format((float)($order->subtotal ?? 0), 2) }}</span></div>
        <div class="row"><span>Tax</span><span>{{ $currency_symbol }}{{ number_format((float)($order->tax_amount ?? 0), 2) }}</span></div>
        @if((float)($order->discount_amount ?? 0) > 0)
            <div class="row"><span>Discount</span><span>- {{ $currency_symbol }}{{ number_format((float)($order->discount_amount ?? 0), 2) }}</span></div>
        @endif
        <div class="row grand"><span>Total</span><span>{{ $currency_symbol }}{{ number_format((float)($order->total ?? 0), 2) }}</span></div>
    </div>

    <div class="hr"></div>

    <div class="center muted">
        Powered by IT Softwar<br>
        softwar.in | info@softwar.in
    </div>

    <div class="print-actions no-print">
        <button type="button" class="btn-close" onclick="window.history.length > 1 ? history.back() : window.close()">Close</button>
        <button type="button" id="receipt-print-btn">Print</button>
    </div>

<script>
(function () {
    var btn = document.getElementById('receipt-print-btn');
    function doPrint() {
        try { window.print(); } catch (e) {}
    }
    if (btn) btn.addEventListener('click', doPrint);

    var coarse = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;
    var narrow = window.matchMedia && window.matchMedia('(max-width: 768px)').matches;
    var ua = navigator.userAgent || '';
    var isAndroid = /Android/i.test(ua);

    // Android / touch: do not auto-open print dialog; user taps Print after preview loads (avoids Chrome bug).
    if (coarse || narrow || isAndroid) {
        return;
    }

    // Desktop: auto-print shortly after load + images
    function afterReady() {
        setTimeout(doPrint, 500);
    }
    if (document.readyState === 'complete') {
        afterReady();
    } else {
        window.addEventListener('load', afterReady);
    }
})();
</script>
</body>
</html>

