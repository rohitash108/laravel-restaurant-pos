<?php $page = 'items'; ?>
@extends('layout.mainlayout')
@section('content')

    <div class="page-wrapper">
        <div class="content">

            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1">
                    <h3 class="mb-0">Items</h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="input-group input-group-flat w-auto">
                        <input type="text" class="form-control" placeholder="Search" id="items-search">
                        <span class="input-group-text">
                            <i class="icon-search text-dark"></i>
                        </span>
                    </div>
                    <a href="#" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_item"><i class="icon-circle-plus me-1"></i>Add New</a>
                </div>
            </div>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible border-0 mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible border-0 mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible border-0 mb-4" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(($categories ?? collect())->isEmpty())
                <div class="alert alert-info border-0 mb-4" role="alert">
                    No categories yet. <a href="{{ route('categories') }}" class="alert-link">Add a category</a> first, then you can add items here.
                </div>
            @endif

            <div class="row" id="items-row">
                @forelse($items ?? [] as $item)
                <div class="col-lg-3 col-md-4 col-sm-6 item-card" data-item-id="{{ $item->id }}" data-item-name="{{ e($item->name) }}" data-item-description="{{ e($item->description ?? '') }}" data-item-image="{{ $item->image ? asset('storage/' . $item->image) : '' }}" data-item-price="{{ $item->price }}" data-item-net-price="{{ $item->net_price ?? '' }}" data-item-tax-id="{{ $item->tax_id ?? '' }}" data-item-category-id="{{ $item->category_id }}" data-item-food-type="{{ $item->food_type ?? 'veg' }}" data-item-variations="{{ e(($item->variations ?? collect())->map(fn($v) => ['name' => $v->name, 'price' => (float) $v->price])->values()->toJson()) }}" data-item-addons="{{ e(($item->addons ?? collect())->map(fn($a) => ['addon_name' => $a->addon_name, 'price' => (float) $a->price])->values()->toJson()) }}">
                    <div class="card {{ $item->is_available ? '' : 'border-warning' }}">
                        <div class="card-body">
                            <div class="food-items position-relative">
                                <a href="#" class="item-details-trigger" data-bs-toggle="modal" data-bs-target="#items_details">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="img-fluid mb-3 w-100 rounded">
                                    @else
                                        <div class="bg-light rounded mb-3 w-100 d-flex align-items-center justify-content-center" style="height: 140px;">
                                            <i class="icon-layout-list fs-48 text-muted"></i>
                                        </div>
                                    @endif
                                </a>
                                @if(!$item->is_available)
                                    <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">Hidden</span>
                                @endif
                                <a class="food-items-menu" href="#" data-bs-toggle="dropdown">
                                    <i class="icon-ellipsis-vertical"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-split">
                                    <li><a href="{{ route('inventory.item.recipe', $item) }}" class="dropdown-item"><i class="icon-layers me-2"></i>Stock recipe</a></li>
                                    <li><a href="#" class="dropdown-item edit-item-btn"><i class="icon-pencil-line me-2"></i>Edit Item</a></li>
                                    <li><a href="#" class="dropdown-item hide-item-btn" data-bs-toggle="modal" data-bs-target="#hide_item" data-id="{{ $item->id }}"><i class="icon-eye-off me-2"></i>Hide Item</a></li>
                                    <li>
                                        <form action="{{ route('items.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="icon-trash-2 me-2"></i>Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            <h6 class="fs-14 fw-semibold"><a href="#" class="item-details-trigger" data-bs-toggle="modal" data-bs-target="#items_details">{{ $item->name }}</a></h6>
                            <div class="d-flex align-items-center justify-content-between">
                                <p class="mb-0">{{ $item->restaurant->currency ?? '$' }}{{ number_format($item->price, 2) }}</p>
                                <div>
                                    <span class="d-flex align-items-center">
                                        @if(($item->food_type ?? 'veg') === 'non_veg')
                                            <i class="icon-square-dot text-danger me-1"></i>Non Veg
                                        @elseif(($item->food_type ?? '') === 'egg')
                                            <i class="icon-square-dot text-warning me-1"></i>Egg
                                        @else
                                            <i class="icon-square-dot text-success me-1"></i>Veg
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <p class="mb-0 mt-1 small text-muted">{{ $item->category?->name ?? '–' }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="icon-layout-list fs-48 text-muted mb-3"></i>
                            <p class="text-muted mb-0">No items yet. Add items from the menu.</p>
                        </div>
                    </div>
                </div>
                @endforelse
            </div>

        </div>
    </div>

    <script>
    document.getElementById("items-search") && document.getElementById("items-search").addEventListener("input", function() {
        var q = (this.value || "").trim().toLowerCase();
        document.querySelectorAll("#items-row .item-card").forEach(function(card) {
            var name = (card.getAttribute("data-item-name") || "").toLowerCase();
            card.style.display = !q || name.indexOf(q) !== -1 ? "" : "none";
        });
    });
    </script>
@endsection
