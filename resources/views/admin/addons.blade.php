@php $page = 'admin-addons'; @endphp
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
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

        {{-- Page Header --}}
        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <h3 class="mb-0">Addons</h3>
                <p class="text-muted small mb-0">Manage item addons / modifiers across all restaurants</p>
            </div>
            @if($selectedRestaurantId)
            <div>
                <a href="#" class="btn btn-primary d-inline-flex align-items-center"
                   data-bs-toggle="modal" data-bs-target="#add_addon_modal">
                    <i class="icon-circle-plus me-1"></i>Add Addon
                </a>
            </div>
            @endif
        </div>

        {{-- Restaurant Selector --}}
        <div class="card mb-4">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('admin.addons.index') }}" class="d-flex align-items-center gap-3 flex-wrap">
                    <label class="fw-medium mb-0 text-nowrap">Restaurant:</label>
                    <select name="restaurant_id" class="form-select w-auto" onchange="this.form.submit()">
                        <option value="">— Select Restaurant —</option>
                        @foreach($restaurants as $r)
                            <option value="{{ $r->id }}" {{ $selectedRestaurantId == $r->id ? 'selected' : '' }}>
                                {{ $r->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        @if(!$selectedRestaurantId)
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="icon-text-select fs-48 text-muted mb-3 d-block"></i>
                    <h5 class="mb-1">Select a restaurant</h5>
                    <p class="text-muted mb-0">Choose a restaurant above to view and manage its addons.</p>
                </div>
            </div>
        @elseif($addons->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="icon-text-select fs-48 text-muted mb-3 d-block"></i>
                    <h5 class="mb-2">No addons yet</h5>
                    <p class="text-muted mb-3">Addons (modifiers) let you offer extras like toppings or sides.</p>
                    <a href="#" class="btn btn-primary btn-sm"
                       data-bs-toggle="modal" data-bs-target="#add_addon_modal">
                        <i class="icon-circle-plus me-1"></i>Add First Addon
                    </a>
                </div>
            </div>
        @else
            <div class="card mb-0">
                <div class="card-body">
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
                                @foreach($addons as $addon)
                                <tr>
                                    <td>{{ $addon->item?->name ?? '–' }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $addon->addon_name }}</span>
                                        @if($addon->description)
                                            <br><small class="text-muted">{{ Str::limit($addon->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>₹{{ number_format($addon->price ?? 0, 2) }}</td>
                                    <td>
                                        <span class="badge {{ ($addon->status ?? 'active') === 'active' ? 'badge-soft-success' : 'badge-soft-secondary' }}">
                                            {{ ucfirst($addon->status ?? 'active') }}
                                        </span>
                                    </td>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

{{-- Add Addon Modal --}}
@if($selectedRestaurantId)
<div class="modal fade" id="add_addon_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.addons.store') }}">
                @csrf
                <input type="hidden" name="restaurant_id" value="{{ $selectedRestaurantId }}">
                <div class="modal-header">
                    <h5 class="modal-title">Add Addon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Item <span class="text-danger">*</span></label>
                        <select class="form-select" name="item_id" required>
                            <option value="">— Select Item —</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Addon Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="addon_name" value="{{ old('addon_name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0" class="form-control" name="price" value="{{ old('price', '0') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea rows="3" class="form-control" name="description" placeholder="Optional">{{ old('description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Addon</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Edit Addon Modal --}}
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
                        <textarea rows="3" class="form-control" name="description" id="edit_description"></textarea>
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

{{-- Delete Addon Modal --}}
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
                    <p>Delete <strong id="delete_addon_name"></strong>?</p>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-edit-addon').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = this.dataset.id;
            document.getElementById('edit_addon_form').action = '{{ url("admin/addons") }}/' + id;
            document.getElementById('edit_item_id').value = this.dataset.itemId;
            document.getElementById('edit_addon_name').value = this.dataset.addonName;
            document.getElementById('edit_price').value = this.dataset.price;
            document.getElementById('edit_description').value = this.dataset.description;
            document.getElementById('edit_status').value = this.dataset.status;
        });
    });

    document.querySelectorAll('.btn-delete-addon').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.getElementById('delete_addon_form').action = '{{ url("admin/addons") }}/' + this.dataset.id;
            document.getElementById('delete_addon_name').textContent = this.dataset.name;
        });
    });

    @if($errors->any())
        var addModal = document.getElementById('add_addon_modal');
        if (addModal) { new bootstrap.Modal(addModal).show(); }
    @endif
});
</script>
@endpush
@endsection
