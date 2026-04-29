@php $page = 'admin-items'; @endphp
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">

        {{-- Header --}}
        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <h3 class="mb-0">Product Catalog</h3>
                <p class="text-muted mb-0 small">Super Admin manages all products</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                    <i class="icon-tag me-1"></i>Categories
                </a>
                <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_master_item_modal">
                    <i class="icon-circle-plus me-1"></i>Add Master Item
                </button>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible border-0 mb-4">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible border-0 mb-4">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible border-0 mb-4">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- Tabs --}}
        <ul class="nav nav-tabs nav-tabs-solid border-0 mb-4" role="tablist">
            <li class="nav-item">
                <a href="#tab-master" class="nav-link active" data-bs-toggle="tab">
                    Master Items <span class="badge bg-primary ms-1">{{ $masterItems->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#tab-restaurant" class="nav-link" data-bs-toggle="tab">
                    Restaurant Items
                </a>
            </li>
        </ul>

        <div class="tab-content">

            {{-- ══════════════════════════════════════════
                 TAB 1 — MASTER ITEMS
            ══════════════════════════════════════════ --}}
            <div class="tab-pane show active" id="tab-master">

                <div class="alert alert-info border-0 mb-4 py-2">
                    <i class="icon-info me-1"></i>
                    <strong>Master Items</strong> are global products created by Super Admin.
                    Click <strong>Assign</strong> on any item to choose which restaurants can see and sell it.
                </div>

                @if($masterItems->isEmpty())
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="icon-layout-list fs-48 text-muted mb-3"></i>
                        <h5 class="mb-2">No master items yet</h5>
                        <p class="text-muted mb-3">Create your first master product and assign it to restaurants.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_master_item_modal">
                            <i class="icon-circle-plus me-1"></i>Add Master Item
                        </button>
                    </div>
                </div>
                @else
                <div class="row">
                    @foreach($masterItems as $item)
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4 item-card"
                         data-item-id="{{ $item->id }}"
                         data-item-restaurant-id=""
                         data-item-name="{{ e($item->name) }}"
                         data-item-description="{{ e($item->description ?? '') }}"
                         data-item-price="{{ $item->price }}"
                         data-item-net-price="{{ $item->net_price ?? '' }}"
                         data-item-tax-id="{{ $item->tax_id ?? '' }}"
                         data-item-category-id="{{ $item->category_id ?? '' }}"
                         data-item-food-type="{{ $item->food_type ?? 'veg' }}"
                         data-item-variations="{{ e(($item->variations ?? collect())->map(fn($v)=>['name'=>$v->name,'price'=>(float)$v->price])->values()->toJson()) }}"
                         data-item-addons="{{ e(($item->addons ?? collect())->map(fn($a)=>['addon_name'=>$a->addon_name,'price'=>(float)$a->price])->values()->toJson()) }}"
                         data-item-plan-ids="{{ e($item->plans->pluck('id')->values()->toJson()) }}">
                        <div class="card h-100">
                            <div class="card-body">
                                {{-- Image --}}
                                @if($item->image)
                                    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}"
                                         class="img-fluid w-100 rounded mb-2" style="height:130px;object-fit:cover;">
                                @else
                                    <div class="bg-light rounded w-100 d-flex align-items-center justify-content-center mb-2" style="height:130px;">
                                        <i class="icon-layout-list fs-36 text-muted"></i>
                                    </div>
                                @endif

                                {{-- Name & type --}}
                                <h6 class="fs-14 fw-semibold mb-1">{{ $item->name }}</h6>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-medium">₹{{ number_format($item->price, 2) }}</span>
                                    <span class="small">
                                        @if(($item->food_type ?? 'veg') === 'non_veg')
                                            <i class="icon-square-dot text-danger"></i> Non Veg
                                        @elseif(($item->food_type ?? '') === 'egg')
                                            <i class="icon-square-dot text-warning"></i> Egg
                                        @else
                                            <i class="icon-square-dot text-success"></i> Veg
                                        @endif
                                    </span>
                                </div>

                                {{-- Plans this item belongs to --}}
                                @if($item->plans->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        @foreach($item->plans as $plan)
                                            <a href="{{ route('admin.plan-items', $plan) }}"
                                               class="badge badge-soft-info text-decoration-none fs-11">
                                                <i class="icon-tag me-1"></i>{{ $plan->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="small text-warning mb-2"><i class="icon-alert-triangle me-1"></i>Not in any plan</p>
                                @endif


                                {{-- Actions --}}
                                <div class="d-flex gap-2 mt-auto pt-1">
                                    <a href="{{ route('admin.items.assign', $item) }}"
                                       class="btn btn-sm btn-primary flex-fill">
                                        <i class="icon-warehouse me-1"></i>Assign
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-secondary edit-item-btn" title="Edit">
                                        <i class="icon-pencil-line"></i>
                                    </button>
                                    <form action="{{ route('admin.items.destroy', $item) }}" method="POST"
                                          class="d-inline" onsubmit="return confirm('Delete this item? It will be removed from all assignments.');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="icon-trash-2"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- ══════════════════════════════════════════
                 TAB 2 — RESTAURANT-SPECIFIC ITEMS
            ══════════════════════════════════════════ --}}
            <div class="tab-pane" id="tab-restaurant">
                <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                    <form method="GET" action="{{ route('admin.items.index') }}" class="mb-0 d-flex align-items-center gap-2">
                        <select name="restaurant_id" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width:220px;">
                            <option value="">— Select Restaurant —</option>
                            @foreach($restaurants as $r)
                                <option value="{{ $r->id }}" {{ $r->id === $selectedRestaurantId ? 'selected' : '' }}>{{ $r->name }}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="tab" value="restaurant">
                    </form>
                    @if($selectedRestaurantId)
                    <button type="button" class="btn btn-sm btn-primary d-inline-flex align-items-center"
                            data-bs-toggle="modal" data-bs-target="#add_restaurant_item_modal"
                            @if($globalCategories->isEmpty()) disabled title="Add a global category first" @endif>
                        <i class="icon-circle-plus me-1"></i>Add Item
                    </button>
                    @endif
                </div>

                @if(!$selectedRestaurantId)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="icon-warehouse fs-48 text-muted mb-3"></i>
                        <p class="text-muted mb-0">Select a restaurant above to view and manage its items.</p>
                    </div>
                </div>
                @elseif($globalCategories->isEmpty())
                <div class="alert alert-info border-0">
                    No global categories yet.
                    <a href="{{ route('admin.categories.index') }}" class="alert-link">Add a category first</a>.
                </div>
                @else
                <div class="row">
                    @forelse($restaurantItems as $item)
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4 item-card"
                         data-item-id="{{ $item->id }}"
                         data-item-restaurant-id="{{ $item->restaurant_id }}"
                         data-item-name="{{ e($item->name) }}"
                         data-item-description="{{ e($item->description ?? '') }}"
                         data-item-price="{{ $item->price }}"
                         data-item-net-price="{{ $item->net_price ?? '' }}"
                         data-item-tax-id="{{ $item->tax_id ?? '' }}"
                         data-item-category-id="{{ $item->category_id }}"
                         data-item-food-type="{{ $item->food_type ?? 'veg' }}"
                         data-item-variations="{{ e(($item->variations ?? collect())->map(fn($v)=>['name'=>$v->name,'price'=>(float)$v->price])->values()->toJson()) }}"
                         data-item-addons="{{ e(($item->addons ?? collect())->map(fn($a)=>['addon_name'=>$a->addon_name,'price'=>(float)$a->price])->values()->toJson()) }}">
                        <div class="card h-100">
                            <div class="card-body">
                                @if($item->image)
                                    <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}"
                                         class="img-fluid w-100 rounded mb-2" style="height:130px;object-fit:cover;">
                                @else
                                    <div class="bg-light rounded w-100 d-flex align-items-center justify-content-center mb-2" style="height:130px;">
                                        <i class="icon-layout-list fs-36 text-muted"></i>
                                    </div>
                                @endif
                                <h6 class="fs-14 fw-semibold mb-1">
                                    {{ $item->name }}
                                    @if($item->is_master)
                                        <span class="badge badge-soft-primary fs-10 ms-1">Master</span>
                                    @endif
                                </h6>
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-medium">₹{{ number_format($item->price, 2) }}</span>
                                    <span class="small text-muted">{{ $item->category?->name ?? '–' }}</span>
                                </div>
                                <div class="d-flex gap-2 mt-2">
                                    @if($item->is_master)
                                        <a href="{{ route('admin.items.assign', $item) }}"
                                           class="btn btn-sm btn-outline-secondary flex-fill">
                                            <i class="icon-warehouse me-1"></i>Manage
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill edit-item-btn">
                                            <i class="icon-pencil-line me-1"></i>Edit
                                        </button>
                                        <form action="{{ route('admin.items.destroy', $item) }}" method="POST"
                                              class="d-inline flex-fill" onsubmit="return confirm('Delete this item?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="icon-trash-2 me-1"></i>Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="icon-layout-list fs-48 text-muted mb-3"></i>
                                <p class="text-muted mb-0">No items yet. Add a restaurant-specific item or assign master items from the <strong>Master Items</strong> tab.</p>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL: ADD MASTER ITEM
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="add_master_item_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 p-4 pb-0">
                <h4 class="modal-title">Add Master Item</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="is_master" value="1">
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 py-2 small mb-3">
                        <i class="icon-info me-1"></i>
                        This item will be available to assign to any restaurant. You can assign it after creation.
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Item Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ old('price') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Net Price</label>
                            <input type="number" name="net_price" class="form-control" step="0.01" min="0" value="{{ old('net_price') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">— Select category —</option>
                                @foreach($globalCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Food Type</label>
                            <select name="food_type" class="form-select">
                                <option value="veg">Veg</option>
                                <option value="non_veg">Non Veg</option>
                                <option value="egg">Egg</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <h6 class="mb-1">Variations <span class="text-muted fw-normal small">(optional)</span></h6>
                            <div id="master-add-variations">
                                <div class="row g-2 mb-2 var-row">
                                    <div class="col-5"><input type="text" name="variations[name][]" class="form-control form-control-sm" placeholder="Size"></div>
                                    <div class="col-4"><input type="number" name="variations[price][]" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price"></div>
                                    <div class="col-3"><button type="button" class="btn btn-sm btn-outline-primary btn-add-var">+ Add</button></div>
                                </div>
                            </div>
                        </div>
                        @if($plans->isNotEmpty())
                        <div class="col-12">
                            <label class="form-label">Assign to Plans <span class="text-muted fw-normal small">(optional)</span></label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($plans as $plan)
                                <label class="d-flex align-items-center gap-2 border rounded px-3 py-2 cursor-pointer plan-check-label">
                                    <input type="checkbox" name="plan_ids[]" value="{{ $plan->id }}" class="form-check-input mt-0">
                                    <span class="small fw-medium">{{ $plan->name }}</span>
                                </label>
                                @endforeach
                            </div>
                            <small class="text-muted">Item will be auto-assigned to all restaurants with an active subscription for the selected plan(s).</small>
                        </div>
                        @endif
                        <div class="col-12">
                            <div class="alert alert-info border-0 py-2 small mb-0">
                                <i class="icon-info me-1"></i>
                                Addons are managed per-restaurant. After creating this item, go to
                                <a href="{{ route('admin.addons.index') }}" class="alert-link">Admin → Addons</a> to add them.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create &amp; Assign Later</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL: ADD RESTAURANT ITEM
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="add_restaurant_item_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 p-4 pb-0">
                <h4 class="modal-title">Add Item</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="is_master" value="0">
                <input type="hidden" name="restaurant_id" value="{{ $selectedRestaurantId }}">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Item Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Net Price</label>
                            <input type="number" name="net_price" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($globalCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Food Type</label>
                            <select name="food_type" class="form-select">
                                <option value="veg">Veg</option>
                                <option value="non_veg">Non Veg</option>
                                <option value="egg">Egg</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     MODAL: EDIT ITEM (shared for master + restaurant)
══════════════════════════════════════════════════════ --}}
<div class="modal fade" id="edit_item_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 p-4 pb-0">
                <h4 class="modal-title">Edit Item</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="adm_edit_form" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Item Image</label>
                            <input type="file" name="image" id="adm_edit_image" class="form-control" accept="image/*">
                            <p class="small text-muted mt-1 mb-0">Leave empty to keep current</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="adm_edit_name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="adm_edit_desc" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="adm_edit_price" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Net Price</label>
                            <input type="number" name="net_price" id="adm_edit_net_price" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="adm_edit_cat" class="form-select" required>
                                <option value="">— Select category —</option>
                                @foreach($globalCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Food Type</label>
                            <select name="food_type" id="adm_edit_food_type" class="form-select">
                                <option value="veg">Veg</option>
                                <option value="non_veg">Non Veg</option>
                                <option value="egg">Egg</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <h6 class="mb-1">Variations</h6>
                            <div id="adm-edit-vars"></div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="adm-add-var-btn">+ Add</button>
                        </div>
                        <div class="col-12" id="adm-edit-addons-section">
                            <h6 class="mb-1">Add-Ons</h6>
                            <div id="adm-edit-addons"></div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="adm-add-addon-btn">+ Add</button>
                            <div id="adm-edit-addons-note" class="alert alert-info border-0 py-2 small mt-2 d-none">
                                <i class="icon-info me-1"></i>Addons for master items are managed via
                                <a href="{{ route('admin.addons.index') }}" class="alert-link">Admin → Addons</a>.
                            </div>
                        </div>
                        @if($plans->isNotEmpty())
                        <div class="col-12" id="adm-edit-plans-section">
                            <label class="form-label">Plans <span class="text-muted fw-normal small">(optional)</span></label>
                            <div class="d-flex flex-wrap gap-2" id="adm-edit-plans-checks">
                                @foreach($plans as $plan)
                                <label class="d-flex align-items-center gap-2 border rounded px-3 py-2 cursor-pointer plan-check-label">
                                    <input type="checkbox" name="plan_ids[]" value="{{ $plan->id }}"
                                           class="form-check-input mt-0 adm-edit-plan-check"
                                           data-plan-id="{{ $plan->id }}">
                                    <span class="small fw-medium">{{ $plan->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif
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
    // ── Add modal: dynamic rows ─────────────────────────────────
    document.addEventListener('click', function (e) {
        var t = e.target;
        if (t.classList.contains('btn-add-var') || t.classList.contains('btn-add-addon')) {
            var isVar  = t.classList.contains('btn-add-var');
            var wrap   = document.getElementById(isVar ? 'master-add-variations' : 'master-add-addons');
            var tmpl   = wrap.querySelector(isVar ? '.var-row' : '.addon-row');
            var clone  = tmpl.cloneNode(true);
            clone.querySelectorAll('input').forEach(function (i) { i.value = ''; });
            var btn = clone.querySelector('button');
            btn.textContent = '✕';
            btn.classList.remove('btn-add-var', 'btn-add-addon');
            btn.classList.add('btn-rm-row');
            wrap.appendChild(clone);
        }
        if (t.classList.contains('btn-rm-row')) {
            t.closest('.row').remove();
        }
    });

    // ── Edit modal ──────────────────────────────────────────────
    var editForm  = document.getElementById('adm_edit_form');
    var varWrap   = document.getElementById('adm-edit-vars');
    var addonWrap = document.getElementById('adm-edit-addons');

    function parseAttr(str) {
        if (!str) return [];
        try { return JSON.parse(str.replace(/&quot;/g, '"')); } catch (e) { return []; }
    }

    function makeRow(wrap, nameVal, priceVal, idx, nameKey, priceKey) {
        var d = document.createElement('div');
        d.className = 'row g-2 mb-2 adm-dyn-row';
        d.innerHTML =
            '<div class="col-4"><input type="text" name="' + nameKey + '[' + idx + '][name]" class="form-control form-control-sm" placeholder="Name" value="' + (nameVal||'').replace(/"/g,'&quot;') + '"></div>' +
            '<div class="col-3"><input type="number" name="' + priceKey + '[' + idx + '][price]" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price" value="' + (priceVal||'') + '"></div>' +
            '<div class="col-2"><button type="button" class="btn btn-sm btn-outline-danger adm-rm-row">✕</button></div>';
        wrap.appendChild(d);
    }

    function reindex() {
        varWrap.querySelectorAll('.adm-dyn-row').forEach(function (r, i) {
            r.querySelectorAll('input')[0].name = 'variations[' + i + '][name]';
            r.querySelectorAll('input')[1].name = 'variations[' + i + '][price]';
        });
        addonWrap.querySelectorAll('.adm-dyn-row').forEach(function (r, i) {
            r.querySelectorAll('input')[0].name = 'addons[' + i + '][name]';
            r.querySelectorAll('input')[1].name = 'addons[' + i + '][price]';
        });
    }

    document.addEventListener('click', function (e) {
        var btn = e.target && e.target.closest && e.target.closest('.edit-item-btn');
        if (!btn) return;
        e.preventDefault();
        var card = btn.closest('.item-card');
        if (!card) return;
        var id = card.getAttribute('data-item-id');
        var isMasterItem = !card.getAttribute('data-item-restaurant-id');
        editForm.action = '{{ url("admin/items") }}/' + id;
        document.getElementById('adm_edit_name').value       = card.getAttribute('data-item-name') || '';
        document.getElementById('adm_edit_desc').value       = card.getAttribute('data-item-description') || '';
        document.getElementById('adm_edit_price').value      = card.getAttribute('data-item-price') || '';
        document.getElementById('adm_edit_net_price').value  = card.getAttribute('data-item-net-price') || '';
        document.getElementById('adm_edit_food_type').value  = card.getAttribute('data-item-food-type') || 'veg';

        // Categories are global — no filtering needed
        var catSelect = document.getElementById('adm_edit_cat');
        catSelect.value = card.getAttribute('data-item-category-id') || '';
        document.getElementById('adm_edit_image').value      = '';
        varWrap.innerHTML = '';
        addonWrap.innerHTML = '';
        var vars = parseAttr(card.getAttribute('data-item-variations'));
        vars.forEach(function (v, i) { makeRow(varWrap, v.name, v.price, i, 'variations', 'variations'); });
        if (!vars.length) makeRow(varWrap, '', '', 0, 'variations', 'variations');
        var addonsSection = document.getElementById('adm-edit-addons-section');
        var addonsNote    = document.getElementById('adm-edit-addons-note');
        var addAddonBtn   = document.getElementById('adm-add-addon-btn');
        if (isMasterItem) {
            addonWrap.innerHTML = '';
            addonsNote.classList.remove('d-none');
            addAddonBtn.classList.add('d-none');
        } else {
            addonsNote.classList.add('d-none');
            addAddonBtn.classList.remove('d-none');
            var addons = parseAttr(card.getAttribute('data-item-addons'));
            addons.forEach(function (a, i) { makeRow(addonWrap, a.addon_name||a.name, a.price, i, 'addons', 'addons'); });
            if (!addons.length) makeRow(addonWrap, '', '', 0, 'addons', 'addons');
        }
        // Populate plan checkboxes for master items
        var plansSection = document.getElementById('adm-edit-plans-section');
        if (plansSection) {
            var itemPlanIds = [];
            try { itemPlanIds = JSON.parse(card.getAttribute('data-item-plan-ids') || '[]'); } catch(e){}
            plansSection.style.display = isMasterItem ? '' : 'none';
            plansSection.querySelectorAll('.adm-edit-plan-check').forEach(function(chk) {
                chk.checked = itemPlanIds.indexOf(parseInt(chk.getAttribute('data-plan-id'))) !== -1;
            });
        }

        bootstrap.Modal.getOrCreateInstance(document.getElementById('edit_item_modal')).show();
    });

    [varWrap, addonWrap].forEach(function (w) {
        w && w.addEventListener('click', function (e) {
            if (e.target.classList.contains('adm-rm-row')) { e.target.closest('.row').remove(); reindex(); }
        });
    });

    var addVarBtn   = document.getElementById('adm-add-var-btn');
    var addAddonBtn = document.getElementById('adm-add-addon-btn');
    addVarBtn   && addVarBtn.addEventListener('click', function () {
        makeRow(varWrap, '', '', varWrap.querySelectorAll('.adm-dyn-row').length, 'variations', 'variations');
    });
    addAddonBtn && addAddonBtn.addEventListener('click', function () {
        makeRow(addonWrap, '', '', addonWrap.querySelectorAll('.adm-dyn-row').length, 'addons', 'addons');
    });

    editForm && editForm.addEventListener('submit', reindex);
})();
</script>
@endsection
