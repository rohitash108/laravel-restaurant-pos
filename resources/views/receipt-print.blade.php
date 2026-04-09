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
        @page { size: 58mm auto; margin: 0; }
        html, body { width: 58mm; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            padding: 6mm 4mm 6mm;
            color: #111;
            font-size: 12px;
            line-height: 1.3;
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
    </style>
</head>
<body onload="setTimeout(function(){ window.print(); }, 250)">

    <div class="center">
        <div class="logo">
            @if($restaurant && $restaurant->logo)
                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="">
            @else
                <img src="{{ asset('build/img/logo-small.svg') }}" alt="">
            @endif
        </div>
        <div class="title">{{ $restaurant->name ?? 'Restaurant' }}</div>
        <div class="muted">{{ $restaurant->address ?? '' }}</div>
        <div class="muted">{{ $restaurant->phone ? ('Phone: ' . $restaurant->phone) : '' }}</div>
    </div>

    <div class="hr"></div>

    <div class="row"><span>Order</span><span>{{ $order->order_number }}</span></div>
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

</body>
</html>

