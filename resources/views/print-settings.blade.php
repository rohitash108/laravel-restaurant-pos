<?php $page = 'print-settings'; ?>
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
                    <h3 class="mb-0">Print Settings <a href="{{ route('print-settings') }}" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
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
                <div class="card">

                    <div class="card-body">
                        <form action="{{ route('print-settings.update') }}" method="POST">
                            @csrf

                            <h5 class="mb-3">Print Settings</h5>

                            <!-- start row -->
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-3 d-flex align-items-center justify-content-between ps-0">
                                            <label class="form-check-label" for="enable_print">Enable Print</label>
                                            <input class="form-check-input" type="checkbox" role="switch" id="enable_print" name="enable_print" {{ ($settings['enable_print'] ?? '1') === '1' ? 'checked' : '' }}>
                                        </div>
                                        <div class="form-check form-switch mb-3 d-flex align-items-center justify-content-between ps-0">
                                            <label class="form-check-label" for="show_store_details">Show Store Details</label>
                                            <input class="form-check-input" type="checkbox" role="switch" id="show_store_details" name="show_store_details" {{ ($settings['show_store_details'] ?? '1') === '1' ? 'checked' : '' }}>
                                        </div>
                                        <div class="form-check form-switch mb-3 d-flex align-items-center justify-content-between ps-0">
                                            <label class="form-check-label" for="show_customer_details">Show Customer Details</label>
                                            <input class="form-check-input" type="checkbox" role="switch" id="show_customer_details" name="show_customer_details" {{ ($settings['show_customer_details'] ?? '1') === '1' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Format (Page Sizes)<span class="text-danger ms-1">*</span></label>
                                        <select class="select" name="format">
                                            <option value="">Select</option>
                                            @foreach(['A1', 'A2', 'A3', 'A4', 'A5'] as $size)
                                                <option value="{{ $size }}" {{ ($settings['format'] ?? '') === $size ? 'selected' : '' }}>{{ $size }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Header</label>
                                        <textarea class="form-control" name="header">{{ $settings['header'] ?? '' }}</textarea>
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Footer</label>
                                        <textarea class="form-control" name="footer">{{ $settings['footer'] ?? '' }}</textarea>
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-12">
                                    <div class="mb-0">
                                        <div class="form-check form-switch d-flex align-items-center justify-content-between mb-3 ps-0">
                                            <label class="form-check-label" for="show_notes">Show Notes</label>
                                            <input class="form-check-input" type="checkbox" role="switch" id="show_notes" name="show_notes" {{ ($settings['show_notes'] ?? '1') === '1' ? 'checked' : '' }}>
                                        </div>
                                        <div class="form-check form-switch d-flex align-items-center justify-content-between mb-0 ps-0">
                                            <label class="form-check-label" for="print_tokens">Print Tokens</label>
                                            <input class="form-check-input" type="checkbox" role="switch" id="print_tokens" name="print_tokens" {{ ($settings['print_tokens'] ?? '1') === '1' ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div> <!-- end col -->

                            </div>
                            <!-- end row -->

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
