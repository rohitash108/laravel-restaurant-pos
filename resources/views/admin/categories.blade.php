@php $page = 'admin-categories'; @endphp
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        {{-- Header --}}
        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <h3 class="mb-0">Categories</h3>
                <p class="text-muted mb-0 small">Manage categories for any restaurant</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="mb-0">
                    <select name="restaurant_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:200px;">
                        @foreach($restaurants as $r)
                            <option value="{{ $r->id }}" {{ $r->id === $selectedRestaurantId ? 'selected' : '' }}>{{ $r->name }}</option>
                        @endforeach
                    </select>
                </form>
                @if($selectedRestaurantId)
                <a href="{{ route('admin.items.index', ['restaurant_id' => $selectedRestaurantId]) }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center">
                    <i class="icon-layout-list me-1"></i>Items
                </a>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_cat_modal">
                    <i class="icon-circle-plus me-1"></i>Add Category
                </button>
                @endif
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

        @if($restaurants->isEmpty())
        <div class="alert alert-info border-0">No restaurants yet. <a href="{{ route('admin.restaurants.create') }}">Create one first.</a></div>
        @else
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60px;">Image</th>
                                <th>Name</th>
                                <th>Items</th>
                                <th>Sort</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $cat)
                            <tr>
                                <td>
                                    @if($cat->image)
                                        <img src="{{ asset('storage/'.$cat->image) }}" alt="{{ $cat->name }}" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                            <i class="icon-tag text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-medium align-middle">{{ $cat->name }}</td>
                                <td class="align-middle">{{ $cat->items_count }}</td>
                                <td class="align-middle">{{ $cat->sort_order }}</td>
                                <td class="align-middle">
                                    <span class="badge {{ $cat->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                        {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="align-middle text-end">
                                    <button type="button" class="btn btn-sm btn-white edit-cat-btn"
                                            data-id="{{ $cat->id }}"
                                            data-name="{{ e($cat->name) }}"
                                            data-sort="{{ $cat->sort_order }}"
                                            data-active="{{ $cat->is_active ? '1' : '0' }}">
                                        <i class="icon-pencil-line"></i>
                                    </button>
                                    <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category? Items inside may be affected.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-white text-danger"><i class="icon-trash-2"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    No categories yet. Click <strong>Add Category</strong> to create one.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- ADD CATEGORY MODAL --}}
<div class="modal fade" id="add_cat_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 p-4 pb-0">
                <h4 class="modal-title">Add Category</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="restaurant_id" value="{{ $selectedRestaurantId }}">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="add_cat_active" value="1" checked>
                        <label class="form-check-label" for="add_cat_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EDIT CATEGORY MODAL --}}
<div class="modal fade" id="edit_cat_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 p-4 pb-0">
                <h4 class="modal-title">Edit Category</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="edit_cat_form" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_cat_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <p class="small text-muted mt-1 mb-0">Leave empty to keep current</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="edit_cat_sort" class="form-control" min="0">
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="edit_cat_active" value="1">
                        <label class="form-check-label" for="edit_cat_active">Active</label>
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
document.querySelectorAll('.edit-cat-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var id   = btn.getAttribute('data-id');
        var form = document.getElementById('edit_cat_form');
        form.action = '{{ url("admin/categories") }}/' + id;
        document.getElementById('edit_cat_name').value = btn.getAttribute('data-name') || '';
        document.getElementById('edit_cat_sort').value = btn.getAttribute('data-sort') || 0;
        document.getElementById('edit_cat_active').checked = btn.getAttribute('data-active') === '1';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('edit_cat_modal')).show();
    });
});
</script>
@endsection
