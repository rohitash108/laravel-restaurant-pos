<?php $page = 'payments'; ?>
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
                    <h3 class="mb-0">Payments <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
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
                                                Transaction ID
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Order ID
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Token No
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
                                                Type
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Menus
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Grand Total
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

                    @if(isset($payments) && $payments->hasPages())
                    <div class="mb-3">{{ $payments->links() }}</div>
                    @endif
                    <!-- table start -->
                    <div class="table-responsive table-nowrap">
                        <table class="table mb-0 border datatable">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Order ID</th>
                                    <th>Token No</th>
                                        <th>Customer</th>
                                    <th>Type</th>
                                    <th>Menus</th>
                                    <th>Grand Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments ?? [] as $payment)
                                <tr>
                                    <td>#{{ $payment->id }}</td>
                                    <td><a href="{{ url('orders') }}">#{{ $payment->order_number }}</a></td>
                                    <td>–</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <h6 class="fs-14 fw-normal mb-0">{{ $payment->customer_name ?: 'Walk-in' }}</h6>
                                        </div>
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $payment->order_type ?? 'dine_in')) }}</td>
                                    <td>{{ $payment->items->count() }}</td>
                                    <td><p class="text-dark fw-medium mb-0">${{ number_format($payment->total, 2) }}</p></td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-4 text-muted">No payments yet. Completed orders appear here.</td></tr>
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
