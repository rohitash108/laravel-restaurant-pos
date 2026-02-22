<?php $page = 'kanban-view'; ?>
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
                    <h3 class="mb-0">Orders <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="daterangepick custom-date form-control w-auto d-flex align-items-center justify-content-between">
                        <i class="icon-calendar-fold text-dark fs-14 me-2"></i>
                        <span class="reportrange-picker"></span>
                    </div>
                    <a href="{{url('pos')}}" class="btn btn-primary d-inline-flex align-items-center"><i class="icon-circle-plus me-1"></i>Add New</a>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Start row -->
            <div class="row gx-3">
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
                <!-- end col -->

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
                <!-- end col -->

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
                <!-- end col -->

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
                <!-- end col -->

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
                <!-- end col -->

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
                <!-- end col -->
            </div>
            <!-- End row -->

            <!-- Start Tabs -->
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-4 mb-4 border-bottom">
                <h5 class="mb-0">Orders</h5>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="active-item d-flex align-items-center justify-content-between gap-1 p-1 border rounded bg-white">
                        <a href="{{url('orders')}}" class="btn btn-sm btn-icon" aria-label="grid"><i class="icon-grid-2x2"></i></a>
                        <a href="{{url('kanban-view')}}" class="btn btn-sm btn-icon btn-primary" aria-label="kanban"><i class="icon-square-kanban"></i></a>
                    </div>
                    <div class="input-group input-group-flat w-auto">
                        <input type="text" class="form-control" placeholder="Search">
                        <span class="input-group-text">
                            <i class="icon-search text-dark"></i>
                        </span>
                    </div>
                </div>
            </div>
            <!-- End Tabs -->

            <!-- Kanban Card drag Item -->
            <div class="d-flex gap-3 align-items-start overflow-auto project-status" data-plugin="dragula" data-containers='["drag-one", "drag-two", "drag-three", "drag-ready", "drag-four", "drag-five"]'>
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="top-item d-flex align-items-center justify-content-between bg-gray rounded py-3 px-3 mb-4">
                            <span class="mb-0 d-flex align-items-center gap-2 text-white fw-semibold"> <i class="icon-newspaper"></i> Pending </span> <span class="text-white">{{ $counts['pending'] ?? 0 }}</span>
                        </div>

                        <div class="kanban-drag" id="drag-one">
                            @foreach($ordersByStatus['pending'] ?? [] as $order)
                            <div class="card flex-fill kanban-card mb-3">
                                <div class="card-body">
                                    <h6 class="mb-1 fs-14 fw-semibold">#{{ $order->order_number }}</h6>
                                    <p class="mb-0 small">{{ $order->customer_name ?: 'Walk-in' }} · {{ $order->table?->name ?? '–' }}</p>
                                    <p class="mb-0 mt-2 small text-muted">{{ $order->items->count() }} items · ${{ number_format($order->total, 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>

                <div class="card mb-0">
                    <div class="card-body">
                        <div class="top-item d-flex align-items-center justify-content-between bg-secondary rounded py-3 px-3 mb-4">
                            <span class="mb-0 d-flex align-items-center gap-2 text-white fw-semibold"> <i class="icon-package-2"></i> Confirmed </span> <span class="text-white">{{ $counts['confirmed'] ?? 0 }}</span>
                        </div>

                        <div class="kanban-drag" id="drag-two">
                            @foreach($ordersByStatus['confirmed'] ?? [] as $order)
                            <div class="card flex-fill kanban-card mb-3">
                                <div class="card-body">
                                    <h6 class="mb-1 fs-14 fw-semibold">#{{ $order->order_number ?? $order->id }}</h6>
                                    <p class="mb-0 small">{{ $order->customer_name ?: 'Walk-in' }} · {{ $order->table?->name ?? '–' }}</p>
                                    <p class="mb-0 mt-2 small text-muted">{{ $order->items->count() }} items · ${{ number_format($order->total ?? 0, 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>

                <div class="card mb-0">
                    <div class="card-body">
                        <div class="top-item d-flex align-items-center justify-content-between bg-orange rounded py-3 px-3 mb-4">
                            <span class="mb-0 d-flex align-items-center gap-2 text-white fw-semibold"> <i class="icon-loader"></i> Processing </span> <span class="text-white">{{ $counts['preparing'] ?? 0 }}</span>
                        </div>

                        <div class="kanban-drag" id="drag-three">
                            @foreach($ordersByStatus['preparing'] ?? [] as $order)
                            <div class="card flex-fill kanban-card mb-3">
                                <div class="card-body">
                                    <h6 class="mb-1 fs-14 fw-semibold">#{{ $order->order_number ?? $order->id }}</h6>
                                    <p class="mb-0 small">{{ $order->customer_name ?: 'Walk-in' }} · {{ $order->table?->name ?? '–' }}</p>
                                    <p class="mb-0 mt-2 small text-muted">{{ $order->items->count() }} items · ${{ number_format($order->total ?? 0, 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>

                <div class="card mb-0">
                    <div class="card-body">
                        <div class="top-item d-flex align-items-center justify-content-between bg-purple rounded py-3 px-3 mb-4">
                            <span class="mb-0 d-flex align-items-center gap-2 text-white fw-semibold"> <i class="icon-bike"></i> Ready </span> <span class="text-white">{{ $counts['ready'] ?? 0 }}</span>
                        </div>

                        <div class="kanban-drag" id="drag-ready">
                            @foreach($ordersByStatus['ready'] ?? [] as $order)
                            <div class="card flex-fill kanban-card mb-3">
                                <div class="card-body">
                                    <h6 class="mb-1 fs-14 fw-semibold">#{{ $order->order_number ?? $order->id }}</h6>
                                    <p class="mb-0 small">{{ $order->customer_name ?: 'Walk-in' }} · {{ $order->table?->name ?? '–' }}</p>
                                    <p class="mb-0 mt-2 small text-muted">{{ $order->items->count() }} items · ${{ number_format($order->total ?? 0, 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>

                <div class="card mb-0">
                    <div class="card-body">
                        <div class="top-item d-flex align-items-center justify-content-between bg-success rounded py-3 px-3 mb-4">
                            <span class="mb-0 d-flex align-items-center gap-2 text-white fw-semibold"> <i class="icon-check-check"></i> Completed </span> <span class="text-white">{{ $counts['completed'] ?? 0 }}</span>
                        </div>

                        <div class="kanban-drag" id="drag-four">
                            @foreach($ordersByStatus['completed'] ?? [] as $order)
                            <div class="card flex-fill kanban-card mb-3">
                                <div class="card-body">
                                    <h6 class="mb-1 fs-14 fw-semibold">#{{ $order->order_number ?? $order->id }}</h6>
                                    <p class="mb-0 small">{{ $order->customer_name ?: 'Walk-in' }} · {{ $order->table?->name ?? '–' }}</p>
                                    <p class="mb-0 mt-2 small text-muted">{{ $order->items->count() }} items · ${{ number_format($order->total ?? 0, 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>

                <div class="card mb-0">
                    <div class="card-body">
                        <div class="top-item d-flex align-items-center justify-content-between bg-danger rounded py-3 px-3 mb-4">
                            <span class="mb-0 d-flex align-items-center gap-2 text-white fw-semibold"> <i class="icon-x"></i> Cancelled </span> <span class="text-white">{{ $counts['cancelled'] ?? 0 }}</span>
                        </div>

                        <div class="kanban-drag" id="drag-five">
                            @foreach($ordersByStatus['cancelled'] ?? [] as $order)
                            <div class="card flex-fill kanban-card mb-3">
                                <div class="card-body">
                                    <h6 class="mb-1 fs-14 fw-semibold">#{{ $order->order_number ?? $order->id }}</h6>
                                    <p class="mb-0 small">{{ $order->customer_name ?: 'Walk-in' }} · {{ $order->table?->name ?? '–' }}</p>
                                    <p class="mb-0 mt-2 small text-muted">{{ $order->items->count() }} items · ${{ number_format($order->total ?? 0, 2) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
            <!-- Kanban Card drag Item -->


        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
