<?php $page = 'login'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="container-fluid">
        <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
            <div class="row g-0">
                <div class="col-lg-6 col-md-12 col-sm-12 p-0 p-md-3">
                    <div class="row justify-content-center align-items-center overflow-auto flex-wrap auth-vh vh-100">
                        <div class="col-xl-8 col-lg-10 col-md-8 col-sm-10 mx-auto px-3 px-md-4">
                            <form action="{{ route('login.submit') }}" method="POST">
                                @csrf
                                <div class="d-flex flex-column justify-content-between">
                                    <div class="mb-5">
                                        <a href="{{ route('dashboard') }}"><img src="{{ asset('build/img/logo.svg') }}" class="img-fluid" alt="Logo"></a>
                                    </div>
                                    <div>
                                        <div class="mb-4">
                                            <h3 class="mb-2">Hi, Welcome Back!</h3>
                                            <p class="mb-0 text-muted">Please enter your credentials to sign in.</p>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-flat pass-group">
                                                <input type="password" name="password" class="form-control pass-input" required>
                                                <span class="input-group-text toggle-password" style="cursor:pointer;"><i class="icon-eye-off"></i></span>
                                            </div>
                                        </div>
                                        @if (session('error'))
                                            <div class="alert alert-danger border-0 border-danger border-bottom alert-dismissible pe-5" role="alert">
                                                <p class="fw-medium mb-0 d-inline-flex align-items-center">
                                                    <span class="btn btn-icon btn-xs rounded-circle bg-danger d-flex align-items-center justify-content-center pe-none me-2"><i class="icon-x fs-16 text-white"></i></span>{{ session('error') }}
                                                </p>
                                                <button type="button" class="btn-close btn-custom-close top-50 translate-middle-y link-danger" data-bs-dismiss="alert" aria-label="Close"><i class="icon-x"></i></button>
                                            </div>
                                        @endif
                                        @if ($errors->any())
                                            <div class="alert alert-danger border-0 alert-dismissible fade show" role="alert">
                                                <ul class="mb-0 ps-3">
                                                    @foreach ($errors->all() as $err)
                                                        <li>{{ $err }}</li>
                                                    @endforeach
                                                </ul>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        @endif
                                        <div class="mb-4">
                                            <button type="submit" class="btn btn-primary w-100">Sign In</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 d-none d-lg-block p-0">
                    <div class="position-relative d-flex align-items-center justify-content-center flex-wrap vh-100 overflow-hidden">
                        <div class="w-100 h-100 position-relative rounded-start-3 overflow-hidden" style="background: linear-gradient(135deg, #0D76E1 0%, #0a5bb5 100%); background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=1200&q=80'); background-size: cover; background-position: center;">
                            <div class="position-absolute inset-0 rounded-start-3" style="background: linear-gradient(135deg, rgba(13,118,225,0.85) 0%, rgba(10,91,181,0.75) 100%);"></div>
                            <div class="position-relative px-4 py-5 rounded-start-3 h-100 d-flex flex-column align-items-center justify-content-center auth-wrap text-white text-center">
                                <h1 class="h3 fw-bold mb-3">Complete Control of Your Cafe & Restaurant with Ease</h1>
                                <p class="mb-0 opacity-90">From billing to inventory — access everything you need in one powerful dashboard. Analyze sales, track your best-selling dishes.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.querySelectorAll('.toggle-password').forEach(function(el) {
        el.addEventListener('click', function() {
            var grp = this.closest('.pass-group');
            if (!grp) return;
            var inp = grp.querySelector('.pass-input');
            if (!inp) return;
            var isPass = inp.type === 'password';
            inp.type = isPass ? 'text' : 'password';
            var icon = this.querySelector('i');
            if (icon) icon.className = isPass ? 'icon-eye' : 'icon-eye-off';
        });
    });
    </script>

@endsection
