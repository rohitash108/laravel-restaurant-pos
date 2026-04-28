<?php $page = 'register'; ?>
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
                            <form action="{{ route('register.submit') }}" method="POST">
                                @csrf

                                <div class="d-flex flex-column justify-content-between">
                                    <div class="mb-5">
                                        <a href="{{ route('dashboard') }}"><span class="app-brand-logo app-brand-logo--lg"><img src="{{ asset('build/img/global-tea-cafe-logo.png') }}" class="img-fluid" alt="Global Tea Cafe" style="max-height:80px;object-fit:contain;"></span></a>
                                    </div>

                                    <div>
                                        <div class="mb-4">
                                            <h3 class="mb-2">Sign Up</h3>
                                            <p class="mb-0">And lets get started with your free trial</p>
                                        </div>

                                        @if ($errors->any())
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="form-label">Full Name<span class="text-danger"> *</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Email<span class="text-danger"> *</span></label>
                                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Password<span class="text-danger"> *</span></label>
                                            <div class="input-group input-group-flat pass-group">
                                                <input type="password" name="password" class="form-control pass-input" required>
                                                <span class="input-group-text toggle-password ">
                                                    <i class="icon-eye-off"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="form-label">Confirm Password<span class="text-danger"> *</span></label>
                                            <div class="input-group input-group-flat pass-group">
                                                <input type="password" name="password_confirmation" class="form-control pass-input" required>
                                                <span class="input-group-text toggle-password ">
                                                    <i class="icon-eye-off"></i>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-check form-check-md mb-4">
                                            <input class="form-check-input" id="agree_terms" name="agree" type="checkbox" required>
                                            <label for="agree_terms" class="form-check-label text-dark mt-0">Agree to <a href="#">Terms</a> & <a href="#">Privacy Policy</a></label>
                                        </div>

                                        <div class="mb-4">
                                            <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                                        </div>

                                        <div class="login-or position-relative mb-4 text-center">
                                            <span class="position-relative bg-white px-2 z-2">or continue with</span>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-center flex-wrap">
                                            <div class="text-center me-2 flex-fill">
                                                <a href="#" class="btn btn-white d-flex align-items-center justify-content-center shadow">
                                                    <img class="img-fluid me-2" src="{{URL::asset('build/img/icons/google.svg')}}" alt="google">Google
                                                </a>
                                            </div>
                                            <div class="text-center me-2 flex-fill">
                                                <a href="#" class="btn btn-white d-flex align-items-center justify-content-center shadow">
                                                    <img class="img-fluid me-2" src="{{URL::asset('build/img/icons/fb.svg')}}" alt="facebook">Facebook
                                                </a>
                                            </div>
                                        </div>

                                        <div class="text-center mt-4">
                                            <p class="fw-normal mb-0">Already have an account?
                                                <a href="{{url('login')}}" class="link-primary"> Sign In</a>
                                            </p>
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

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
