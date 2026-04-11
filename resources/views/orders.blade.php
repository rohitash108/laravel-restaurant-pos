<?php $page = 'orders'; ?>
@extends('layout.mainlayout')
@section('content')
    <script>
        window.ordersDateRange = {
            from: @json(request('from')),
            to: @json(request('to'))
        };
    </script>
    <div class="page-wrapper">
        <div class="content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1">
                    <h3 class="mb-0">Orders <a href="{{ route('orders') }}" class="btn btn-icon btn-sm btn-white rounded-circle ms-2" title="Refresh"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="daterangepick custom-date form-control w-auto d-flex align-items-center justify-content-between">
                        <i class="icon-calendar-fold text-dark fs-14 me-2"></i>
                        <span class="reportrange-picker"></span>
                    </div>
                    <a href="{{ url('pos') }}" class="btn btn-primary d-inline-flex align-items-center"><i class="icon-circle-plus me-1"></i>Add New</a>
                </div>
            </div>

            @php
                $totalAll = ($orders ?? new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20))->total();
                $counts = $counts ?? ['pending' => 0, 'confirmed' => 0, 'preparing' => 0, 'ready' => 0, 'completed' => 0, 'cancelled' => 0];
                $pendingOrders = $pendingOrders ?? collect();
                $inProgressOrders = $inProgressOrders ?? collect();
                $completedOrders = $completedOrders ?? collect();
                $cancelledOrders = $cancelledOrders ?? collect();
                $totalPending = $pendingOrders->count();
                $totalInProgress = $inProgressOrders->count();
                $totalCompleted = $completedOrders->count();
                $totalCancelled = $cancelledOrders->count();
            @endphp

            <div class="row orders-list-four">
                <div class="col-xxl-2 col-lg-4 col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <span class="fs-13 fw-medium mb-1 d-block">Confirmed</span>
                                    <h4 class="mb-0">{{ $counts['confirmed'] ?? 0 }}</h4>
                                </div>
                                <div class="avatar bg-soft-secondary fs-20 rounded-circle flex-shrink-0">
                                    <i class="icon-bookmark-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-lg-4 col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <span class="fs-13 fw-medium mb-1 d-block">Pending</span>
                                    <h4 class="mb-0">{{ $counts['pending'] ?? 0 }}</h4>
                                </div>
                                <div class="avatar bg-soft-primary fs-20 rounded-circle flex-shrink-0">
                                    <i class="icon-circle-arrow-out-down-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-lg-4 col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <span class="fs-13 fw-medium mb-1 d-block">Processing</span>
                                    <h4 class="mb-0">{{ $counts['preparing'] ?? 0 }}</h4>
                                </div>
                                <div class="avatar bg-soft-orange fs-20 rounded-circle flex-shrink-0">
                                    <i class="icon-loader"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-lg-4 col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <span class="fs-13 fw-medium mb-1 d-block">Ready</span>
                                    <h4 class="mb-0">{{ $counts['ready'] ?? 0 }}</h4>
                                </div>
                                <div class="avatar bg-soft-purple fs-20 rounded-circle flex-shrink-0">
                                    <i class="icon-bike"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-lg-4 col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <span class="fs-13 fw-medium mb-1 d-block">Completed</span>
                                    <h4 class="mb-0">{{ $counts['completed'] ?? 0 }}</h4>
                                </div>
                                <div class="avatar badge-soft-success fs-20 rounded-circle flex-shrink-0">
                                    <i class="icon-send"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-2 col-lg-4 col-md-4 col-sm-6">
                    <div class="card">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                <div>
                                    <span class="fs-13 fw-medium mb-1 d-block">Cancelled</span>
                                    <h4 class="mb-0">{{ $counts['cancelled'] ?? 0 }}</h4>
                                </div>
                                <div class="avatar bg-soft-danger fs-20 rounded-circle flex-shrink-0">
                                    <i class="icon-user-x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-4 mb-4 border-bottom">
                <ul class="nav nav-tabs nav-tabs-solid border-0" role="tablist">
                    <li class="nav-item">
                        <a href="#order-tab1" class="nav-link active" data-bs-toggle="tab">All Orders ({{ $totalAll }})</a>
                    </li>
                    <li class="nav-item">
                        <a href="#order-tab2" class="nav-link" data-bs-toggle="tab">Pending ({{ $totalPending }})</a>
                    </li>
                    <li class="nav-item">
                        <a href="#order-tab3" class="nav-link" data-bs-toggle="tab">In Progress ({{ $totalInProgress }})</a>
                    </li>
                    <li class="nav-item">
                        <a href="#order-tab4" class="nav-link" data-bs-toggle="tab">Completed ({{ $totalCompleted }})</a>
                    </li>
                    <li class="nav-item">
                        <a href="#order-tab5" class="nav-link" data-bs-toggle="tab">Cancelled ({{ $totalCancelled }})</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="active-item d-flex align-items-center justify-content-between gap-1 p-1 border rounded bg-white">
                        <a href="{{ url('orders') }}" class="btn btn-sm btn-icon btn-primary" aria-label="grid"><i class="icon-grid-2x2"></i></a>
                        <a href="{{ url('kanban-view') }}" class="btn btn-sm btn-icon" aria-label="kanban"><i class="icon-square-kanban"></i></a>
                    </div>
                    <div class="input-group input-group-flat w-auto">
                        <input type="text" class="form-control" placeholder="Search" id="orders-search">
                        <span class="input-group-text"><i class="icon-search text-dark"></i></span>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="orders-tab-content">
                <div class="tab-pane show active" id="order-tab1">
                    <div class="row">
                        @forelse($orders as $order)
                        <div class="col-xxl-4 col-xl-6 col-md-6 d-flex order-card-row" data-order-number="{{ $order->order_number }}" data-customer="{{ e($order->customer_name ?? '') }}">
                            @include('partials.order-card', ['order' => $order])
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <i class="icon-shopping-bag fs-48 text-muted mb-3"></i>
                                    <h5 class="mb-2">No orders yet</h5>
                                    <p class="text-muted mb-0">Orders will appear here. <a href="{{ url('pos') }}">Create an order</a> from POS.</p>
                                </div>
                            </div>
                        </div>
                        @endforelse
                    </div>
                    @if($orders->hasPages())
                    <nav class="pagination-nav mt-4">
                        <div class="d-flex justify-content-center">
                            {{ $orders->withQueryString()->links() }}
                        </div>
                    </nav>
                    @endif
                </div>

                <div class="tab-pane" id="order-tab2">
                    <div class="row">
                        @forelse($pendingOrders as $order)
                        <div class="col-xxl-4 col-xl-6 col-md-6 d-flex order-card-row" data-order-number="{{ $order->order_number }}" data-customer="{{ e($order->customer_name ?? '') }}">
                            @include('partials.order-card', ['order' => $order])
                        </div>
                        @empty
                        <div class="col-12"><p class="text-muted text-center py-5 mb-0">No pending orders.</p></div>
                        @endforelse
                    </div>
                </div>

                <div class="tab-pane" id="order-tab3">
                    <div class="row">
                        @forelse($inProgressOrders as $order)
                        <div class="col-xxl-4 col-xl-6 col-md-6 d-flex order-card-row" data-order-number="{{ $order->order_number }}" data-customer="{{ e($order->customer_name ?? '') }}">
                            @include('partials.order-card', ['order' => $order])
                        </div>
                        @empty
                        <div class="col-12"><p class="text-muted text-center py-5 mb-0">No orders in progress.</p></div>
                        @endforelse
                    </div>
                </div>

                <div class="tab-pane" id="order-tab4">
                    <div class="row">
                        @forelse($completedOrders as $order)
                        <div class="col-xxl-4 col-xl-6 col-md-6 d-flex order-card-row" data-order-number="{{ $order->order_number }}" data-customer="{{ e($order->customer_name ?? '') }}">
                            @include('partials.order-card', ['order' => $order])
                        </div>
                        @empty
                        <div class="col-12"><p class="text-muted text-center py-5 mb-0">No completed orders.</p></div>
                        @endforelse
                    </div>
                </div>

                <div class="tab-pane" id="order-tab5">
                    <div class="row">
                        @forelse($cancelledOrders as $order)
                        <div class="col-xxl-4 col-xl-6 col-md-6 d-flex order-card-row" data-order-number="{{ $order->order_number }}" data-customer="{{ e($order->customer_name ?? '') }}">
                            @include('partials.order-card', ['order' => $order])
                        </div>
                        @empty
                        <div class="col-12"><p class="text-muted text-center py-5 mb-0">No cancelled orders.</p></div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    (function() {
        var searchEl = document.getElementById('orders-search');
        var tabContent = document.getElementById('orders-tab-content');
        if (!searchEl || !tabContent) return;
        function applySearch() {
            var q = (searchEl.value || '').trim().toLowerCase();
            var active = tabContent.querySelector('.tab-pane.active');
            if (!active) active = tabContent.querySelector('.tab-pane.show');
            var rows = active ? active.querySelectorAll('.order-card-row') : [];
            rows.forEach(function(row) {
                var num = (row.getAttribute('data-order-number') || '').toLowerCase();
                var cust = (row.getAttribute('data-customer') || '').toLowerCase();
                row.style.display = !q || num.indexOf(q) !== -1 || cust.indexOf(q) !== -1 ? '' : 'none';
            });
        }
        searchEl.addEventListener('input', applySearch);
        tabContent.closest('.content').querySelectorAll('.nav-tabs [data-bs-toggle="tab"]').forEach(function(tab) {
            tab.addEventListener('shown.bs.tab', applySearch);
        });
    })();

    // Mobile / touch: open receipt & thermal print in same tab (Android Chrome print preview; avoids broken pop-ups).
    (function () {
        function useSameTab() {
            return (window.matchMedia && window.matchMedia('(max-width: 768px)').matches)
                || (window.matchMedia && window.matchMedia('(pointer: coarse)').matches)
                || /Android|iPhone|iPad/i.test(navigator.userAgent || '');
        }
        if (!useSameTab()) return;
        document.querySelectorAll('.order-card-print-link, .order-card-receipt-link').forEach(function (a) {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                window.location.href = a.getAttribute('href');
            });
        });
    })();
    </script>

@endsection
