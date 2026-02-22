<?php $page = 'table'; ?>
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
                    <h3 class="mb-0">Tables
                        @if(isset($restaurant))
                            <span class="fs-14 fw-normal text-muted">– {{ $restaurant->name }}</span>
                        @endif
                    </h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    @if(isset($restaurant))
                        <a href="#" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_table"><i class="icon-circle-plus me-1"></i>Add New</a>
                    @endif
                </div>
            </div>
            <!-- End Page Header -->

            @if (session('success'))
                <div class="alert alert-success alert-dismissible border-0 mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible border-0 mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible border-0 mb-4" role="alert">
                    {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Row start -->
            <div class="row">
                @forelse($tables ?? [] as $t)
                @php $hasActiveOrder = in_array($t->id, $occupiedTableIds ?? []); @endphp
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card {{ $hasActiveOrder ? 'border-warning' : '' }}">
                        <div class="card-body">
                            <div class="bg-light rounded mb-3 tables-img text-center p-2">
                                <a href="{{ $t->order_by_qr_url }}" target="_blank" rel="noopener" title="Scan or open menu">
                                    <img src="{{ route('order.by-qr.qr-image', ['restaurant' => $t->restaurant->slug, 'table' => $t->slug ?? $t->id]) }}" alt="QR – Table {{ $t->name }}" class="img-fluid" style="max-height: 140px; width: auto;">
                                </a>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <h6 class="fs-14 fw-semibold">
                                        <a href="{{ $t->order_by_qr_url }}" target="_blank" class="me-1">{{ $t->name }}</a>
                                        @if($hasActiveOrder)
                                            <span class="badge badge-soft-warning fw-medium fs-10 mb-0">Occupied</span>
                                        @elseif($t->status === 'reserved')
                                            <span class="badge badge-soft-danger fw-medium fs-10 mb-0">Reserved</span>
                                        @else
                                            <span class="badge badge-soft-success fw-medium fs-10 mb-0">Available</span>
                                        @endif
                                    </h6>
                                    <div class="d-flex align-items-center">
                                        <p class="mb-0"><span class="border-end pe-2">Floor : {{ $t->floor ?: '–' }}</span><span class="ms-2">Capacity : {{ $t->capacity }}</span></p>
                                    </div>
                                    <p class="mb-0 mt-1 small">
                                        <a href="{{ $t->order_by_qr_url }}" target="_blank" class="link-primary"><i class="icon-qrcode me-1"></i>Order by QR</a>
                                    </p>
                                </div>
                                <div>
                                    <a href="#" class="table-menu" data-bs-toggle="dropdown">
                                        <i class="icon-ellipsis-vertical"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-split">
                                        <li><a href="#" class="dropdown-item edit-table-btn" data-bs-toggle="modal" data-bs-target="#edit_table" data-id="{{ $t->id }}" data-name="{{ $t->name }}" data-floor="{{ $t->floor }}" data-capacity="{{ $t->capacity }}" data-status="{{ $t->status }}"><i class="icon-pencil-line me-2"></i>Edit</a></li>
                                        <li>
                                            <form action="{{ route('table.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this table?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger"><i class="icon-trash-2 me-2"></i>Delete</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <p class="text-muted mb-0">No tables yet. Add one using the button above.</p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>
            <!-- Row end -->

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
