<?php $page = 'email-verification'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="container-fuild">

        <!-- Start Content -->
        <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">

            <!-- start row -->
            <div class="row g-2">

                <div class="col-lg-6 col-md-12 col-sm-12 p-3">

                    <!-- start row -->
                    <div class="row justify-content-center align-items-center overflow-auto flex-wrap auth-vh vh-100">

                        <div class="col-xl-8 col-lg-10 col-md-8 col-sm-10 mx-auto">
                            <form action="{{url('otp')}}">

                                <div class="d-flex flex-column justify-content-between p-3">
                                    <div class="mb-5">
                                        <a href="{{ route('dashboard') }}"><img src="{{URL::asset('build/img/logo.svg')}}" class="img-fluid" alt="Logo"></a>
                                    </div>

                                    <div>
                                        <div class="mb-4">
                                            <h3 class="mb-2">Check your Email</h3>
                                            <p class="mb-0">We have sent a password recovery instruction to your email</p>
                                        </div>

                                        <div class="mb-4">
                                            <button type="submit" class="btn btn-primary w-100">Resend Email</button>
                                        </div>

                                        <div class="text-center mt-4">
                                            <p class="fw-normal mb-0">Back to <a href="{{url('login')}}" class="link-primary"> Sign In</a></p>
                                        </div>

                                    </div>
                                </div>

                            </form>
                        </div> <!-- end col -->

                    </div>
                    <!-- end row -->

                </div> <!-- end col -->

                <div class="col-lg-6">
                    <div class="position-relative d-lg-flex align-items-center justify-content-center d-none flex-wrap vh-100 p-4 ps-0">
                        <div class="w-100 rounded-3 position-relative h-100 bg-primary z-1 overflow-hidden">
                            <img src="{{URL::asset('build/img/authentication/authentication-bg-01.png')}}" class="img-fluid position-absolute bottom-0 end-0 z-n1 auth-bg-01" alt="bg">
                            <img src="{{URL::asset('build/img/authentication/authentication-bg-02.png')}}" class="img-fluid position-absolute top-0 end-0 z-n1 auth-bg-02" alt="bg">
                            <div class="px-4 rounded-3 h-100 d-flex flex-column align-items-center auth-wrap">
                                <div class="text-center z-2">
                                    <h1 class="text-white mb-2">Complete Control of Your Cafe & Restaurant with Ease</h1>
                                    <p class="text-white mb-0">From billing to inventory access everything you need in a single powerful dashboard, Analyze sales, track your best-selling dishes.</p>
                                </div>
                                <div class="text-center auth-img position-absolute bottom-0">
                                    <img src="{{URL::asset('build/img/authentication/login.png')}}" class="img-fluid position-relative z-1" alt="user">
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end col -->

            </div>
            <!-- end row -->

            <!-- Alert -->
            <div class="alert alert-success border-0 border-success border-bottom alert-dismissible pe-5 d-none" role="alert">
                <p class="fw-medium mb-0 d-inline-flex align-items-center">
                <span class="btn btn-icon btn-xs rounded-circle bg-success d-flex align-items-center justify-content-center pe-none me-2 "><i class="icon-check fs-16 text-white"></i></span>Verification code sent</p>
                <button type="button" class="btn-close btn-custom-close top-50 translate-middle-y link-success" data-bs-dismiss="alert" aria-label="Close"><i class="icon-x"></i></button>
            </div>

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
