<?php $page = 'dashboard'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content pb-0">

            <!-- Page Header -->
            <div class="d-flex align-items-center flex-wrap gap-3 mb-4">
                <div class="flex-grow-1">
                    <h3 class="mb-0">Dashboard <a href="{{ route('dashboard') }}" class="btn btn-icon btn-sm btn-white rounded-circle ms-2" title="Refresh"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                {{-- Sync Data, Export, and date filter buttons removed --}}
            </div>
            <!-- End Page Header -->

            {{-- Super Admin Module: only visible when logged in as super admin --}}
            @if(auth()->user() && auth()->user()->isSuperAdmin())
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-2 border-primary shadow-sm">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center flex-wrap gap-3">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md avatar-rounded bg-primary me-2"><i class="icon-user-cog fs-20 text-white"></i></span>
                                    <div>
                                        <h5 class="mb-0">Super Admin</h5>
                                        <p class="text-muted fs-13 mb-0">Manage all restaurants and platform settings.</p>
                                    </div>
                                </div>
                                <div class="ms-auto d-flex gap-2 flex-wrap">
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary d-inline-flex align-items-center"><i class="icon-layout-dashboard me-1"></i>Super Admin Dashboard</a>
                                    <a href="{{ route('admin.restaurants.index') }}" class="btn btn-white d-inline-flex align-items-center"><i class="icon-warehouse me-1"></i>Manage Restaurants</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- start row -->
            <div class="row">

                <div class="col-xl-3 col-md-6 d-flex">
                    <div class="card z-1 w-100 overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="d-inline-flex align-items-center mb-2">{{ number_format($total_orders ?? 0) }}@if(isset($chart_sales_percent) && $chart_sales_percent > 0)<span class="badge badge-sm bg-success text-white rounded-pill ms-2">+{{ $chart_sales_percent }}%</span>@endif</h4>
                                    <p class="mb-0">Total Orders</p>
                                </div>
                                <div class="avatar avatar-lg avatar-rounded bg-purple count-icon border-end border-purple border-2">
                                    <i class="icon-box fs-24"></i>
                                </div>
                            </div>
                            <img src="{{URL::asset('build/img/bg/order-bg.png')}}" alt="bg" class="img-fluid position-absolute top-0 end-0 z-n1 custom-line-img">
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

                <div class="col-xl-3 col-md-6 d-flex">
                    <div class="card z-1 w-100 overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="d-inline-flex align-items-center mb-2">{{ $currency_symbol }}{{ number_format($total_sales ?? 0, 2) }}@if(isset($chart_sales_percent) && $chart_sales_percent > 0)<span class="badge badge-sm bg-success text-white rounded-pill ms-2">+{{ $chart_sales_percent }}%</span>@endif</h4>
                                    <p class="mb-0">Total Sales</p>
                                </div>
                                <div class="avatar avatar-lg avatar-rounded bg-primary count-icon border-end border-primary border-2">
                                    <i class="icon-badge-dollar-sign fs-24"></i>
                                </div>
                            </div>
                            <img src="{{URL::asset('build/img/bg/order-bg.png')}}" alt="bg" class="img-fluid position-absolute top-0 end-0 z-n1 custom-line-img">
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

                <div class="col-xl-3 col-md-6 d-flex">
                    <div class="card z-1 w-100 overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="d-inline-flex align-items-center mb-2">{{ $currency_symbol }}{{ number_format($average_order_value ?? 0, 2) }}</h4>
                                    <p class="mb-0">Average Value</p>
                                </div>
                                <div class="avatar avatar-lg avatar-rounded bg-orange count-icon border-end border-orange border-2">
                                    <i class="icon-diamond-percent fs-24"></i>
                                </div>
                            </div>
                            <img src="{{URL::asset('build/img/bg/order-bg.png')}}" alt="bg" class="img-fluid position-absolute top-0 end-0 z-n1 custom-line-img">
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

                <div class="col-xl-3 col-md-6 d-flex">
                    <div class="card z-1 w-100 overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h4 class="d-inline-flex align-items-center mb-2">{{ number_format($reservations_count ?? 0) }}</h4>
                                    <p class="mb-0">Reservations</p>
                                </div>
                                <div class="avatar avatar-lg avatar-rounded bg-success count-icon border-end border-success border-2">
                                    <i class="icon-calendar-fold fs-24"></i>
                                </div>
                            </div>
                            <img src="{{URL::asset('build/img/bg/order-bg.png')}}" alt="bg" class="img-fluid position-absolute top-0 end-0 z-n1 custom-line-img">
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

            </div>
            <!-- end row -->

            <!-- start row -->
            <div class="row">

                <div class="col-xxl-8 col-xl-7 col-lg-12 d-flex">
                    <div class="card flex-fill w-100">
                        <div class="card-body pb-0">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs shadow border text-dark fs-14 me-2"><i class="icon-dollar-sign"></i></div>
                                    <h5 class="mb-0">Total Revenue</h5>
                                </div>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-white dropdown-toggle" data-bs-toggle="dropdown"  aria-haspopup="false" aria-expanded="false">
                                        Weekly
                                    </a>
                                    <ul class="dropdown-menu p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Weekly</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Monthly</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Yearly</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-primary me-2">
                                        <i class="icon-arrow-up fs-20"></i>
                                    </div>
                                    <div>
                                        <p class="mb-1">Total Revenue</p>
                                        <h4 class="mb-0">{{ $currency_symbol }}{{ number_format($total_sales ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                                <p class="d-inline-flex align-items-center mb-0"><i class="icon-square text-primary me-1"></i>Revenue</p>
                            </div>
                            <div id="revenue-chart"></div>
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

                <div class="col-xxl-4 col-xl-5 col-lg-12 d-flex">
                    <div class="card flex-fill w-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs shadow border text-dark fs-14 me-2"><i class="icon-donut"></i></div>
                                    <h5 class="mb-0">Top Selling Item</h5>
                                </div>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-white dropdown-toggle" data-bs-toggle="dropdown"  aria-haspopup="false" aria-expanded="false">
                                        All
                                    </a>
                                    <ul class="dropdown-menu p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">All</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Sea Food</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Pizza</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Salads</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            @if(isset($top_selling_items) && $top_selling_items->isNotEmpty())
                                @php $maxQty = $top_selling_items->max('total_qty') ?: 1; @endphp
                                <div class="badge badge-soft-success text-start d-flex align-items-center text-wrap px-3 py-2 mb-3">
                                    <img src="{{URL::asset('build/img/icons/spark.png')}}" alt="icon" class="img-fluid me-2">
                                    Most Ordered : {{ $top_selling_items->first()->item_name }}
                                </div>
                                @foreach($top_selling_items->take(5) as $idx => $row)
                                <div class="d-flex align-items-center justify-content-between {{ $loop->last ? 'mb-0' : 'mb-3' }}">
                                    <h6 class="fs-14 fw-semibold mb-0"><span class="text-body">#{{ $idx + 1 }}</span> {{ $row->item_name }}</h6>
                                    <div class="d-flex align-items-center gap-4 w-50">
                                        <div class="progress-stacked progress-sm w-100">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $maxQty ? round($row->total_qty / $maxQty * 100) : 0 }}%"></div>
                                        </div>
                                        <p class="fs-14 text-dark fw-medium mb-0">{{ $row->total_qty }}</p>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="badge badge-soft-success text-start d-flex align-items-center text-wrap px-3 py-2 mb-3">
                                    <img src="{{URL::asset('build/img/icons/spark.png')}}" alt="icon" class="img-fluid me-2">
                                    Most Ordered : —
                                </div>
                                <p class="text-muted small mb-0">No orders yet. Top selling items will appear here.</p>
                            @endif
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

            </div>
            <!-- end row -->

                <!-- start row -->
            <div class="row">

                <div class="col-lg-6 col-xxl-4 d-flex">
                    <div class="card flex-fill w-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs shadow border text-dark fs-14 me-2"><i class="icon-croissant"></i></div>
                                    <h5 class="mb-0">Category Statistics</h5>
                                </div>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-white dropdown-toggle" data-bs-toggle="dropdown"  aria-haspopup="false" aria-expanded="false">
                                        Weekly
                                    </a>
                                    <ul class="dropdown-menu p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Weekly</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Monthly</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Yearly</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div id="category-chart"></div>
                            @php
                                $typeLabels = ['dine_in' => 'Dine In', 'takeaway' => 'Take Away', 'delivery' => 'Delivery', 'qr_order' => 'QR Order'];
                                $typeIcons = ['dine_in' => 'icon-shopping-bag', 'takeaway' => 'icon-shopping-bag', 'delivery' => 'icon-bike', 'qr_order' => 'icon-qrcode'];
                                $typeClasses = ['dine_in' => 'bg-primary', 'takeaway' => 'bg-secondary', 'delivery' => 'bg-success', 'qr_order' => 'bg-purple'];
                            @endphp
                            @forelse($orders_by_type ?? [] as $type => $count)
                            <div class="d-flex align-items-center justify-content-between border-bottom p-2">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm avatar-rounded {{ $typeClasses[$type] ?? 'bg-primary' }} me-2">
                                        <i class="{{ $typeIcons[$type] ?? 'icon-shopping-bag' }}"></i>
                                    </span>
                                    <h6 class="fs-14 fw-medium mb-0">{{ $typeLabels[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}</h6>
                                </div>
                                <p class="fw-medium mb-0">{{ $count }} Orders</p>
                            </div>
                            @empty
                            <div class="d-flex align-items-center justify-content-between border-bottom p-2">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm avatar-rounded bg-primary me-2"><i class="icon-shopping-bag"></i></span>
                                    <h6 class="fs-14 fw-medium mb-0">Take Away</h6>
                                </div>
                                <p class="fw-medium mb-0">0 Orders</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between border-bottom p-2">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm avatar-rounded bg-secondary me-2"><i class="icon-wine"></i></span>
                                    <h6 class="fs-14 fw-medium mb-0">Reservation</h6>
                                </div>
                                <p class="fw-medium mb-0">0 Orders</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between p-2 pb-0">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-sm avatar-rounded bg-success me-2"><i class="icon-check-check"></i></span>
                                    <h6 class="fs-14 fw-medium mb-0">Delivery</h6>
                                </div>
                                <p class="fw-medium mb-0">0 Orders</p>
                            </div>
                            @endforelse
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

                <div class="col-lg-6 col-xxl-4 d-flex">
                    <div class="card flex-fill w-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs shadow border text-dark fs-14 me-2"><i class="icon-shopping-cart"></i></div>
                                    <h5 class="mb-0">Active Orders</h5>
                                </div>
                                <a href="{{url('orders')}}" class="btn btn-sm btn-white">Add New</a>
                            </div>
                            @forelse($recent_orders ?? [] as $order)
                            <div class="d-flex align-items-sm-center justify-content-between gap-2 flex-column flex-sm-row mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-rounded bg-light border me-2">
                                        <i class="icon-users-round fs-16 text-dark"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="fs-14 fw-semibold mb-1">{{ $order->customer_name ?: 'Walk in Customer' }}</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $order->order_type ?? 'dine_in')) }}</p>
                                            @if($order->table)
                                            <span class="even-line"></span>
                                            <p class="mb-0">Table No : {{ $order->table->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge badge-soft-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }}">{{ ucfirst($order->status) }}</span>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted small mb-3">No recent orders.</p>
                            @endforelse
                            <a href="{{url('orders')}}" class="btn btn-sm btn-secondary w-100">View All</a>
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

                <div class="col-lg-12 col-xxl-4 d-flex">
                    <div class="card flex-fill w-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs shadow border text-dark fs-14 me-2"><i class="icon-chart-column-stacked"></i></div>
                                    <h5 class="mb-0">Sales Performance</h5>
                                </div>
                                <a href="{{url('sales-report')}}" class="btn btn-sm btn-white">View All</a>
                            </div>
                            <div id="sales-chart" class="mb-xl-4 mb-3"></div>
                            <div class="d-flex align-items-center justify-content-between border rounded position-relative p-2 position-relative p-2 z-1 overflow-hidden mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md avatar-rounded bg-indigo me-2">
                                        <i class="icon-shopping-bag fs-16"></i>
                                    </span>
                                    <div>
                                        <p class="mb-1">Total Orders</p>
                                        <h6 class="mb-0">{{ number_format($total_orders ?? 0) }}</h6>
                                    </div>
                                </div>
                                @if(isset($chart_sales_percent) && $chart_sales_percent > 0)
                                <span class="badge bg-success text-white rounded-pill">+{{ $chart_sales_percent }}%</span>
                                @endif
                                <img src="{{URL::asset('build/img/bg/sale-bg.png')}}" alt="bg" class="img-fluid z-n1 position-absolute start-0 top-0 custom-line-img">
                            </div>
                            <div class="d-flex align-items-center justify-content-between border rounded position-relative p-2 z-1 overflow-hidden mb-0">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-md avatar-rounded bg-success me-2">
                                        <i class="icon-shield-check fs-16"></i>
                                    </span>
                                    <div>
                                        <p class="mb-1">Total Sales</p>
                                        <h6 class="mb-0">{{ $currency_symbol }}{{ number_format($total_sales ?? 0, 2) }}</h6>
                                    </div>
                                </div>
                                @if(isset($chart_sales_percent) && $chart_sales_percent > 0)
                                <span class="badge bg-success text-white rounded-pill">+{{ $chart_sales_percent }}%</span>
                                @endif
                                <img src="{{URL::asset('build/img/bg/sale-bg.png')}}" alt="bg" class="img-fluid z-n1 position-absolute start-0 top-0 custom-line-img">
                            </div>
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

            </div>
            <!-- end row -->

                <!-- start row -->
            <div class="row">

                <div class="col-lg-12 col-xxl-8 d-flex">
                    <div class="card flex-fill w-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs shadow border text-dark fs-14 me-2"><i class="icon-book-text"></i></div>
                                    <h5 class="mb-0">Trending Menus</h5>
                                </div>
                                <div class="dropdown">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-white dropdown-toggle" data-bs-toggle="dropdown"  aria-haspopup="false" aria-expanded="false">
                                        All Items
                                    </a>
                                    <ul class="dropdown-menu p-3">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">All Items</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Sea Food</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Pizza</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">Salads</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="row g-3">
                                @forelse($top_selling_items ?? [] as $item)
                                <div class="col-md-4 col-sm-6">
                                    <div class="border p-3 rounded">
                                        <div class="text-center mb-3 bg-light rounded py-4">
                                            <i class="icon-layout-list fs-48 text-muted"></i>
                                        </div>
                                        <div>
                                            <h6 class="fs-14 fw-semibold text-truncate mb-1">{{ $item->item_name }}</h6>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="mb-0">Orders : {{ $item->total_qty }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <p class="text-muted small text-center py-4 mb-0">No trending items yet. Orders will appear here.</p>
                                </div>
                                @endforelse
                            </div>
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

                <div class="col-lg-12 col-xxl-4 d-flex">
                    <div class="card flex-fill w-100">
                        <div class="card-body d-flex flex-column pb-0">
                            <div>
                                <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xs shadow border text-dark fs-14 me-2"><i class="icon-users-round"></i></div>
                                        <h5 class="mb-0">User Statistics</h5>
                                    </div>
                                    <div class="dropdown">
                                        <a href="javascript:void(0);" class="btn btn-sm btn-white dropdown-toggle" data-bs-toggle="dropdown"  aria-haspopup="false" aria-expanded="false">
                                            Weekly
                                        </a>
                                        <ul class="dropdown-menu p-3">
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Weekly</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Monthly</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Yearly</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between border-bottom mb-4 pb-4 flex-sm-row flex-wrap gap-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-xxl avatar-rounded flex-shrink-0 border me-2">
                                            <i class="icon-box fs-24 text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="fs-13 text-dark mb-1">Total Orders</p>
                                            <h6 class="mb-0">{{ number_format($total_orders ?? 0) }}</h6>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="fs-13 text-dark mb-1">Total Sales</p>
                                        <h6 class="mb-0">{{ $currency_symbol }}{{ number_format($total_sales ?? 0, 2) }}</h6>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-1">Average Order Value</p>
                                        <h6 class="mb-0">{{ $currency_symbol }}{{ number_format($average_order_value ?? 0, 2) }}</h6>
                                    </div>
                                </div>
                            </div>
                            <div id="statistic-chart" class="mt-auto"></div>
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

            </div>
            <!-- end row -->

                <!-- start row -->
            <div class="row">

                <div class="col-xxl-4 col-lg-12 d-flex">
                    <div class="card flex-fill w-100">
                        <div class="card-body pb-1">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs shadow border text-dark fs-14 me-2"><i class="icon-file-clock"></i></div>
                                    <h5 class="mb-0">Reservations</h5>
                                </div>
                                <a href="{{ url('reservations') }}" class="btn btn-sm btn-white">View All</a>
                            </div>
                            @forelse($reservations ?? [] as $res)
                            <div class="d-flex align-items-sm-center flex-column flex-sm-row gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2 flex-fill">
                                    <div class="bg-dark reservation-date rounded p-2 text-center flex-shrink-0">
                                        <p class="text-white fw-semibold mb-0 position-relative">{{ $res->reservation_date ? (\Carbon\Carbon::parse($res->reservation_date)->format('M d')) : '–' }} <span class="fs-13 fw-normal d-block mt-1">{{ $res->reservation_time ?? '–' }}</span></p>
                                    </div>
                                    <div>
                                        <h6 class="mb-2 fw-semibold">{{ $res->customer_name ?? 'Guest' }}</h6>
                                        <div class="d-flex align-items-center flex-wrap gap-2">
                                            <p class="d-flex align-items-center mb-0"><i class="icon-clock me-1 text-dark me-1"></i>{{ $res->reservation_time ?? '–' }}</p>
                                            <span class="even-line"></span>
                                            <p class="d-flex align-items-center mb-0"><i class="icon-sofa me-1 text-dark me-1"></i>{{ $res->table?->name ?? '–' }}</p>
                                            <span class="even-line"></span>
                                            <p class="d-flex align-items-center mb-0"><i class="icon-users-round me-1 text-dark me-1"></i>{{ $res->guests ?? '–' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <span class="badge badge-soft-success">Booked</span>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted small mb-0 py-3">No reservations yet. <a href="{{ url('reservations') }}">View all</a></p>
                            @endforelse
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

                <div class="col-xxl-4 col-lg-6 d-flex">
                    <div class="card flex-fill w-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs shadow border text-dark fs-14 me-2"><i class="icon-concierge-bell"></i></div>
                                    <h5 class="mb-0">Tables Available</h5>
                                </div>
                                <a href="{{url('table')}}" class="btn btn-sm btn-white">View All</a>
                            </div>
                            <div class="row g-3">
                                @forelse($tables ?? [] as $t)
                                @php $isOccupied = in_array($t->id, $occupied_table_ids ?? []); @endphp
                                <div class="col-sm-6 d-flex">
                                    <div class="border p-3 rounded w-100 d-flex align-items-center justify-content-center {{ $isOccupied ? 'border-warning bg-light' : '' }}">
                                        <div class="position-relative text-center">
                                            <img src="{{URL::asset('build/img/tables/tables-17.svg')}}" alt="reservation" class="img-fluid custom-line-img">
                                            <div class="position-absolute top-50 start-50 w-100 translate-middle text-center">
                                                <h6 class="fs-12 fw-semibold mb-1">{{ $t->name }}</h6>
                                                <p class="fs-12 mb-0">Guests : {{ $t->capacity ?? '–' }}</p>
                                                @if($isOccupied)
                                                    <span class="badge badge-soft-warning mt-1">Occupied</span>
                                                @else
                                                    <span class="badge badge-soft-success mt-1">Available</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12">
                                    <p class="text-muted small text-center py-3 mb-0">No tables yet. <a href="{{ url('table') }}">Add tables</a>.</p>
                                </div>
                                @endforelse
                            </div>
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

                <div class="col-xxl-4 col-lg-6 d-flex">
                    <div class="card flex-fill w-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs shadow border text-dark fs-14 me-2"><i class="icon-bell"></i></div>
                                    <h5 class="mb-0">Notifications</h5>
                                </div>
                            </div>
                            <h6 class="mb-3">Recent</h6>
                            <div class="log-wrap">
                                @forelse($recent_orders ?? [] as $order)
                                <div class="d-flex gap-2 flex-sm-row flex-column mb-3">
                                    <span class="avatar avatar-rounded flex-shrink-0 position-relative z-2 badge-soft-primary border border-primary">
                                        <i class="icon-shopping-cart fs-16"></i>
                                    </span>
                                    <div class="w-100 overflow-hidden">
                                        <p class="text-truncate mb-1">Order <span class="text-dark fw-medium">#{{ $order->order_number }}</span> ({{ $order->items->count() }} items) @if($order->table)– {{ $order->table->name }}@endif</p>
                                        <p class="mb-0 fs-13 d-inline-flex align-items-center"><i class="icon-clock me-1"></i>{{ $order->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                @empty
                                <p class="text-muted small mb-0">No recent activity.</p>
                                @endforelse
                            </div>
                        </div> <!-- end card body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->

            </div>
            <!-- end row -->

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

    <script>
        window.dashboardChartData = @json($dashboard_chart_data ?? []);
    </script>
@endsection
