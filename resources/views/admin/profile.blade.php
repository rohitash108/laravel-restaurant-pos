@php $page = 'admin-profile'; @endphp
@extends('layout.mainlayout')
@section('content')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1">
                    <h3 class="mb-1">Account</h3>
                    <p class="text-muted mb-0">Update your display name, email, or password. Only you can change these settings.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success border-0 mb-4" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <form action="{{ route('admin.profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <h6 class="text-uppercase text-muted fs-12 fw-semibold mb-3">Profile</h6>
                                    <div class="mb-3">
                                        <label for="profile-name" class="form-label">Name</label>
                                        <input type="text" name="name" id="profile-name" class="form-control @error('name') is-invalid @enderror"
                                               value="{{ old('name', $user->name) }}" required autocomplete="name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label for="profile-email" class="form-label">Email</label>
                                        <input type="email" name="email" id="profile-email" class="form-control @error('email') is-invalid @enderror"
                                               value="{{ old('email', $user->email) }}" required autocomplete="email">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <hr class="border-opacity-10 my-4">

                                <div class="mb-2">
                                    <h6 class="text-uppercase text-muted fs-12 fw-semibold mb-3">Change password</h6>
                                    <p class="text-muted small mb-3">Leave blank to keep your current password. To set a new password, enter your current password first.</p>
                                    <div class="mb-3">
                                        <label for="profile-current-password" class="form-label">Current password</label>
                                        <input type="password" name="current_password" id="profile-current-password"
                                               class="form-control @error('current_password') is-invalid @enderror"
                                               autocomplete="current-password">
                                        @error('current_password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="profile-password" class="form-label">New password</label>
                                        <input type="password" name="password" id="profile-password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <label for="profile-password-confirmation" class="form-label">Confirm new password</label>
                                        <input type="password" name="password_confirmation" id="profile-password-confirmation"
                                               class="form-control" autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 justify-content-end mt-4 pt-2">
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-white">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
