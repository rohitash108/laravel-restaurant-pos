<?php $page = 'audit-report'; ?>
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
                    <h3 class="mb-0">Reports <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="icon-upload me-1"></i>Export
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-3">
                            <li>
                                <a href="#" class="dropdown-item rounded">Export as PDF</a>
                            </li>
                            <li>
                                <a href="#" class="dropdown-item rounded">Export as Excel</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Tabs -->
            <ul class="nav nav-tabs nav-bordered nav-bordered-primary mb-4" role="tablist">
                <li class="nav-item">
                    <a href="{{url('earning-report')}}" class="nav-link d-flex align-items-center"><i class="icon-badge-dollar-sign me-2"></i>Earning Report</a>
                </li>
                <li class="nav-item">
                    <a href="{{url('order-report')}}" class="nav-link d-flex align-items-center"><i class="icon-list-todo me-2"></i>Order Report</a>
                </li>
                <li class="nav-item">
                    <a href="{{url('sales-report')}}" class="nav-link d-flex align-items-center"><i class="icon-shopping-bag me-2"></i>Sales Report</a>
                </li>
                <li class="nav-item">
                    <a href="{{url('customer-report')}}" class="nav-link d-flex align-items-center"><i class="icon-users me-2"></i>Customer Report</a>
                </li>
                @if(false)<li class="nav-item">
                    <a href="{{url('audit-report')}}" class="nav-link d-flex align-items-center active"><i class="icon-hourglass me-2"></i>Audit Logs</a>
                </li>@endif
            </ul>
            <!-- Tabs -->

            <!-- card start -->
            <div class="card mb-0">
                <div class="card-body schedule-timeline">
                    @forelse($orders ?? [] as $order)
                    <div class="d-sm-flex align-items-center">
                        <div class="d-flex align-items-center active-time">
                            <span class="{{ $loop->last ? 'timeline-border-none' : 'timeline-border' }} d-flex align-items-center justify-content-center bg-white">
                                <i class="icon-badge-dollar-sign text-gray-2 fs-18"></i>
                            </span>
                        </div>
                        <div class="flex-fill {{ $loop->last ? '' : 'timeline-hrline' }}">
                            <div class="p-3">
                                <p class="fw-medium text-dark mb-1">Order {{ $order->order_number ?? '#'.$order->id }} — {{ $order->customer_name ?: 'Walk-in' }} — {{ $order->order_type ?? '–' }} — Table: {{ $order->table->name ?? '–' }}</p>
                                <span>{{ $order->created_at ? $order->created_at->format('d M Y \a\t h:i A') : '–' }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 text-center text-muted">No order activity yet.</div>
                    @endforelse
                </div> <!-- end card body -->

            </div>
            <!-- card end -->

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
