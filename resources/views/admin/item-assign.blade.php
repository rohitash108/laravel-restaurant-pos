@php $page = 'admin-items'; @endphp
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        {{-- Header --}}
        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <a href="{{ route('admin.items.index') }}" class="text-muted small d-inline-flex align-items-center mb-1">
                    <i class="icon-chevron-left me-1"></i> Back to Items
                </a>
                <h3 class="mb-0">Assign: {{ $item->name }}</h3>
                <p class="text-muted mb-0 small">Choose which restaurants can see and sell this item, and set its category in each restaurant's POS.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row">
            {{-- Item preview --}}
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        @if($item->image)
                            <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}"
                                 class="img-fluid rounded mb-3" style="max-height:140px;object-fit:cover;">
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3 mx-auto" style="width:100px;height:100px;">
                                <i class="icon-layout-list fs-36 text-muted"></i>
                            </div>
                        @endif
                        <h5 class="fw-semibold mb-1">{{ $item->name }}</h5>
                        <p class="mb-1 fw-medium">₹{{ number_format($item->price, 2) }}</p>
                        @if($item->description)
                            <p class="small text-muted mb-0">{{ Str::limit($item->description, 80) }}</p>
                        @endif
                        <hr>
                        <div class="d-flex align-items-center justify-content-between text-muted small">
                            <span>Assigned to</span>
                            <span class="badge bg-primary" id="assigned-count-badge">{{ count($assignedIds) }}</span>
                        </div>
                        <p class="text-muted small mb-0">restaurant(s)</p>
                    </div>
                </div>
            </div>

            {{-- Restaurant + Category selection --}}
            <div class="col-lg-9 col-md-8">
                <div class="card">
                    <div class="card-header border-0 py-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h5 class="mb-0">Assign to Restaurants</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="select-all-btn">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect-all-btn">Deselect All</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($restaurants->isEmpty())
                            <p class="text-muted text-center py-4">No restaurants found. <a href="{{ route('admin.restaurants.create') }}">Create one first.</a></p>
                        @else
                        <form action="{{ route('admin.items.assign.save', $item) }}" method="POST">
                            @csrf
                            <div class="mb-4" id="restaurant-list">
                                @foreach($restaurants as $restaurant)
                                @php
                                    $isAssigned    = in_array($restaurant->id, $assignedIds);
                                    $assignment    = $assignments->get($restaurant->id);
                                    $currentCatId  = $assignment?->category_id;
                                    $restaurantCats = $categoriesByRestaurant->get($restaurant->id, collect());
                                @endphp
                                <div class="border rounded p-3 mb-3 restaurant-row {{ $isAssigned ? 'border-primary' : '' }}"
                                     data-restaurant-id="{{ $restaurant->id }}">
                                    <div class="d-flex align-items-center gap-3">
                                        <input type="checkbox"
                                               name="restaurant_ids[]"
                                               value="{{ $restaurant->id }}"
                                               id="rest_{{ $restaurant->id }}"
                                               class="form-check-input flex-shrink-0 rest-checkbox"
                                               style="width:18px;height:18px;"
                                               {{ $isAssigned ? 'checked' : '' }}>
                                        <label for="rest_{{ $restaurant->id }}" class="fw-medium mb-0 flex-grow-1" style="cursor:pointer;">
                                            {{ $restaurant->name }}
                                            @if($isAssigned)
                                                <span class="badge badge-soft-success ms-1 fs-11">Assigned</span>
                                            @endif
                                        </label>
                                    </div>

                                    {{-- Category picker — only visible when checked --}}
                                    <div class="cat-picker mt-3 ps-4 {{ $isAssigned ? '' : 'd-none' }}">
                                        <label class="form-label small fw-medium mb-1">
                                            <i class="icon-tag me-1"></i>Category in POS <span class="text-danger">*</span>
                                        </label>
                                        @if($restaurantCats->isEmpty())
                                            <p class="small text-warning mb-0">
                                                <i class="icon-alert-triangle me-1"></i>
                                                No categories for this restaurant.
                                                <a href="{{ route('admin.categories.index', ['restaurant_id' => $restaurant->id]) }}" class="alert-link" target="_blank">Add one first</a>.
                                            </p>
                                        @else
                                            <select name="category_ids[{{ $restaurant->id }}]"
                                                    class="form-select form-select-sm"
                                                    style="max-width:280px;"
                                                    {{ $isAssigned ? 'required' : '' }}>
                                                <option value="">— Select category —</option>
                                                @foreach($restaurantCats as $cat)
                                                    <option value="{{ $cat->id }}" {{ $currentCatId == $cat->id ? 'selected' : '' }}>
                                                        {{ $cat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="icon-save me-1"></i>Save Assignments
                                </button>
                                <a href="{{ route('admin.items.index') }}" class="btn btn-light">Cancel</a>
                                <span class="text-muted small ms-auto" id="selected-count">
                                    {{ count($assignedIds) }} restaurant(s) selected
                                </span>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    function updateRow(row, checked) {
        var picker = row.querySelector('.cat-picker');
        var selects = picker ? picker.querySelectorAll('select') : [];
        if (checked) {
            row.classList.add('border-primary');
            if (picker) picker.classList.remove('d-none');
            selects.forEach(function(s){ s.setAttribute('required',''); });
        } else {
            row.classList.remove('border-primary');
            if (picker) picker.classList.add('d-none');
            selects.forEach(function(s){ s.removeAttribute('required'); });
        }
    }

    function updateCount() {
        var count = document.querySelectorAll('.rest-checkbox:checked').length;
        document.getElementById('selected-count').textContent = count + ' restaurant(s) selected';
        document.getElementById('assigned-count-badge').textContent = count;
    }

    document.querySelectorAll('.rest-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            updateRow(cb.closest('.restaurant-row'), cb.checked);
            updateCount();
        });
    });

    document.getElementById('select-all-btn').addEventListener('click', function () {
        document.querySelectorAll('.rest-checkbox').forEach(function (cb) {
            cb.checked = true;
            updateRow(cb.closest('.restaurant-row'), true);
        });
        updateCount();
    });

    document.getElementById('deselect-all-btn').addEventListener('click', function () {
        document.querySelectorAll('.rest-checkbox').forEach(function (cb) {
            cb.checked = false;
            updateRow(cb.closest('.restaurant-row'), false);
        });
        updateCount();
    });
});
</script>
@endsection
