<?php $page = 'invoices'; ?>
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
                    <h3 class="mb-0">Invoices <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
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

            <!-- card start -->
            <div class="card mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between mb-4">

                        <div class="search-input">
                            <span class="btn-searchset"><i class="icon-search fs-14"></i></span>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">

                            <!-- filter -->
                            <a href="#" class="btn btn-white d-inline-flex align-items-center" data-bs-toggle="offcanvas" data-bs-target="#filter-offcanvas" aria-controls="filter-offcanvas">
                                <i class="icon-funnel me-2"></i>Filter
                            </a>

                            <!-- column -->
                            <div class="dropdown">
                                <a href="#" class="btn btn-icon btn-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="icon-columns-3"></i></a>
                                <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-3 pb-0">
                                    <h5 class="mb-3">Column</h5>
                                    <div id="drag-container">
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Invoice ID
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Customer
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Date
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Order Type
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Amount
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Status
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Actions
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- sort by -->
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                    Sort by : Newest
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Newest</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Oldest</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Ascending</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Descending</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @if(isset($invoices) && $invoices->hasPages())
                    <div class="mb-3">{{ $invoices->links() }}</div>
                    @endif
                    <!-- table start -->
                    <div class="table-responsive table-nowrap">
                        <table class="table mb-0 border datatable">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Order Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices ?? [] as $inv)
                                <tr>
                                    <td><a href="{{ route('invoice-details', $inv) }}">#{{ $inv->order_number ?? $inv->id }}</a></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <h6 class="fs-14 fw-normal mb-0">{{ $inv->customer_name ?: 'Walk-in' }}</h6>
                                        </div>
                                    </td>
                                    <td>{{ $inv->created_at->format('d M Y') }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $inv->order_type ?? 'dine_in')) }}</td>
                                    <td>{{ $currency_symbol }}{{ number_format($inv->total, 2) }}</td>
                                    <td>
                                        <span class="badge badge-soft-{{ $inv->invoice_payment_status_badge }}">
                                            {{ $inv->invoice_payment_status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('invoice-details', $inv) }}" class="btn btn-icon btn-sm btn-white rounded-circle me-2"><i class="icon-eye"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-4 text-muted">No invoices yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- table end -->
                </div>

            </div>
            <!-- card start -->

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
