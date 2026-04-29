<?php $page = 'items'; ?>
@extends('layout.mainlayout')
@section('content')

<div class="page-wrapper">
    <div class="content">

        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <h3 class="mb-0">Items</h3>
                <p class="text-muted small mb-0">Manage your restaurant's items</p>
            </div>
            <div class="gap-2 d-flex align-items-center flex-wrap">
                <div class="input-group input-group-flat w-auto">
                    <input type="text" class="form-control" placeholder="Search items…" id="items-search">
                    <span class="input-group-text"><i class="icon-search text-dark"></i></span>
                </div>
                @if(($categories ?? collect())->isNotEmpty())
                <button type="button" class="btn btn-primary d-inline-flex align-items-center"
                        data-bs-toggle="modal" data-bs-target="#add_item_modal">
                    <i class="icon-circle-plus me-1"></i>Add Item
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
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible border-0 mb-4">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row" id="items-row">
            @forelse($items ?? [] as $item)
            @php
                $isMaster   = $item->is_master;
                $available  = $isMaster
                    ? ($item->assignments->first()?->is_available ?? true)
                    : $item->is_available;
                $vars       = ($item->variations ?? collect())->map(fn($v) => ['name' => $v->name, 'price' => (float)$v->price])->values();
                $addons     = ($item->addons ?? collect())->map(fn($a) => ['name' => $a->addon_name, 'price' => (float)$a->price])->values();
            @endphp
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4 item-card"
                 data-item-name="{{ e($item->name) }}"
                 data-item-id="{{ $item->id }}"
                 data-item-is-master="{{ $isMaster ? '1' : '0' }}"
                 data-item-name-val="{{ e($item->name) }}"
                 data-item-description="{{ e($item->description ?? '') }}"
                 data-item-price="{{ $item->price }}"
                 data-item-net-price="{{ $item->net_price ?? '' }}"
                 data-item-category-id="{{ $item->category_id }}"
                 data-item-food-type="{{ $item->food_type ?? 'veg' }}"
                 data-item-tax-id="{{ $item->tax_id ?? '' }}"
                 data-item-variations="{{ e($vars->toJson()) }}"
                 data-item-addons="{{ e($addons->toJson()) }}">
                <div class="card h-100 {{ !$available ? 'border-warning' : '' }}">
                    <div class="card-body d-flex flex-column">
                        <div class="position-relative mb-2">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                     class="img-fluid w-100 rounded" style="height:140px;object-fit:cover;">
                            @else
                                <div class="bg-light rounded w-100 d-flex align-items-center justify-content-center" style="height:140px;">
                                    <i class="icon-layout-list fs-48 text-muted"></i>
                                </div>
                            @endif

                            @if(!$available)
                                <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">Hidden</span>
                            @endif
                            @if($isMaster)
                                <span class="badge badge-soft-primary position-absolute top-0 start-0 m-2">Master</span>
                            @endif
                        </div>

                        <h6 class="fs-14 fw-semibold mb-1">{{ $item->name }}</h6>
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-medium">{{ $currency_symbol ?? '₹' }}{{ number_format($item->price, 2) }}</span>
                            <span class="small">
                                @if(($item->food_type ?? 'veg') === 'non_veg')
                                    <i class="icon-square-dot text-danger me-1"></i>Non Veg
                                @elseif(($item->food_type ?? '') === 'egg')
                                    <i class="icon-square-dot text-warning me-1"></i>Egg
                                @else
                                    <i class="icon-square-dot text-success me-1"></i>Veg
                                @endif
                            </span>
                        </div>
                        <p class="mb-1 small text-muted">{{ $item->category?->name ?? '–' }}</p>

                        @if($vars->isNotEmpty())
                            <p class="mb-0 small text-muted"><i class="icon-layers me-1"></i>{{ $vars->count() }} variation(s)</p>
                        @endif
                        @if($addons->isNotEmpty())
                            <p class="mb-0 small text-muted"><i class="icon-text-select me-1"></i>{{ $addons->count() }} addon(s)</p>
                        @endif

                        {{-- Actions --}}
                        <div class="d-flex gap-2 mt-auto pt-2">
                            {{-- Availability toggle --}}
                            <form action="{{ route('items.hide', $item) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $available ? 'btn-outline-secondary' : 'btn-warning' }}" title="{{ $available ? 'Hide item' : 'Show item' }}">
                                    <i class="icon-{{ $available ? 'eye-off' : 'eye' }}"></i>
                                </button>
                            </form>

                            @if(!$isMaster)
                                <button type="button" class="btn btn-sm btn-outline-secondary flex-fill edit-item-btn" title="Edit">
                                    <i class="icon-pencil-line me-1"></i>Edit
                                </button>
                                <form action="{{ route('items.destroy', $item) }}" method="POST"
                                      class="d-inline" onsubmit="return confirm('Delete this item?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="icon-trash-2"></i>
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
                        <i class="icon-layout-list fs-48 text-muted mb-3 d-block"></i>
                        <h5 class="mb-2">No items yet</h5>
                        <p class="text-muted mb-0">Add your own items or ask Super Admin to assign master items to your restaurant.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════
     MODAL: ADD ITEM
