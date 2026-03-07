@php $page = 'admin-restaurants'; @endphp
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
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Restaurants</li>
                        </ol>
                    </nav>
                    <h3 class="mb-0">Restaurants</h3>
                    <p class="text-muted mb-0 fs-14">Manage all restaurant locations.</p>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-white"><i class="icon-layout-dashboard me-1"></i>Dashboard</a>
                    <a href="{{ route('admin.restaurants.create') }}" class="btn btn-primary d-inline-flex align-items-center"><i class="icon-circle-plus me-1"></i>Add Restaurant</a>
                </div>
            </div>
            <!-- End Page Header -->

            @if (session('success'))
                <div class="alert alert-success alert-dismissible border-0 mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Summary when there are restaurants --}}
            @if($restaurants->total() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body py-3">
                            <span class="text-muted">Total restaurants:</span>
                            <strong class="fs-5 ms-1">{{ $restaurants->total() }}</strong>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Restaurants grid -->
            <div class="row">
                @forelse($restaurants as $restaurant)
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="bg-light rounded mb-3 overflow-hidden d-flex align-items-center justify-content-center" style="height: 120px;">
                                    @if($restaurant->logo)
                                        <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="{{ $restaurant->name }}" class="img-fluid w-100 h-100" style="object-fit: cover;">
                                    @else
                                        <i class="icon-warehouse fs-48 text-muted"></i>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="fs-14 fw-semibold mb-0 text-truncate flex-grow-1 me-2">{{ $restaurant->name }}</h6>
                                    <div class="dropdown flex-shrink-0">
                                        <a href="#" class="btn btn-icon btn-sm btn-white" data-bs-toggle="dropdown" aria-expanded="false"><i class="icon-ellipsis-vertical"></i></a>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('admin.restaurants.show', $restaurant) }}"><i class="icon-eye me-2"></i>View</a></li>
                                            <li><a class="dropdown-item" href="{{ route('admin.restaurants.edit', $restaurant) }}"><i class="icon-pencil-line me-2"></i>Edit</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.restaurants.destroy', $restaurant) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this restaurant?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger"><i class="icon-trash-2 me-2"></i>Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <p class="fs-13 text-muted mb-2">{{ $restaurant->slug }}</p>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
                                    <span class="badge badge-soft-primary">{{ $restaurant->tables_count }} Tables</span>
                                    @if($restaurant->is_active)
                                        <span class="badge badge-soft-success">Active</span>
                                    @else
                                        <span class="badge badge-soft-secondary">Inactive</span>
                                    @endif
                                    @if($restaurant->activeSubscription)
                                        @php $days = $restaurant->activeSubscription->daysRemaining(); @endphp
                                        @if($days <= 7)
                                            <span class="badge badge-soft-warning" title="Expires {{ $restaurant->activeSubscription->ends_at->format('d M Y') }}">
                                                <i class="icon-clock"></i> {{ $days }}d left
                                            </span>
                                        @else
                                            <span class="badge badge-soft-info" title="{{ $restaurant->activeSubscription->plan->name ?? '' }} — Expires {{ $restaurant->activeSubscription->ends_at->format('d M Y') }}">
                                                <i class="icon-credit-card"></i> {{ $restaurant->activeSubscription->plan->name ?? 'Subscribed' }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge badge-soft-danger">No Subscription</span>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.restaurants.show', $restaurant) }}" class="btn btn-sm btn-soft-primary flex-grow-1">View</a>
                                    <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="btn btn-sm btn-primary">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="icon-warehouse fs-48 text-muted mb-3 d-block"></i>
                                <h5 class="mb-2">No restaurants yet</h5>
                                <p class="text-muted mb-4">Create your first restaurant to get started.</p>
                                <a href="{{ route('admin.restaurants.create') }}" class="btn btn-primary"><i class="icon-circle-plus me-1"></i>Add Restaurant</a>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($restaurants->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $restaurants->links() }}
                </div>
            @endif

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
