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

            <style>
                .table-qr-card {
                    border: none;
                    background: transparent;
                    transition: transform 0.2s ease;
                }
                .table-qr-card:hover {
                    transform: translateY(-4px);
                }
                .table-qr-frame {
                    background: linear-gradient(135deg, #ef4444, #7c3aed);
                    padding: 2.5px;
                    border-radius: 18px;
                }
                .table-qr-inner {
                    background: #ffffff;
                    border-radius: 16px;
                    padding: 16px 14px 12px;
                    min-height: 100%;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }
                .table-qr-title {
                    font-family: 'Georgia', serif;
                    font-style: italic;
                    font-size: 1.05rem;
                    font-weight: 700;
                    margin-bottom: 2px;
                    color: #6b21a8;
                }
                .table-qr-hotel {
                    font-size: 0.82rem;
                    text-transform: uppercase;
                    font-weight: 800;
                    color: #1e293b;
                    letter-spacing: 0.04em;
                }
                .table-qr-table {
                    font-size: 0.8rem;
                    font-weight: 700;
                    color: #dc2626;
                }
                .table-qr-qrbox {
                    background: linear-gradient(135deg, #ef4444, #7c3aed);
                    border-radius: 14px;
                    padding: 3px;
                }
                .table-qr-qr-inner {
                    background: #ffffff;
                    border-radius: 12px;
                    padding: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .table-qr-qr-inner img {
                    display: block;
                    width: 100%;
                    max-width: 160px;
                    height: auto;
                    image-rendering: pixelated;
                    border-radius: 4px;
                }
                .table-qr-footer {
                    font-size: 0.76rem;
                }
                .table-qr-powered {
                    font-size: 0.72rem;
                }
                .table-qr-actions {
                    display: flex;
                    gap: 6px;
                    justify-content: center;
                    flex-wrap: wrap;
                }
                .table-qr-actions .btn {
                    font-size: 0.72rem;
                    padding: 3px 10px;
                }
                @media (max-width: 575.98px) {
                    .table-qr-inner {
                        padding: 12px 10px;
                    }
                }
            </style>

            <!-- Row start -->
            <div class="row">
                @forelse($tables ?? [] as $t)
                @php $hasActiveOrder = in_array($t->id, $occupiedTableIds ?? []); @endphp
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card table-qr-card {{ $hasActiveOrder ? 'border-warning' : '' }}">
                        <div class="card-body">
                            <div class="table-qr-frame">
                                <div class="table-qr-inner">
                                    <div class="text-center mb-2">
                                        <div class="table-qr-title mb-0">OrderByQR</div>
                                        <p class="mb-0 table-qr-hotel">
                                            {{ $restaurant->name ?? ($t->restaurant->name ?? 'Restaurant') }}
                                        </p>
                                        <p class="mb-1 table-qr-table">Table Number - {{ $t->name }}</p>
                                    </div>
                                    <div class="table-qr-qrbox text-center mb-3">
                                        <div class="table-qr-qr-inner">
                                            <a href="{{ $t->order_by_qr_url }}" target="_blank" rel="noopener" title="Scan or open menu">
                                                <img src="{{ route('order.by-qr.qr-image', ['restaurant' => $t->restaurant->slug, 'table' => $t->slug ?? $t->id]) }}" alt="QR – Table {{ $t->name }}">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="mb-2 table-qr-actions">
                                        <a href="{{ route('table.qr-print', $t) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="icon-printer me-1"></i>Print
                                        </a>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between border-top pt-2 table-qr-footer">
                                        <div>
                                            <span class="d-block mb-1">Floor: {{ $t->floor ?: '–' }} | Capacity: {{ $t->capacity }}</span>
                                            <span class="d-block">
                                                @if($hasActiveOrder)
                                                    <span class="badge badge-soft-warning fw-medium fs-10 mb-0">Occupied</span>
                                                @elseif($t->status === 'reserved')
                                                    <span class="badge badge-soft-danger fw-medium fs-10 mb-0">Reserved</span>
                                                @else
                                                    <span class="badge badge-soft-success fw-medium fs-10 mb-0">Available</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <div class="dropdown mb-1">
                                                <a href="#" class="table-menu" data-bs-toggle="dropdown">
                                                    <i class="icon-ellipsis-vertical"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-split">
                                                    <li><a href="#" class="dropdown-item edit-table-btn" data-bs-toggle="modal" data-bs-target="#edit_table" data-id="{{ $t->id }}" data-name="{{ $t->name }}" data-floor="{{ $t->floor }}" data-capacity="{{ $t->capacity }}" data-status="{{ $t->status }}"><i class="icon-pencil-line me-2"></i>Edit</a></li>
                                                    <li><a href="{{ route('table.qr-print', $t) }}" target="_blank" class="dropdown-item"><i class="icon-printer me-2"></i>Print QR Stand</a></li>
                                                    <li>
                                                        <form action="{{ route('table.destroy', $t) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this table?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger"><i class="icon-trash-2 me-2"></i>Delete</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                            <small class="d-block text-muted table-qr-powered">Powered by <span class="fw-semibold">IT Software</span></small>
                                        </div>
                                    </div>
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
