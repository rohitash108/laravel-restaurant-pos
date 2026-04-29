@php $page = 'admin-users'; @endphp
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        {{-- Header --}}
        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <h3 class="mb-0">Users</h3>
                <p class="text-muted mb-0 small">Manage super admin users and their module access</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <form method="GET" action="{{ route('admin.users.index') }}" class="mb-0">
                    <select name="restaurant_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:200px;">
                        <option value="">All Users</option>
                        <option value="0" {{ $selectedRestaurantId === 0 ? 'selected' : '' }}>Super Admins Only</option>
                        @foreach($restaurants as $r)
                            <option value="{{ $r->id }}" {{ $r->id === $selectedRestaurantId ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </form>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_user_modal">
                    <i class="icon-circle-plus me-1"></i>Add User
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible border-0 mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible border-0 mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible border-0 mb-4">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Restaurant</th>
                                <th>Module Access</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $u)
                            @php
                                $userModules = $u->isSuperAdmin()
                                    ? \App\Http\Controllers\Admin\UserController::getUserModules($u)
                                    : [];
                            @endphp
                            <tr>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm avatar-rounded bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-medium">{{ $u->name }}</span>
                                            @if($u->phone)
                                                <p class="small text-muted mb-0">{{ $u->phone }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">{{ $u->email }}</td>
                                <td class="align-middle">
                                    @if($u->isSuperAdmin())
                                        <span class="badge bg-primary">Super Admin</span>
                                        @if($u->isOwner())
                                            <span class="badge badge-soft-warning ms-1">Owner</span>
                                        @else
                                            <span class="badge badge-soft-info ms-1">Manager</span>
                                        @endif
                                    @else
                                        <span class="badge badge-soft-secondary">{{ ucfirst(str_replace('_', ' ', $u->role)) }}</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($u->restaurant)
                                        <span class="small">{{ $u->restaurant->name }}</span>
                                    @elseif($u->isSuperAdmin())
                                        <span class="text-muted small">—</span>
                                    @else
                                        <span class="text-muted small">None</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($u->isSuperAdmin())
                                        @if($u->isOwner())
                                            <span class="badge badge-soft-success">All Modules</span>
                                        @elseif(!empty($userModules))
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($userModules as $mod)
                                                    <span class="badge badge-soft-primary fs-11">{{ ucfirst($mod) }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small">No access</span>
                                        @endif
                                    @else
                                        <span class="text-muted small">Restaurant-level</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <span class="badge {{ ($u->status ?? 'active') === 'active' ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                        {{ ucfirst($u->status ?? 'active') }}
                                    </span>
                                </td>
                                <td class="align-middle text-end">
                                    @if(auth()->id() !== $u->id)
                                        <button type="button" class="btn btn-sm btn-white edit-user-btn"
                                                data-id="{{ $u->id }}"
                                                data-name="{{ e($u->name) }}"
                                                data-email="{{ e($u->email) }}"
                                                data-role="{{ $u->role }}"
                                                data-admin-level="{{ $u->admin_level ?? '' }}"
                                                data-restaurant-id="{{ $u->restaurant_id ?? '' }}"
                                                data-phone="{{ e($u->phone ?? '') }}"
                                                data-status="{{ $u->status ?? 'active' }}"
                                                data-modules="{{ implode(',', $userModules) }}">
                                            <i class="icon-pencil-line"></i>
                                        </button>
                                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete user {{ e($u->name) }}?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-white text-danger"><i class="icon-trash-2"></i></button>
                                        </form>
                                    @else
                                        <span class="text-muted small">You</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    No users found. Click <strong>Add User</strong> to create one.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ADD USER MODAL --}}
<div class="modal fade" id="add_user_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 p-4 pb-0">
                <h4 class="modal-title">Add User</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select add-role-select" required>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 add-admin-level-wrap">
                            <label class="form-label">Admin Level</label>
                            <select name="admin_level" class="form-select">
                                <option value="manager">Manager</option>
                                <option value="owner">Owner (Full Access)</option>
                            </select>
                        </div>
                        <div class="col-md-6 add-restaurant-wrap d-none">
                            <label class="form-label">Restaurant</label>
                            <select name="restaurant_id" class="form-select">
                                <option value="">— Select restaurant —</option>
                                @foreach($restaurants as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 add-modules-wrap">
                            <label class="form-label">Module Access</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($availableModules as $key => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="modules[]" value="{{ $key }}" id="add_mod_{{ $key }}" checked>
                                    <label class="form-check-label" for="add_mod_{{ $key }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>
                            <p class="small text-muted mt-1 mb-0">Select which admin modules this user can access.</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT USER MODAL --}}
<div class="modal fade" id="edit_user_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 p-4 pb-0">
                <h4 class="modal-title">Edit User</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="edit_user_form" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <small class="text-muted">(leave blank to keep)</small></label>
                            <input type="password" name="password" class="form-control" minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role" id="edit_role" class="form-select edit-role-select" required>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 edit-admin-level-wrap">
                            <label class="form-label">Admin Level</label>
                            <select name="admin_level" id="edit_admin_level" class="form-select">
                                <option value="manager">Manager</option>
                                <option value="owner">Owner (Full Access)</option>
                            </select>
                        </div>
                        <div class="col-md-6 edit-restaurant-wrap d-none">
                            <label class="form-label">Restaurant</label>
                            <select name="restaurant_id" id="edit_restaurant_id" class="form-select">
                                <option value="">— Select restaurant —</option>
                                @foreach($restaurants as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 edit-modules-wrap">
                            <label class="form-label">Module Access</label>
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($availableModules as $key => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="modules[]" value="{{ $key }}" id="edit_mod_{{ $key }}">
                                    <label class="form-check-label" for="edit_mod_{{ $key }}">{{ $label }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    // Toggle role-dependent fields
    function toggleRoleFields(roleSelect, adminLevelWrap, restaurantWrap, modulesWrap) {
        var role = roleSelect.value;
        var isSuperAdmin = role === 'super_admin';
        adminLevelWrap.classList.toggle('d-none', !isSuperAdmin);
        restaurantWrap.classList.toggle('d-none', isSuperAdmin);
        modulesWrap.classList.toggle('d-none', !isSuperAdmin);
    }

    // Add modal
    var addRoleSelect = document.querySelector('.add-role-select');
    if (addRoleSelect) {
        var addAdminWrap = document.querySelector('.add-admin-level-wrap');
        var addRestWrap  = document.querySelector('.add-restaurant-wrap');
        var addModWrap   = document.querySelector('.add-modules-wrap');
        toggleRoleFields(addRoleSelect, addAdminWrap, addRestWrap, addModWrap);
        addRoleSelect.addEventListener('change', function () {
            toggleRoleFields(addRoleSelect, addAdminWrap, addRestWrap, addModWrap);
        });
    }

    // Edit modal
    var editRoleSelect = document.querySelector('.edit-role-select');
    if (editRoleSelect) {
        var editAdminWrap = document.querySelector('.edit-admin-level-wrap');
        var editRestWrap  = document.querySelector('.edit-restaurant-wrap');
        var editModWrap   = document.querySelector('.edit-modules-wrap');
        editRoleSelect.addEventListener('change', function () {
            toggleRoleFields(editRoleSelect, editAdminWrap, editRestWrap, editModWrap);
        });
    }

    // Edit button click
    document.querySelectorAll('.edit-user-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-id');
            var form = document.getElementById('edit_user_form');
            form.action = '{{ url("admin/users") }}/' + id;

            document.getElementById('edit_name').value       = btn.getAttribute('data-name') || '';
            document.getElementById('edit_email').value      = btn.getAttribute('data-email') || '';
            document.getElementById('edit_phone').value      = btn.getAttribute('data-phone') || '';
            document.getElementById('edit_role').value       = btn.getAttribute('data-role') || 'staff';
            document.getElementById('edit_admin_level').value = btn.getAttribute('data-admin-level') || 'manager';
            document.getElementById('edit_restaurant_id').value = btn.getAttribute('data-restaurant-id') || '';
            document.getElementById('edit_status').value     = btn.getAttribute('data-status') || 'active';

            // Modules
            var modules = (btn.getAttribute('data-modules') || '').split(',').filter(Boolean);
            document.querySelectorAll('[id^="edit_mod_"]').forEach(function (cb) {
                cb.checked = modules.indexOf(cb.value) !== -1;
            });

            // Toggle fields
            toggleRoleFields(editRoleSelect, editAdminWrap, editRestWrap, editModWrap);

            bootstrap.Modal.getOrCreateInstance(document.getElementById('edit_user_modal')).show();
        });
    });
})();
</script>
@endsection
