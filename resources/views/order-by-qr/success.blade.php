@extends('layout.order-by-qr')

@section('content')

{{-- ========== HEADER ========== --}}
<div class="qr-header">
    <h1>{{ $restaurant->name }}</h1>
    <p class="mb-0"><span class="table-badge">🍽 Table {{ $table->name }}</span></p>
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
        <p style="color:var(--qr-text-muted);font-size:0.875rem;margin:0 0 1.25rem;">Your order has been sent to the kitchen.</p>
        <div class="success-order-num">#{{ $order_number }}</div>
        <p style="color:var(--qr-text-muted);font-size:0.8125rem;margin:0 0 1.5rem;">Show this number to your server if needed.</p>
        <a href="{{ route('order.by-qr', ['restaurant' => $restaurant->slug, 'table' => $table->slug ?? $table->id]) }}" class="order-again-btn">Order More →</a>
    </div>
</div>

@endsection
