<?php $page = 'customer'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1">
                    <h3 class="mb-0">Customer <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="input-group input-group-flat w-auto">
                        <input type="text" class="form-control" id="customer-search" placeholder="Search by name or phone">
                        <span class="input-group-text"><i class="icon-search text-dark"></i></span>
                    </div>
                    <a href="#" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_customer"><i class="icon-circle-plus me-1"></i>Add New</a>
                </div>
            </div>
            <!-- End Page Header -->

            @if (session('success'))
                <div class="alert alert-success alert-dismissible border-0 mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible border-0 mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible border-0 mb-4" role="alert">
                    <ul class="mb-0 list-unstyled">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Row start - dynamic from orders (customer_name, customer_phone) -->
            <div class="row">
                @forelse($customers ?? [] as $c)
                <div class="col-xxl-4 col-xl-4 col-md-6 col-sm-6 customer-card" data-name="{{ strtolower($c->customer_name ?? '') }}" data-phone="{{ str_replace(' ', '', $c->customer_phone ?? '') }}">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded badge bg-light flex-shrink-0 me-2">
                                        <i class="icon-user text-dark fs-16"></i>
                                    </div>
                                    <span>
                                        <h6 class="fs-14 fw-semibold mb-0">{{ $c->customer_name ?: 'Walk-in Customer' }}</h6>
                                        <p class="mb-0">{{ $c->orders_count ?? 0 }} order(s)</p>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <p class="badge bg-light text-dark fw-medium fs-13 mb-0">#{{ str_pad($loop->iteration, 4, '0', STR_PAD_LEFT) }}</p>
                                    @if(isset($c->id))
                                    <div class="dropdown">
                                        <a href="#" class="btn btn-icon btn-sm btn-white border" data-bs-toggle="dropdown"><i class="icon-ellipsis-vertical"></i></a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a href="#" class="dropdown-item edit-customer-btn" data-id="{{ $c->id }}" data-name="{{ e($c->customer_name ?? $c->name ?? '') }}" data-phone="{{ e($c->customer_phone ?? $c->phone ?? '') }}" data-email="{{ e($c->email ?? '') }}" data-bs-toggle="modal" data-bs-target="#edit_customer"><i class="icon-pencil-line me-2"></i>Edit</a></li>
                                            <li>
                                                <form action="{{ route('customer.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this customer?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="icon-trash-2 me-2"></i>Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-flex align-items-center"><i class="icon-phone text-dark me-2"></i> Phone </span>
                                    <span class="fw-medium text-dark">{{ $c->customer_phone ?? $c->phone ?? '–' }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-flex align-items-center"><i class="icon-calendar-fold text-dark me-2"></i> Last order </span>
                                    <span class="fw-medium text-dark">{{ $c->last_order_at ? \Carbon\Carbon::parse($c->last_order_at)->format('d M, Y') : '–' }}</span>
                                </div>
                                @if(isset($c->id) && isset($c->balance))
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="d-flex align-items-center"><i class="icon-wallet text-dark me-2"></i> Balance </span>
                                    @php $bal = (float) ($c->balance ?? 0); @endphp
                                    @if($bal > 0)
                                        <span class="fw-semibold text-success">Credit {{ $currency_symbol ?? '₹' }}{{ number_format($bal, 2) }}</span>
                                    @elseif($bal < 0)
                                        <span class="fw-semibold text-danger">Due {{ $currency_symbol ?? '₹' }}{{ number_format(-$bal, 2) }}</span>
                                    @else
                                        <span class="fw-medium text-muted">{{ $currency_symbol ?? '₹' }}0.00</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                            <div class="d-flex align-items-center justify-content-between border-top pt-3">
                                <span class="badge badge-soft-success">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="icon-user-round fs-48 text-muted mb-3"></i>
                            <h5 class="mb-2">No customers yet</h5>
                            <p class="text-muted mb-0">Customers will appear here when orders are placed with a name or phone number.</p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
            <!-- Row end -->

            @if(isset($customers) && method_exists($customers, 'links'))
            <div class="d-flex justify-content-center mt-3">{{ $customers->links() }}</div>
            @endif

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

<script>
document.getElementById('customer-search') && document.getElementById('customer-search').addEventListener('input', function() {
    var q = (this.value || '').trim().toLowerCase().replace(/\s/g, '');
    document.querySelectorAll('.customer-card').forEach(function(card) {
        var name = (card.getAttribute('data-name') || '').replace(/\s/g, '');
        var phone = (card.getAttribute('data-phone') || '').replace(/\s/g, '');
        var show = !q || name.indexOf(q) !== -1 || phone.indexOf(q) !== -1;
        card.style.display = show ? '' : 'none';
    });
});
</script>
@endsection
