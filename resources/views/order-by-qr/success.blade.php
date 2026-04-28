@extends('layout.order-by-qr')

@section('content')

{{-- ========== HEADER ========== --}}
<div class="qr-header">
    <h1>{{ $restaurant->name }}</h1>
    <p class="mb-0"><span class="table-badge">🍽 Table {{ $table->name }}</span></p>
</div>

{{-- ========== PAYMENT STATUS BADGE ========== --}}
@php
    $paymentStatus = $order ? ($order->payment_status ?? 'unpaid') : 'unpaid';
    $orderTotal = $order ? (float) $order->total : 0;
@endphp
<div style="padding: 0.5rem 1rem 0;">
    <div id="payment-status-badge" class="payment-status-badge payment-status-{{ $paymentStatus }}" data-status="{{ $paymentStatus }}">
        @if($paymentStatus === 'paid')
            <span class="payment-status-icon">✓</span>
            <span>Payment complete</span>
        @else
            <span class="payment-status-icon pending">⏳</span>
            <span>Payment pending – scan QR below to pay</span>
        @endif
    </div>
</div>

{{-- ========== SUCCESS CARD ========== --}}
<div style="padding: 0 1rem;">
    <div class="success-card">
        <div class="success-check">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
        </div>
        <h3 style="font-size:1.25rem;font-weight:700;margin:0 0 0.5rem;">Order Placed! 🎉</h3>
        <p style="color:var(--qr-text-muted);font-size:0.875rem;margin:0 0 1rem;">Your order has been sent to the kitchen.</p>
        <div class="success-order-num">#{{ $order_number }}</div>
        @if($orderTotal > 0)
            <p style="font-size:1rem;font-weight:700;margin:0 0 1rem;">Total: {{ $currency_symbol }}{{ number_format($orderTotal, 2) }}</p>
        @endif
        <p style="color:var(--qr-text-muted);font-size:0.8125rem;margin:0 0 1.25rem;">Show this screen to your server. After you pay, staff will mark payment and you’ll see <strong>Payment complete</strong> here.</p>
        <a href="{{ route('order.by-qr', ['restaurant' => $restaurant->slug, 'table' => $table->slug ?? $table->id]) }}" class="order-again-btn">Order More →</a>
    </div>
</div>

{{-- ========== SCAN & PAY (same as menu) ========== --}}
@if($restaurant->payment_qr)
<div style="padding: 1rem;">
    <div class="qr-scan-pay-card">
        <p style="font-size:0.9rem;font-weight:700;margin:0 0 0.5rem;color:#1e293b;">Scan & Pay</p>
        <p style="font-size:0.8rem;color:#64748b;margin:0 0 0.75rem;">Scan with your UPI app and pay. Staff will then mark this order as paid.</p>
        <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
            <div class="qr-pay-qr-wrap">
                <img src="{{ asset('storage/' . $restaurant->payment_qr) }}" alt="Payment QR" class="qr-pay-qr-img">
            </div>
            <div>
                @if($orderTotal > 0)
                    <p style="font-size:1.1rem;font-weight:700;margin:0;color:#0f172a;">Pay {{ $currency_symbol }}{{ number_format($orderTotal, 2) }}</p>
                @endif
                <p style="font-size:0.75rem;color:#64748b;margin:0.25rem 0 0;">Order #{{ $order_number }}</p>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
(function() {
    var statusEl = document.getElementById('payment-status-badge');
    if (!statusEl || statusEl.getAttribute('data-status') === 'paid') return;

    var orderNumber = '{{ $order_number }}';
    var url = '{{ route("order.by-qr.order-status", ["restaurant" => $restaurant->slug, "table" => $table->slug ?? $table->id]) }}?order_number=' + encodeURIComponent(orderNumber);

    function poll() {
        fetch(url).then(function(r) { return r.json(); }).then(function(d) {
            if (d.payment_status === 'paid') {
                statusEl.setAttribute('data-status', 'paid');
                statusEl.className = 'payment-status-badge payment-status-paid';
                statusEl.innerHTML = '<span class="payment-status-icon">✓</span><span>Payment complete</span>';
                return;
            }
        }).catch(function() {});
    }

    setInterval(poll, 4000);
})();
</script>
@endpush

@endsection