═══════════════════════════════════════ --}}
<div class="modal fade" id="add_item_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 p-4 pb-0">
                <h4 class="modal-title">Add Item</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
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
                                @foreach($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                        @if(($taxes ?? collect())->isNotEmpty())
                        <div class="col-md-6">
                            <label class="form-label">Tax</label>
                            <select name="tax_id" class="form-select">
                                <option value="">— No tax —</option>
                                @foreach($taxes as $tax)
                                    <option value="{{ $tax->id }}">{{ $tax->name }} ({{ $tax->rate }}%)</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-12">
                            <h6 class="mb-1">Variations <span class="text-muted fw-normal small">(optional)</span></h6>
                            <div id="add-variations">
                                <div class="row g-2 mb-2 var-row">
                                    <div class="col-5"><input type="text" name="variations[name][]" class="form-control form-control-sm" placeholder="e.g. Small"></div>
                                    <div class="col-4"><input type="number" name="variations[price][]" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price"></div>
                                    <div class="col-3"><button type="button" class="btn btn-sm btn-outline-primary btn-add-var">+ Add</button></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <h6 class="mb-1">Add-Ons <span class="text-muted fw-normal small">(optional)</span></h6>
                            <div id="add-addons">
                                <div class="row g-2 mb-2 addon-row">
                                    <div class="col-5"><input type="text" name="addons[name][]" class="form-control form-control-sm" placeholder="e.g. Extra Cheese"></div>
                                    <div class="col-4"><input type="number" name="addons[price][]" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price"></div>
                                    <div class="col-3"><button type="button" class="btn btn-sm btn-outline-primary btn-add-addon">+ Add</button></div>
                                </div>
                            </div>
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

{{-- ═══════════════════════════════════════
     MODAL: EDIT ITEM
═══════════════════════════════════════ --}}
<div class="modal fade" id="edit_item_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 p-4 pb-0">
                <h4 class="modal-title">Edit Item</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="edit_item_form" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Item Image</label>
                            <input type="file" name="image" id="edit_image" class="form-control" accept="image/*">
                            <p class="small text-muted mt-1 mb-0">Leave empty to keep current image</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_desc" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Price <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="edit_price" class="form-control" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Net Price</label>
                            <input type="number" name="net_price" id="edit_net_price" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="edit_cat" class="form-select" required>
                                <option value="">— Select category —</option>
                                @foreach($categories ?? [] as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Food Type</label>
                            <select name="food_type" id="edit_food_type" class="form-select">
                                <option value="veg">Veg</option>
                                <option value="non_veg">Non Veg</option>
                                <option value="egg">Egg</option>
                            </select>
                        </div>
                        @if(($taxes ?? collect())->isNotEmpty())
                        <div class="col-md-6">
                            <label class="form-label">Tax</label>
                            <select name="tax_id" id="edit_tax" class="form-select">
                                <option value="">— No tax —</option>
                                @foreach($taxes as $tax)
                                    <option value="{{ $tax->id }}">{{ $tax->name }} ({{ $tax->rate }}%)</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-12">
                            <h6 class="mb-1">Variations</h6>
                            <div id="edit-vars"></div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="edit-add-var-btn">+ Add</button>
                        </div>
                        <div class="col-12">
                            <h6 class="mb-1">Add-Ons</h6>
                            <div id="edit-addons"></div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-1" id="edit-add-addon-btn">+ Add</button>
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
    // ── Add modal: dynamic rows ──────────────────────────────────
    document.addEventListener('click', function (e) {
        var t = e.target;
        if (t.classList.contains('btn-add-var') || t.classList.contains('btn-add-addon')) {
            var isVar = t.classList.contains('btn-add-var');
            var wrap  = document.getElementById(isVar ? 'add-variations' : 'add-addons');
            var tmpl  = wrap.querySelector(isVar ? '.var-row' : '.addon-row');
            var clone = tmpl.cloneNode(true);
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

    // ── Edit modal ───────────────────────────────────────────────
    var editForm  = document.getElementById('edit_item_form');
    var varWrap   = document.getElementById('edit-vars');
    var addonWrap = document.getElementById('edit-addons');

    function parseAttr(str) {
        if (!str) return [];
        try { return JSON.parse(str.replace(/&quot;/g, '"')); } catch (e) { return []; }
    }

    function makeDynRow(wrap, nameVal, priceVal, idx, nameKey, priceKey) {
        var d = document.createElement('div');
        d.className = 'row g-2 mb-2 dyn-row';
        d.innerHTML =
            '<div class="col-5"><input type="text" name="' + nameKey + '[' + idx + '][name]" class="form-control form-control-sm" placeholder="Name" value="' + (nameVal||'').replace(/"/g,'&quot;') + '"></div>' +
            '<div class="col-4"><input type="number" name="' + priceKey + '[' + idx + '][price]" class="form-control form-control-sm" step="0.01" min="0" placeholder="Price" value="' + (priceVal||'') + '"></div>' +
            '<div class="col-3"><button type="button" class="btn btn-sm btn-outline-danger dyn-rm">✕</button></div>';
        wrap.appendChild(d);
    }

    function reindex() {
        varWrap.querySelectorAll('.dyn-row').forEach(function (r, i) {
            r.querySelectorAll('input')[0].name = 'variations[' + i + '][name]';
            r.querySelectorAll('input')[1].name = 'variations[' + i + '][price]';
        });
        addonWrap.querySelectorAll('.dyn-row').forEach(function (r, i) {
            r.querySelectorAll('input')[0].name = 'addons[' + i + '][name]';
            r.querySelectorAll('input')[1].name = 'addons[' + i + '][price]';
        });
    }

    [varWrap, addonWrap].forEach(function (w) {
        w && w.addEventListener('click', function (e) {
            if (e.target.classList.contains('dyn-rm')) {
                e.target.closest('.dyn-row').remove();
                reindex();
            }
        });
    });

    document.getElementById('edit-add-var-btn') && document.getElementById('edit-add-var-btn').addEventListener('click', function () {
        makeDynRow(varWrap, '', '', varWrap.querySelectorAll('.dyn-row').length, 'variations', 'variations');
    });
    document.getElementById('edit-add-addon-btn') && document.getElementById('edit-add-addon-btn').addEventListener('click', function () {
        makeDynRow(addonWrap, '', '', addonWrap.querySelectorAll('.dyn-row').length, 'addons', 'addons');
    });

    // Open edit modal from button
    document.addEventListener('click', function (e) {
        var btn = e.target && e.target.closest && e.target.closest('.edit-item-btn');
        if (!btn) return;
        e.preventDefault();
        var card = btn.closest('.item-card');
        if (!card) return;

        var id = card.getAttribute('data-item-id');
        editForm.action = '{{ url("items") }}/' + id;

        document.getElementById('edit_name').value      = card.getAttribute('data-item-name-val') || '';
        document.getElementById('edit_desc').value      = card.getAttribute('data-item-description') || '';
        document.getElementById('edit_price').value     = card.getAttribute('data-item-price') || '';
        document.getElementById('edit_net_price').value = card.getAttribute('data-item-net-price') || '';
        document.getElementById('edit_food_type').value = card.getAttribute('data-item-food-type') || 'veg';
        document.getElementById('edit_cat').value       = card.getAttribute('data-item-category-id') || '';
        document.getElementById('edit_image').value     = '';

        var taxEl = document.getElementById('edit_tax');
        if (taxEl) taxEl.value = card.getAttribute('data-item-tax-id') || '';

        varWrap.innerHTML = '';
        addonWrap.innerHTML = '';

        var vars = parseAttr(card.getAttribute('data-item-variations'));
        vars.forEach(function (v, i) { makeDynRow(varWrap, v.name, v.price, i, 'variations', 'variations'); });
        if (!vars.length) makeDynRow(varWrap, '', '', 0, 'variations', 'variations');

        var addons = parseAttr(card.getAttribute('data-item-addons'));
        addons.forEach(function (a, i) { makeDynRow(addonWrap, a.name || a.addon_name, a.price, i, 'addons', 'addons'); });
        if (!addons.length) makeDynRow(addonWrap, '', '', 0, 'addons', 'addons');

        bootstrap.Modal.getOrCreateInstance(document.getElementById('edit_item_modal')).show();
    });

    editForm && editForm.addEventListener('submit', reindex);

    // ── Search ───────────────────────────────────────────────────
    var searchEl = document.getElementById('items-search');
    searchEl && searchEl.addEventListener('input', function () {
        var q = (this.value || '').trim().toLowerCase();
        document.querySelectorAll('#items-row .item-card').forEach(function (card) {
            var name = (card.getAttribute('data-item-name') || '').toLowerCase();
            card.style.display = !q || name.indexOf(q) !== -1 ? '' : 'none';
        });
    });
})();
</script>
@endsection
