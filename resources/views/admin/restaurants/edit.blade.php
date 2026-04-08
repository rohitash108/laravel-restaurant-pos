<?php $page = 'admin-restaurants'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <h3 class="mb-0">Edit Restaurant</h3>
            </div>
            <a href="{{ route('admin.restaurants.index') }}" class="btn btn-white">Back to list</a>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.restaurants.update', $restaurant) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Restaurant Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $restaurant->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $restaurant->email) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $restaurant->phone) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ old('is_active', $restaurant->is_active) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !old('is_active', $restaurant->is_active) ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-4">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $restaurant->address) }}</textarea>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="border-top pt-4 mt-2 mb-4">
                                <h6 class="mb-1">Restaurant Admin Password</h6>
                                <p class="text-muted mb-3">Optional. Set a new password for the restaurant admin login.</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="password" name="admin_password" class="form-control @error('admin_password') is-invalid @enderror" autocomplete="new-password">
                                            @error('admin_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Confirm New Password</label>
                                            <input type="password" name="admin_password_confirmation" class="form-control" autocomplete="new-password">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Restaurant</button>
                            <a href="{{ route('admin.restaurants.index') }}" class="btn btn-white">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
