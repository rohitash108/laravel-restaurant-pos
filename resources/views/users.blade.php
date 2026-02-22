<?php $page = 'users'; ?>
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
                    <h3 class="mb-0">Users <a href="{{ route('users') }}" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <a href="#" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_users"><i class="icon-circle-plus me-1"></i>Add New</a>
                </div>
            </div>
            <!-- End Page Header -->

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- card start -->
            <div class="card mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between mb-4">
                        <div class="search-input">
                            <span class="btn-searchset"><i class="icon-search fs-14"></i></span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <!-- sort by -->
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                    Sort by : Newest
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-3">
                                    <li><a href="javascript:void(0);" class="dropdown-item">Newest</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item">Oldest</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item">Ascending</a></li>
                                    <li><a href="javascript:void(0);" class="dropdown-item">Descending</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- table start -->
                    <div class="table-responsive table-nowrap">
                        <table class="table mb-0 border datatable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Phone Number</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="avatar avatar-sm avatar-rounded flex-shrink-0 me-2">
                                                <img src="{{URL::asset('build/img/users/user-01.jpg')}}" alt="user" class="img-fluid">
                                            </a>
                                            <h6 class="fs-14 fw-normal mb-0"><a href="#">{{ $user->name }}</a></h6>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $user->role)) }}</td>
                                    <td>
                                        <p class="text-dark mb-0">{{ $user->phone ?? '-' }}</p>
                                    </td>
                                    <td>
                                        @if(($user->status ?? 'active') === 'active')
                                            <span class="badge badge-soft-success">Active</span>
                                        @else
                                            <span class="badge badge-soft-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(auth()->id() !== $user->id)
                                            <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle me-2" data-bs-toggle="modal" data-bs-target="#edit_user_{{ $user->id }}"><i class="icon-pencil-line"></i></a>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-sm btn-white rounded-circle text-danger"><i class="icon-trash-2"></i></button>
                                            </form>
                                        @else
                                            <span class="text-muted fs-12">Current user</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No users found. Click "Add New" to create one.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- table end -->
                </div>

            </div>
            <!-- card end -->

        </div>
        <!-- End Content -->

    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="add_users" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email<span class="text-danger ms-1">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password<span class="text-danger ms-1">*</span></label>
                            <input type="password" class="form-control" name="password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role<span class="text-danger ms-1">*</span></label>
                            <select class="form-select" name="role" required>
                                <option value="staff">Staff</option>
                                <option value="restaurant_admin">Admin / Owner</option>
                                <option value="cashier">Cashier</option>
                                <option value="chef">Chef</option>
                                <option value="waiter">Waiter</option>
                                <option value="delivery">Delivery</option>
                                <option value="accountant">Accountant</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="system_operator">System Operator</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modals -->
    @foreach($users as $user)
    @if(auth()->id() !== $user->id)
    <div class="modal fade" id="edit_user_{{ $user->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email<span class="text-danger ms-1">*</span></label>
                            <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input type="password" class="form-control" name="password" minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role<span class="text-danger ms-1">*</span></label>
                            <select class="form-select" name="role" required>
                                @foreach(['staff', 'restaurant_admin', 'cashier', 'chef', 'waiter', 'delivery', 'accountant', 'supervisor', 'system_operator'] as $role)
                                    <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" value="{{ $user->phone }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status">
                                <option value="active" {{ ($user->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ ($user->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
    @endforeach

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
