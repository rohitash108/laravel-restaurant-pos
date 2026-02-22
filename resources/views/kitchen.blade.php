<?php $page = 'kitchen'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-sm-center justify-content-between flex-sm-row flex-column flex-wrap gap-3 mb-4">
                <div>
                    <h3 class="mb-0">Kitchen <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                    <div class="d-inline-flex align-items-center justify-content-between rounded-pill bg-white ps-2 pe-3 py-2 gap-3 border">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm rounded-circle bg-gray">
                                <i class="icon-newspaper fs-14"></i>
                            </div>
                            <p class="mb-0 text-dark fw-medium">Pending</p>
                        </div>
                        <h5 class="fs-18px fw-semibold mb-0">{{ $counts['pending'] ?? 0 }}</h5>
                    </div>
                    <div class="d-inline-flex align-items-center justify-content-between rounded-pill bg-white ps-2 pe-3 py-2 gap-3 border">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm rounded-circle bg-secondary">
                                <i class="icon-package-2 fs-14"></i>
                            </div>
                            <p class="mb-0 text-dark fw-medium">In Kitchen</p>
                        </div>
                        <h5 class="fs-18px fw-semibold mb-0">{{ ($counts['confirmed'] ?? 0) + ($counts['preparing'] ?? 0) }}</h5>
                    </div>
                    <div class="d-inline-flex align-items-center justify-content-between rounded-pill bg-white ps-2 pe-3 py-2 gap-3 border">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm rounded-circle bg-danger">
                                <i class="icon-clock-alert fs-14"></i>
                            </div>
                            <p class="mb-0 text-dark fw-medium">Ready</p>
                        </div>
                        <h5 class="fs-18px fw-semibold mb-0">{{ $counts['ready'] ?? 0 }}</h5>
                    </div>
                    <div class="d-inline-flex align-items-center justify-content-between rounded-pill bg-white ps-2 pe-3 py-2 gap-3 border">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm rounded-circle bg-success">
                                <i class="icon-check-check fs-14"></i>
                            </div>
                            <p class="mb-0 text-dark fw-medium">Completed</p>
                        </div>
                        <h5 class="fs-18px fw-semibold mb-0">{{ $counts['completed'] ?? 0 }}</h5>
                    </div>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="input-group input-group-flat w-auto">
                        <input type="text" class="form-control" placeholder="Search">
                        <span class="input-group-text">
                            <i class="icon-search text-dark"></i>
                        </span>
                    </div>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- start row - dynamic orders -->
            <div class="row g-4">
                @forelse($orders ?? [] as $order)
                <div class="col-xl-4 col-lg-6 col-md-6 d-flex">
                    <div class="card flex-fill mb-0">
                        <div class="card-header bg-gray">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar rounded-circle bg-white">
                                        <i class="icon-hand-platter fs-24 text-dark"></i>
                                    </div>
                                    <p class="mb-0 text-white fw-semibold fs-14px">{{ $order->customer_name ?: 'Walk-in' }}
                                        <span class="fs-13 fw-normal d-block mt-1">{{ ucfirst(str_replace('_', ' ', $order->order_type ?? 'dine_in')) }}</span>
                                    </p>
                                </div>
                                <span class="badge bg-white text-center text-dark">#{{ $order->order_number }}</span>
                            </div>
                        </div>
                        <div class="card-body border-bottom">
                            <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                <h6 class="mb-0 fw-normal fs-14">Table : <span class="fw-semibold">{{ $order->table?->name ?? '–' }}</span></h6>
                                <p class="mb-0 fw-normal text-dark">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="orders-list mb-4">
                                @foreach($order->items as $oi)
                                <div class="orders text-dark mb-2">
                                    <p><span class="dot success"></span>{{ $oi->item_name }}</p>
                                    <p class="text-dark">×{{ $oi->quantity }}</p>
                                </div>
                                @endforeach
                                @if($order->notes)
                                <div class="bg-light rounded py-1 px-2 mt-2">
                                    <p class="mb-0 fw-medium d-flex align-items-center text-dark"><i class="icon-badge-info me-1"></i> Notes : {{ Str::limit($order->notes, 50) }}</p>
                                </div>
                                @endif
                            </div>
                            <div class="d-flex align-items-center justify-content-between gap-3">
                                <span class="badge badge-soft-{{ $order->status === 'ready' ? 'success' : 'primary' }}">{{ ucfirst($order->status) }}</span>
                                <a href="{{ route('orders.update-status', $order) }}" class="btn btn-sm btn-white" onclick="event.preventDefault(); document.getElementById('kitchen-done-{{ $order->id }}').submit();">Mark Done</a>
                                <form id="kitchen-done-{{ $order->id }}" action="{{ route('orders.update-status', $order) }}" method="POST" class="d-none">@csrf @method('PATCH')<input type="hidden" name="status" value="ready"></form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="icon-drumstick fs-48 text-muted mb-3"></i>
                            <h5 class="mb-2">No orders in kitchen</h5>
                            <p class="text-muted mb-0">New and in-progress orders will appear here.</p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
            <!-- end row -->

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
