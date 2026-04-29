@php $page = 'admin-dashboard'; @endphp
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Super Admin Dashboard
    ========================= -->

    <div class="page-wrapper">
        <div class="content">

            {{-- Page header --}}
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1">
                    <h3 class="mb-1">Super Admin Dashboard</h3>
                    <p class="text-muted mb-0">
                        {{ ($is_limited_admin ?? false) ? 'Overview of your added products, categories, addons and users.' : 'Overview of all restaurants and platform activity.' }}
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-white btn-icon rounded-circle" title="Refresh"><i class="icon-refresh-ccw"></i></a>
                    @if(!($is_limited_admin ?? false))
                    <a href="{{ route('admin.restaurants.create') }}" class="btn btn-secondary d-inline-flex align-items-center"><i class="icon-circle-plus me-1"></i>Add Restaurant</a>
                    @endif
                </div>
            </div>

            {{-- KPI cards --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted text-uppercase fs-12 fw-medium mb-1">{{ ($is_limited_admin ?? false) ? 'Total Categories' : 'Total Restaurants' }}</p>
                                    <h3 class="mb-0 fw-bold">{{ number_format(($is_limited_admin ?? false) ? ($total_categories ?? 0) : ($total_restaurants ?? 0)) }}</h3>
                                    @if(!($is_limited_admin ?? false))
                                        <small class="text-success">{{ $active_restaurants ?? 0 }} active</small>
                                    @endif
                                </div>
                                <div class="avatar avatar-lg avatar-rounded bg-secondary text-white">
                                    <i class="{{ ($is_limited_admin ?? false) ? 'icon-tag' : 'icon-warehouse' }} fs-28"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted text-uppercase fs-12 fw-medium mb-1">{{ ($is_limited_admin ?? false) ? 'Total Products' : 'Total Orders' }}</p>
                                    <h3 class="mb-0 fw-bold">{{ number_format(($is_limited_admin ?? false) ? ($total_products ?? 0) : ($total_orders ?? 0)) }}</h3>
                                </div>
                                <div class="avatar avatar-lg avatar-rounded bg-purple text-white">
                                    <i class="{{ ($is_limited_admin ?? false) ? 'icon-layout-list' : 'icon-box' }} fs-28"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted text-uppercase fs-12 fw-medium mb-1">{{ ($is_limited_admin ?? false) ? 'Total Addons' : 'Total Sales' }}</p>
                                    <h3 class="mb-0 fw-bold">
                                        @if($is_limited_admin ?? false)
                                            {{ number_format($total_addons ?? 0) }}
                                        @else
                                            {{ $currency_symbol }}{{ number_format($total_sales ?? 0, 2) }}
                                        @endif
                                    </h3>
                                </div>
                                <div class="avatar avatar-lg avatar-rounded bg-success text-white">
                                    <i class="{{ ($is_limited_admin ?? false) ? 'icon-text-select' : 'icon-badge-dollar-sign' }} fs-28"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100 overflow-hidden">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="text-muted text-uppercase fs-12 fw-medium mb-1">{{ ($is_limited_admin ?? false) ? 'My Users' : 'Sales growth' }}</p>
                                    @if($is_limited_admin ?? false)
                                        <h3 class="mb-0 fw-bold">{{ number_format($total_my_users ?? 0) }}</h3>
                                        <small class="text-muted">Created by you</small>
                                    @else
                                        <h3 class="mb-0 fw-bold {{ ($sales_growth_percent ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ ($sales_growth_percent ?? 0) >= 0 ? '+' : '' }}{{ $sales_growth_percent ?? 0 }}%
                                        </h3>
                                        <small class="text-muted">vs last month</small>
                                    @endif
                                </div>
                                <div class="avatar avatar-lg avatar-rounded bg-orange text-white">
                                    <i class="{{ ($is_limited_admin ?? false) ? 'icon-users' : 'icon-trending-up' }} fs-28"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- Orders (Last 7 Days) chart --}}
                <div class="col-xxl-8 col-lg-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-0 bg-transparent d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h5 class="mb-0 card-title">{{ ($is_limited_admin ?? false) ? 'Your Module Totals' : 'Orders (Last 7 Days)' }}</h5>
                            @if(!($is_limited_admin ?? false))
                                <a href="{{ route('admin.restaurants.index') }}" class="btn btn-sm btn-secondary">View restaurants</a>
                            @endif
                        </div>
                        <div class="card-body pt-0">
                            <div id="admin-statistic-chart" style="min-height: 260px;"></div>
                        </div>
                    </div>
                </div>
                {{-- Quick actions & Recent Restaurants --}}
                <div class="col-xxl-4 col-lg-5">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Quick actions</h5>
                            <div class="d-grid gap-2">
                                @if(!($is_limited_admin ?? false))
                                <a href="{{ route('admin.restaurants.create') }}" class="btn btn-secondary d-inline-flex align-items-center justify-content-center">
                                    <i class="icon-circle-plus me-2"></i>Add new restaurant
                                </a>
                                <a href="{{ route('admin.restaurants.index') }}" class="btn btn-soft-secondary d-inline-flex align-items-center justify-content-center">
                                    <i class="icon-warehouse me-2"></i>Manage restaurants
                                </a>
                                @endif
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-soft-secondary d-inline-flex align-items-center justify-content-center">
                                    <i class="icon-tag me-2"></i>Manage categories
                                </a>
                                <a href="{{ route('admin.items.index') }}" class="btn btn-soft-secondary d-inline-flex align-items-center justify-content-center">
                                    <i class="icon-layout-list me-2"></i>Manage items
                                </a>
                                <a href="{{ route('admin.addons.index') }}" class="btn btn-soft-secondary d-inline-flex align-items-center justify-content-center">
                                    <i class="icon-text-select me-2"></i>Manage addons
                                </a>
                                @if(auth()->user()->isOwner())
                                <a href="{{ route('admin.subscriptions') }}" class="btn btn-soft-primary d-inline-flex align-items-center justify-content-center">
                                    <i class="icon-credit-card me-2"></i>Manage subscriptions
                                    @if(($expiring_soon ?? 0) > 0)
                                        <span class="badge bg-warning ms-2">{{ $expiring_soon }} expiring</span>
                                    @endif
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm">
                        <div class="card-header border-0 bg-transparent d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 card-title">{{ ($is_limited_admin ?? false) ? 'Recent Restaurants' : 'Recent Restaurants' }}</h5>
                            @if(!($is_limited_admin ?? false))
                                <a href="{{ route('admin.restaurants.index') }}" class="btn btn-sm btn-link p-0">View all</a>
                            @endif
                        </div>
                        <div class="card-body pt-0">
                            @if($is_limited_admin ?? false)
                                <p class="text-muted small mb-0 py-2">Restaurant overview is available only for owner super admin.</p>
                            @else
                            @forelse($recent_restaurants ?? [] as $r)
                                <a href="{{ route('admin.restaurants.show', $r) }}" class="d-flex align-items-center gap-3 py-3 {{ $loop->last ? '' : 'border-bottom border-light' }} text-body text-decoration-none">
                                    <div class="flex-shrink-0 rounded overflow-hidden bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        @if($r->logo)
                                            <img src="{{ asset('storage/' . $r->logo) }}" alt="" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                        @else
                                            <i class="icon-warehouse text-muted"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <span class="fw-medium d-block text-truncate">{{ $r->name }}</span>
                                        <small class="text-muted">{{ $r->tables_count }} tables · {{ $r->orders_count }} orders</small>
                                    </div>
                                    <i class="icon-chevron-right text-muted flex-shrink-0"></i>
                                </a>
                            @empty
                                <p class="text-muted small mb-0 py-2">No restaurants yet. <a href="{{ route('admin.restaurants.create') }}">Add one</a>.</p>
                            @endforelse
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('#admin-statistic-chart') && typeof ApexCharts !== 'undefined') {
        var chartData = @json($admin_chart_data);
        var categories = chartData.categories && chartData.categories.length ? chartData.categories : ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        var data = chartData.data && chartData.data.length ? chartData.data : [0,0,0,0,0,0,0];
        var seriesName = @json(($is_limited_admin ?? false) ? 'Count' : 'Orders');
        new ApexCharts(document.querySelector('#admin-statistic-chart'), {
            series: [{ name: seriesName, data: data }],
            chart: { height: 260, type: 'area', toolbar: { show: false } },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } },
            xaxis: { categories: categories },
            colors: ['#4da5ff'],
            grid: { borderColor: 'transparent', padding: { top: 0, right: 0, bottom: 0, left: 0 } }
        }).render();
    }
});
</script>
@endpush
