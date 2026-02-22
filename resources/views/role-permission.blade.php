<?php $page = 'role-permission'; ?>
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
                    <h3 class="mb-0">Permissions <a href="{{ route('role-permission') }}"
                            class="btn btn-icon btn-sm btn-white rounded-circle ms-2" title="Refresh"><i
                                class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <a href="#" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal"
                        data-bs-target="#add_role"><i class="icon-circle-plus me-1"></i>Add New</a>
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

            @if($roles->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="icon-shield-alert fs-48 text-muted mb-3 d-block"></i>
                        <h5>No Roles Created Yet</h5>
                        <p class="text-muted mb-3">Click "Add New" to create a role and manage permissions.</p>
                        <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_role"><i class="icon-circle-plus me-1"></i>Create Your First Role</a>
                    </div>
                </div>
            @else
            <!-- card start -->
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-5">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="fs-20 fw-bold mb-4">Roles</h6>
                            <div class="roles-sidebar d-flex align-items-start">
                                <ul class="nav flex-column nav-pills me-3 w-100" id="v-pills-tab" role="tablist"
                                    aria-orientation="vertical">
                                    @foreach($roles as $index => $role)
                                    <li class="d-flex align-items-center {{ $loop->last ? '' : 'mb-1' }}">
                                        <a href="#" class="nav-link flex-grow-1 {{ $index === 0 ? 'active' : '' }}" id="v-pills-role-{{ $role->id }}-tab"
                                            data-bs-toggle="pill" data-bs-target="#v-pills-role-{{ $role->id }}" type="button"
                                            role="tab" aria-controls="v-pills-role-{{ $role->id }}" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">{{ $role->name }}</a>
                                        <form action="{{ route('role-permission.destroy', $role) }}" method="POST" class="ms-1 d-inline flex-shrink-0"
                                            onsubmit="return confirm({{ Js::from('Delete role ' . $role->name . '?') }})">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-1" title="Delete Role"><i class="icon-trash-2 fs-12"></i></button>
                                        </form>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-7">
                    <div class="tab-content" id="v-pills-tabContent">
                        @foreach($roles as $index => $role)
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="v-pills-role-{{ $role->id }}" role="tabpanel"
                            aria-labelledby="v-pills-role-{{ $role->id }}-tab" tabindex="0">
                            <form action="{{ route('role-permission.update', $role) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="name" value="{{ $role->name }}">

                                <div class="d-flex align-items-center flex-wrap gap-2 mb-4">
                                    <div class="flex-grow-1">
                                        <h5 class="fs-16 fw-bold mb-0">Role : {{ $role->name }}</h5>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input select-all-checkbox" type="checkbox" id="select_all_{{ $role->id }}">
                                        <label class="form-check-label small text-muted" for="select_all_{{ $role->id }}">Select All</label>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table m-0 table-nowrap bg-white border">
                                                <thead>
                                                    <tr>
                                                        <th>Module</th>
                                                        @foreach($allActions as $action)
                                                            <th>{{ ucfirst(str_replace('_', ' ', $action)) }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $rolePerms = $role->permissions ?? []; @endphp
                                                    @foreach($modules as $moduleName => $moduleActions)
                                                    <tr>
                                                        <td class="text-dark fw-medium">{{ ucfirst(str_replace('_', ' ', $moduleName)) }}</td>
                                                        @foreach($allActions as $action)
                                                            <td>
                                                                @if(in_array($action, $moduleActions))
                                                                <div class="form-check form-check-md">
                                                                    <input class="form-check-input perm-checkbox" type="checkbox"
                                                                        name="permissions[{{ $moduleName }}][{{ $action }}]"
                                                                        value="1"
                                                                        {{ !empty($rolePerms[$moduleName][$action]) ? 'checked' : '' }}>
                                                                </div>
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-2 border-top mt-4 pt-4">
                                            <button type="button" class="btn btn-light me-2" onclick="window.location.reload()">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- card end -->
            @endif

        </div>
        <!-- End Content -->

    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="add_role" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('role-permission.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Role Name<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required placeholder="e.g. Cashier, Chef, Waiter...">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Select All toggle per role tab
    document.querySelectorAll('.select-all-checkbox').forEach(function (selectAll) {
        selectAll.addEventListener('change', function () {
            var tabPane = this.closest('.tab-pane');
            if (tabPane) {
                tabPane.querySelectorAll('.perm-checkbox').forEach(function (cb) {
                    cb.checked = selectAll.checked;
                });
            }
        });
    });

    // Auto-open Add Role modal on validation error
    @if($errors->any())
        var addModal = document.getElementById('add_role');
        if (addModal) {
            new bootstrap.Modal(addModal).show();
        }
    @endif
});
</script>
@endpush
