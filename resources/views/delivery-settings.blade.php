<?php $page = 'delivery-settings'; ?>
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
                    <h3 class="mb-0">Delivery <a href="{{ route('delivery-settings') }}" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
            </div>
            <!-- End Page Header -->

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div>
                <!-- card start -->
                <div class="card mb-0">
                    <div class="card-body">
                        <form action="{{ route('delivery-settings.update') }}" method="POST">
                            @csrf

                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3 d-flex align-items-center justify-content-between ps-0">
                                        <label class="form-check-label fs-16 fw-bold text-dark" for="free_delivery_enabled">Free Delivery</label>
                                        <input class="form-check-input" type="checkbox" role="switch" id="free_delivery_enabled" name="free_delivery_enabled" {{ ($settings['free_delivery_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">Free Delivery Over ($)<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="free_delivery_over" value="{{ $settings['free_delivery_over'] ?? '' }}">
                                    </div>
                                </div><!-- card-body -->
                            </div><!-- card end -->

                            <div class="card">
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3 d-flex align-items-center justify-content-between ps-0">
                                        <label class="form-check-label fs-16 fw-bold text-dark" for="fixed_delivery_enabled">Fixed Delivery Charges</label>
                                        <input class="form-check-input" type="checkbox" role="switch" id="fixed_delivery_enabled" name="fixed_delivery_enabled" {{ ($settings['fixed_delivery_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">Fixed Delivery Amount ($)<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="fixed_delivery_amount" value="{{ $settings['fixed_delivery_amount'] ?? '' }}">
                                    </div>
                                </div><!-- card-body -->
                            </div><!-- card end -->

                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="form-check form-switch mb-3 d-flex align-items-center justify-content-between ps-0">
                                        <label class="form-check-label fs-16 fw-bold text-dark" for="km_delivery_enabled">Kilometer Based Delivery Charges</label>
                                        <input class="form-check-input" type="checkbox" role="switch" id="km_delivery_enabled" name="km_delivery_enabled" {{ ($settings['km_delivery_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Per KM Delivery Charge ($)<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="per_km_charge" value="{{ $settings['per_km_charge'] ?? '' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Minimum Delivery Over ($)<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="minimum_delivery_over" value="{{ $settings['minimum_delivery_over'] ?? '' }}">
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">Minimum Distance for Free Delivery (KM)<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="min_distance_free_delivery" value="{{ $settings['min_distance_free_delivery'] ?? '' }}">
                                    </div>
                                </div><!-- card-body -->
                            </div><!-- card end -->

                            <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-2 border-top mt-4 pt-4">
                                <button type="button" class="btn btn-light me-2" onclick="window.location.reload()">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>

                        </form>
                    </div> <!-- end card body -->

                </div>
                <!-- card start -->
            </div>

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
