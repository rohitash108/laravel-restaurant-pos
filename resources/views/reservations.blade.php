<?php $page = 'reservations'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Page Header -->
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1">
                    <h3 class="mb-0">Reservations <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="input-group input-group-flat w-auto">
                        <input type="text" class="form-control" placeholder="Search">
                        <span class="input-group-text">
                            <i class="icon-search text-dark"></i>
                        </span>
                    </div>
                    <a href="#" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_reservation"><i class="icon-circle-plus me-1"></i>Add New</a>
                </div>
            </div>
            <!-- End Page Header -->

            @if(($reservations ?? collect())->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="icon-calendar-fold fs-48 text-muted mb-3"></i>
                    <h5 class="mb-2">No reservations yet</h5>
                    <p class="text-muted mb-0">Reservations will appear here when the feature is enabled.</p>
                </div>
            </div>
            @else
            <!-- start row -->
            <div class="row">
                @foreach($reservations as $r)
                <div class="col-xxl-4 col-xl-6 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                                <div class="bg-dark reservation-date rounded p-2 text-center flex-shrink-0">
                                    <p class="text-white fw-semibold mb-0">{{ $r->reservation_date?->format('d M') ?? '–' }} <span class="fs-13 fw-normal d-block mt-1">{{ $r->reservation_time ?? '' }}</span></p>
                                </div>
                                <div>
                                    <h6 class="mb-2 fw-semibold">{{ $r->customer_name ?? 'Guest' }}</h6>
                                    <p class="mb-0 small"><i class="icon-users-round me-1"></i> Guests : {{ $r->guests ?? '–' }}</p>
                                </div>
                            </div>
                            @php $statusClass = match($r->status ?? 'booked') { 'cancelled' => 'badge-soft-danger', 'completed' => 'badge-soft-success', 'no-show' => 'badge-soft-warning', default => 'badge-soft-success' }; @endphp
                            <p class="mb-0"><span class="badge {{ $statusClass }}">{{ ucfirst($r->status ?? 'Booked') }}</span></p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
