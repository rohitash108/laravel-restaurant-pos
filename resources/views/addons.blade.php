<?php $page = 'addons'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Page Header -->
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1">
                    <h3 class="mb-0">Addons<a href="{{ route('addons') }}" class="btn btn-icon btn-sm btn-white rounded-circle ms-2" title="Refresh"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <a href="#" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_modifier"><i class="icon-circle-plus me-1"></i>Add New</a>
                </div>
            </div>
            <!-- End Page Header -->

            @forelse($addons as $addon)
                @if($loop->first)
                <!-- card start -->
                <div class="card mb-0">
                    <div class="card-body">
                        <!-- table start -->
                        <div class="table-responsive">
                            <table class="table mb-0 border">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Addon</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                @endif

                <tr>
                    <td>{{ $addon->item?->name ?? '–' }}</td>
                    <td>
                        <span class="fw-medium">{{ $addon->addon_name ?? '–' }}</span>
                        @if($addon->description)
                            <br><small class="text-muted">{{ Str::limit($addon->description, 40) }}</small>
                        @endif
                    </td>
                    <td>${{ number_format($addon->price ?? 0, 2) }}</td>
                    <td><span class="badge {{ ($addon->status ?? 'active') === 'active' ? 'badge-soft-success' : 'badge-soft-secondary' }}">{{ ucfirst($addon->status ?? 'Active') }}</span></td>
                    <td class="text-end">
                        <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle me-1 btn-edit-addon"
                            data-bs-toggle="modal" data-bs-target="#edit_addon_modal"
                            data-id="{{ $addon->id }}"
                            data-item-id="{{ $addon->item_id }}"
                            data-addon-name="{{ $addon->addon_name }}"
                            data-price="{{ $addon->price }}"
                            data-description="{{ $addon->description ?? '' }}"
                            data-status="{{ $addon->status ?? 'active' }}"
                            title="Edit"><i class="icon-pencil-line"></i></a>
                        <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle btn-delete-addon"
                            data-bs-toggle="modal" data-bs-target="#delete_addon_modal"
                            data-id="{{ $addon->id }}"
                            data-name="{{ $addon->addon_name }}"
                            title="Delete"><i class="icon-trash-2"></i></a>
                    </td>
                </tr>

                @if($loop->last)
                                </tbody>
                            </table>
                        </div>
                        <!-- table end -->
                    </div>
                </div>
                <!-- card end -->
                @endif
            @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="icon-text-select fs-48 text-muted mb-3 d-block"></i>
                        <h5 class="mb-2">No addons yet</h5>
                        <p class="text-muted mb-3">Addons (modifiers) let you offer extras like toppings or sides.</p>
                        <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_modifier"><i class="icon-circle-plus me-1"></i>Add Your First Addon</a>
                    </div>
                </div>
            @endforelse

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        Reusable Edit Modal (single instance, populated via JS)
    ========================= -->
    <div class="modal fade" id="edit_addon_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="edit_addon_form" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Addon</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Item <span class="text-danger">*</span></label>
                            <select class="form-select" name="item_id" id="edit_item_id" required>
                                @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Addon Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="addon_name" id="edit_addon_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control" name="price" id="edit_price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea rows="3" class="form-control" name="description" id="edit_description" placeholder="Optional description"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================
        Reusable Delete Modal (single instance, populated via JS)
    ========================= -->
    <div class="modal fade" id="delete_addon_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form id="delete_addon_form" method="POST">
                    @csrf @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Addon</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong id="delete_addon_name"></strong>?</p>
                        <p class="text-muted small mb-0">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
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
    // Populate Edit Modal
    document.querySelectorAll('.btn-edit-addon').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = this.dataset.id;
            document.getElementById('edit_addon_form').action = '{{ url("addons") }}/' + id;
            document.getElementById('edit_item_id').value = this.dataset.itemId;
            document.getElementById('edit_addon_name').value = this.dataset.addonName;
            document.getElementById('edit_price').value = this.dataset.price;
            document.getElementById('edit_description').value = this.dataset.description;
            document.getElementById('edit_status').value = this.dataset.status;
        });
    });

    // Populate Delete Modal
    document.querySelectorAll('.btn-delete-addon').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = this.dataset.id;
            document.getElementById('delete_addon_form').action = '{{ url("addons") }}/' + id;
            document.getElementById('delete_addon_name').textContent = this.dataset.name;
        });
    });

    // Auto-open add modal if there were validation errors (likely from the add form)
    @if($errors->any())
        var addModal = document.getElementById('add_modifier');
        if (addModal) {
            new bootstrap.Modal(addModal).show();
        }
    @endif
});
</script>
@endpush
