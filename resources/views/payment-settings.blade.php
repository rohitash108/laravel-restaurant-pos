<?php $page = 'payment-settings'; ?>
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
                    <h3 class="mb-0">Payment Types <a href="{{ route('payment-settings') }}" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
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
                        <form action="{{ route('payment-settings.update') }}" method="POST">
                            @csrf

                            <!-- start row -->
                            <div class="row">

                                @php
                                    $methods = [
                                        ['key' => 'cash', 'label' => 'Cash', 'icon' => 'dollar-sign.svg'],
                                        ['key' => 'card', 'label' => 'Card', 'icon' => 'credit-card.svg'],
                                        ['key' => 'wallet', 'label' => 'Wallet', 'icon' => 'wallet.svg'],
                                        ['key' => 'paypal', 'label' => 'Paypal', 'icon' => 'russian-ruble.svg'],
                                        ['key' => 'qr_reader', 'label' => 'QR Reader', 'icon' => 'qr-code.svg'],
                                        ['key' => 'card_reader', 'label' => 'Card Reader', 'icon' => 'receipt-text.svg'],
                                        ['key' => 'bank', 'label' => 'Bank', 'icon' => 'landmark.svg'],
                                    ];
                                @endphp

                                @foreach($methods as $i => $method)
                                <div class="col-md-6 col-lg-4">
                                    <div class="card flex-fill{{ $loop->last ? ' mb-0' : '' }}">
                                        <div class="card-body">
                                            <div class="w-100 d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-md bg-light p-2 me-2">
                                                        <img src="{{URL::asset('build/img/icons/' . $method['icon'])}}" alt="Img" class="custom-line-img-two">
                                                    </div>
                                                    <p class="mb-0 text-dark">{{ $method['label'] }}</p>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="form-check form-switch mb-0">
                                                        <label class="form-check-label" for="payment_{{ $method['key'] }}"></label>
                                                        <input class="form-check-input" type="checkbox" role="switch" id="payment_{{ $method['key'] }}" name="{{ $method['key'] }}" {{ ($settings[$method['key']] ?? '1') === '1' ? 'checked' : '' }}>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end col -->
                                @endforeach

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
