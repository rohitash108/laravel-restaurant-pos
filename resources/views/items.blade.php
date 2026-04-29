<?php $page = 'items'; ?>
@extends('layout.mainlayout')
@section('content')

<div class="page-wrapper">
    <div class="content">

        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <h3 class="mb-0">Items</h3>
                <p class="text-muted small mb-0">Items are managed by Super Admin and assigned to your restaurant.</p>
            </div>
            <div class="gap-2 d-flex align-items-center flex-wrap">
                <div class="input-group input-group-flat w-auto">
                    <input type="text" class="form-control" placeholder="Search items…" id="items-search">
                    <span class="input-group-text"><i class="icon-search text-dark"></i></span>
                </div>
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

        <div class="row" id="items-row">
            @forelse($items ?? [] as $item)
            <div class="col-lg-3 col-md-4 col-sm-6 item-card"
                 data-item-name="{{ e($item->name) }}">
                <div class="card {{ $item->is_available ? '' : 'border-warning' }}">
                    <div class="card-body">
                        <div class="food-items position-relative">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}"
                                     class="img-fluid mb-3 w-100 rounded" style="height:140px;object-fit:cover;">
                            @else
                                <div class="bg-light rounded mb-3 w-100 d-flex align-items-center justify-content-center" style="height:140px;">
                                    <i class="icon-layout-list fs-48 text-muted"></i>
                                </div>
                            @endif

                            @if(!$item->is_available)
                                <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">Hidden</span>
                            @endif
                            @if($item->is_master)
                                <span class="badge badge-soft-primary position-absolute top-0 start-0 m-2">Master</span>
                            @endif
                        </div>

                        <h6 class="fs-14 fw-semibold mb-1">{{ $item->name }}</h6>
                        <div class="d-flex align-items-center justify-content-between">
                            <p class="mb-0">{{ $currency_symbol }}{{ number_format($item->price, 2) }}</p>
                            <span class="d-flex align-items-center small">
                                @if(($item->food_type ?? 'veg') === 'non_veg')
                                    <i class="icon-square-dot text-danger me-1"></i>Non Veg
                                @elseif(($item->food_type ?? '') === 'egg')
                                    <i class="icon-square-dot text-warning me-1"></i>Egg
                                @else
                                    <i class="icon-square-dot text-success me-1"></i>Veg
                                @endif
                            </span>
                        </div>
                        <p class="mb-0 mt-1 small text-muted">{{ $item->category?->name ?? '–' }}</p>

                        @if(($item->variations ?? collect())->isNotEmpty())
                            <p class="mb-0 mt-1 small text-muted">
                                <i class="icon-layers me-1"></i>
                                {{ $item->variations->count() }} variation(s)
                            </p>
                        @endif
                        @if(($item->addons ?? collect())->isNotEmpty())
                            <p class="mb-0 mt-1 small text-muted">
                                <i class="icon-text-select me-1"></i>
                                {{ $item->addons->count() }} addon(s)
                            </p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="icon-layout-list fs-48 text-muted mb-3 d-block"></i>
                        <h5 class="mb-2">No items assigned yet</h5>
                        <p class="text-muted mb-0">Contact Super Admin to assign items to your restaurant.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

    </div>
</div>

<script>
document.getElementById("items-search") && document.getElementById("items-search").addEventListener("input", function () {
    var q = (this.value || "").trim().toLowerCase();
    document.querySelectorAll("#items-row .item-card").forEach(function (card) {
        var name = (card.getAttribute("data-item-name") || "").toLowerCase();
        card.style.display = !q || name.indexOf(q) !== -1 ? "" : "none";
    });
});
</script>
@endsection
