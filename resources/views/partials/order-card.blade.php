<div class="card flex-fill">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="avatar avatar-lg bg-primary rounded-circle">
                    <i class="icon-shopping-bag"></i>
                </div>
                <div>
                    <h6 class="mb-1 fs-14 fw-semibold"><a href="{{ route('invoice-details', $order) }}" target="_blank">#{{ $order->order_number }}</a></h6>
                    <p class="mb-0 d-flex align-items-center gap-2">{{ ucfirst(str_replace('_', ' ', $order->order_type ?? 'dine_in')) }}@if($order->table)<span class="text-light">|</span> Table : {{ $order->table->name }}@endif</p>
                </div>
            </div>
            <div class="dropstart">
                <button class="btn btn-sm btn-icon btn-white rounded-circle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" aria-label="Actions">
                    <i class="icon-ellipsis-vertical"></i>
                </button>
                <ul class="dropdown-menu p-3">
                    @if($order->status !== 'completed' && $order->status !== 'cancelled')
                    <li><a href="{{ route('orders.edit', $order) }}" class="dropdown-item rounded d-flex align-items-center"><i class="icon-pencil-line me-2"></i>Edit Order</a></li>
                    @endif
                    @if($order->status !== 'cancelled')
                    <li>
                        <form action="{{ route('orders.update-payment-status', $order) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="payment_status" value="{{ ($order->payment_status ?? 'unpaid') === 'paid' ? 'unpaid' : 'paid' }}">
                            <button type="submit" class="dropdown-item rounded d-flex align-items-center w-100 border-0 bg-transparent text-start"><i class="icon-{{ ($order->payment_status ?? 'unpaid') === 'paid' ? 'circle-alert' : 'circle-check' }} me-2"></i>{{ ($order->payment_status ?? 'unpaid') === 'paid' ? 'Mark Unpaid' : 'Mark Paid' }}</button>
                        </form>
                    </li>
                    @endif
                    @if($order->status !== 'completed')
                    <li>
                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="dropdown-item rounded d-flex align-items-center w-100 border-0 bg-transparent text-start"><i class="icon-check-check me-2"></i>Complete</button>
                        </form>
                    </li>
                    @endif
                    @if($order->status !== 'cancelled')
                    <li>
                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="cancelled">
                            <button type="submit" class="dropdown-item rounded d-flex align-items-center w-100 border-0 bg-transparent text-start"><i class="icon-x me-2"></i>Cancel</button>
                        </form>
                    </li>
                    @endif
                    @if($order->status !== 'completed')
                    <li>
                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="dropdown-item rounded d-flex align-items-center w-100 border-0 bg-transparent text-start"><i class="icon-pointer me-2"></i>Pay & Complete</button>
                        </form>
                    </li>
                    @endif
                    <li><a href="{{ route('invoice-details', $order) }}?print=1" target="_blank" class="dropdown-item rounded d-flex align-items-center"><i class="icon-printer me-2"></i>Print Receipt</a></li>
                </ul>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <p class="mb-0 fs-14 fw-semibold text-dark"><span class="fw-normal">Total:</span> {{ $currency_symbol }}{{ number_format($order->total ?? 0, 2) }}</p>
            <h6 class="mb-0 fw-semibold d-flex align-items-center gap-1"><i class="icon-clock fs-14"></i> {{ $order->created_at->format('h:i A') }}</h6>
        </div>
        <div class="mb-3 pb-3 border-bottom">
            <div class="orders-list">
                @foreach($order->items->take(4) as $oi)
                <div class="orders text-dark mb-2">
                    <p><span class="dot"></span>{{ $oi->item_name }}</p>
                    <span class="line"></span>
                    <p class="text-dark">×{{ $oi->quantity }}</p>
                </div>
                @endforeach
                @if($order->notes)
                <div class="bg-light rounded py-1 px-2 mb-2">
                    <p class="mb-0 fw-medium d-flex align-items-center text-dark"><i class="icon-badge-info me-1"></i> Notes : {{ Str::limit($order->notes, 40) }}</p>
                </div>
                @endif
                @if($order->items->count() > 4)
                <div class="view-all mt-1">
                    <span class="fw-semibold fs-14 mb-0 text-primary">+{{ $order->items->count() - 4 }} More</span>
                </div>
                @endif
            </div>
        </div>
        @php $paymentStatus = $order->payment_status ?? 'unpaid'; @endphp
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <span class="badge mb-0 {{ $paymentStatus === 'paid' ? 'badge-soft-success' : 'badge-soft-warning' }}">
                <i class="icon-{{ $paymentStatus === 'paid' ? 'circle-check' : 'circle-alert' }} me-1"></i> {{ $paymentStatus === 'paid' ? 'Paid' : 'Unpaid' }}
            </span>
            @if($order->status !== 'cancelled')
            <form action="{{ route('orders.update-payment-status', $order) }}" method="POST" class="d-inline">
                @csrf
                @method('PATCH')
                <input type="hidden" name="payment_status" value="{{ $paymentStatus === 'paid' ? 'unpaid' : 'paid' }}">
                <button type="submit" class="btn btn-sm {{ $paymentStatus === 'paid' ? 'btn-outline-warning' : 'btn-outline-success' }}">
                    {{ $paymentStatus === 'paid' ? 'Mark Unpaid' : 'Mark Paid' }}
                </button>
            </form>
            @endif
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <p class="badge mb-0 @if($order->status === 'completed') badge-soft-success @elseif($order->status === 'cancelled') badge-soft-danger @else badge-soft-primary @endif">{{ ucfirst($order->status) }}</p>
            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="d-inline" id="order-status-form-{{ $order->id }}">
                @csrf
                @method('PATCH')
                <div class="dropdown">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">{{ ucfirst($order->status) }}</button>
                    <ul class="dropdown-menu dropdown-menu-end p-3">
                        @foreach(['pending','confirmed','preparing','ready','completed','cancelled'] as $s)
                        <li><button type="submit" name="status" value="{{ $s }}" form="order-status-form-{{ $order->id }}" class="dropdown-item rounded w-100 text-start">{{ ucfirst($s) }}</button></li>
                        @endforeach
                    </ul>
                </div>
            </form>
        </div>
    </div>
</div>
