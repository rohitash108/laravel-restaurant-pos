<?php $page = 'admin-restaurants'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <h3 class="mb-0">Add Restaurant</h3>
            </div>
            <a href="{{ route('admin.restaurants.index') }}" class="btn btn-white">Back to list</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.restaurants.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Restaurant Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <hr class="my-4">
                            <h5 class="mb-3">Create restaurant admin account (optional)</h5>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="create_admin" value="1" id="create_admin" {{ old('create_admin') ? 'checked' : '' }}>
                                <label class="form-check-label" for="create_admin">Create an admin user for this restaurant</label>
                            </div>
                            <div id="admin_fields" style="{{ old('create_admin') ? '' : 'display:none' }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Admin Name <span class="text-danger">*</span></label>
                                            <input type="text" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror" value="{{ old('admin_name') }}">
                                            @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Admin Email <span class="text-danger">*</span></label>
                                            <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror" value="{{ old('admin_email') }}">
                                            @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Password <span class="text-danger">*</span></label>
                                            <input type="password" name="admin_password" class="form-control @error('admin_password') is-invalid @enderror">
                                            @error('admin_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                            <input type="password" name="admin_password_confirmation" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">Create Restaurant</button>
                            <a href="{{ route('admin.restaurants.index') }}" class="btn btn-white">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('create_admin').addEventListener('change', function() {
    document.getElementById('admin_fields').style.display = this.checked ? 'block' : 'none';
});
</script>
@endsection
