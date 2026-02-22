<?php $page = 'earning-report'; ?>
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
                    <a href="{{url('earning-report')}}" class="nav-link d-flex align-items-center active"><i class="icon-badge-dollar-sign me-2"></i>Earning Report</a>
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
                    <a href="{{url('audit-report')}}" class="nav-link d-flex align-items-center"><i class="icon-hourglass me-2"></i>Audit Logs</a>
                </li>@endif
            </ul>
            <!-- Tabs -->

            <!-- card start -->
            <div class="card mb-0">
                <div class="card-body">


                    <div class="border-bottom earning-report-filter-wrap">
                        <div class="report-filter">
                            <div class="mb-3">
                                <label class="form-label">Start Date<span class="text-danger ms-1">*</span></label>
                                <div class="input-group w-auto input-group-flat">
                                    <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d/m/Y" placeholder="dd/mm/yyyy">
                                    <span class="input-group-text">
                                        <i class="icon-calendar-fold"></i>
                                    </span>
                                </div>
                            </div>
                        </div> <!-- end col -->
                        <div class="report-filter">
                            <div class="mb-3">
                                <label class="form-label">End Date<span class="text-danger ms-1">*</span></label>
                                <div class="input-group w-auto input-group-flat">
                                    <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d/m/Y" placeholder="dd/mm/yyyy">
                                    <span class="input-group-text">
                                        <i class="icon-calendar-fold"></i>
                                    </span>
                                </div>
                            </div>
                        </div> <!-- end col -->
                        <div class="report-filter">
                            <div class="mb-3">
                                <label class="form-label">Customer<span class="text-danger ms-1">*</span></label>
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                        Select
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                                        <h6 class="fs-14 fw-semibold mb-3">Customer</h6>
                                        <div class="input-icon-end input-icon position-relative mb-3">
                                            <span class="input-icon-addon">
                                                <i class="icon-search text-dark"></i>
                                            </span>
                                            <input type="text" class="form-control form-control-md" placeholder="Search">
                                        </div>
                                        <div class="vstack gap-2">
                                            <div>
                                                <label class="d-flex align-items-center">
                                                    <input class="form-check-input m-0 me-2" type="checkbox">Walk-in Customer
                                                </label>
                                            </div>
                                            <div>
                                                <label class="d-flex align-items-center">
                                                    <input class="form-check-input m-0 me-2" type="checkbox">Sue Allen
                                                </label>
                                            </div>
                                            <div>
                                                <label class="d-flex align-items-center">
                                                    <input class="form-check-input m-0 me-2" type="checkbox">Frank Barrett
                                                </label>
                                            </div>
                                            <div>
                                                <label class="d-flex align-items-center">
                                                    <input class="form-check-input m-0 me-2" type="checkbox">Kelley Davis
                                                </label>
                                            </div>
                                            <div>
                                                <label class="d-flex align-items-center">
                                                    <input class="form-check-input m-0 me-2" type="checkbox">Jim Vickers
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end col -->
                        <div class="report-filter">
                            <div class="mb-3">
                                <label class="form-label">Payment Method<span class="text-danger ms-1">*</span></label>
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                        Select
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end p-3 w-100">
                                        <h6 class="fs-14 fw-semibold mb-3">Payment Method</h6>
                                        <div class="input-icon-end input-icon position-relative mb-3">
                                            <span class="input-icon-addon">
                                                <i class="icon-search text-dark"></i>
                                            </span>
                                            <input type="text" class="form-control form-control-md" placeholder="Search">
                                        </div>
                                        <div class="vstack gap-2">
                                            <div>
                                                <label class="d-flex align-items-center">
                                                    <input class="form-check-input m-0 me-2" type="checkbox">Credit Card
                                                </label>
                                            </div>
                                            <div>
                                                <label class="d-flex align-items-center">
                                                    <input class="form-check-input m-0 me-2" type="checkbox">Cash
                                                </label>
                                            </div>
                                            <div>
                                                <label class="d-flex align-items-center">
                                                    <input class="form-check-input m-0 me-2" type="checkbox">PayPal
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end col -->
                        <div class="report-filter">
                            <div class="mb-3">
                                <a href="#" class="btn btn-primary d-inline-flex align-items-center">Submit</a>
                            </div>
                        </div> <!-- end col -->
                    </div>

                    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between mb-4">

                        <div class="search-input">
                            <span class="btn-searchset"><i class="icon-search fs-14"></i></span>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">

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

                    <!-- table start -->
                    <div class="table-responsive table-nowrap">
                        <table class="table mb-0 border datatable">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Table</th>
                                    <th>Grand Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->order_number ?? '#'.$order->id }}</td>
                                    <td>{{ $order->created_at ? $order->created_at->format('d M Y') : '–' }}</td>
                                    <td>{{ $order->customer_name ?? 'Walk-in' }}</td>
                                    <td>{{ $order->order_type ?? '–' }}</td>
                                    <td>{{ $order->table->name ?? '–' }}</td>
                                    <td class="fw-medium text-dark">${{ number_format($order->total ?? 0, 2) }}</td>
                                    <td><span class="badge badge-soft-success">Completed</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-4 text-muted">No completed orders in this period.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- table end -->
                    @if(isset($totalSales) && isset($totalOrders))
                    <div class="mt-3 pt-3 border-top">
                        <strong>Period total:</strong> {{ $totalOrders }} order(s), ${{ number_format($totalSales ?? 0, 2) }}
                    </div>
                    @endif
                    @if(isset($orders) && is_object($orders) && method_exists($orders, 'links'))
                    <div class="mt-2">{{ $orders->links() }}</div>
                    @endif
                </div>

            </div>
            <!-- card end -->

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
