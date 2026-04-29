@php $page = 'admin-subscription-plans'; @endphp
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content container-fluid">

        {{-- Header --}}
        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.subscription-plans') }}" class="text-muted">Plans</a></li>
                        <li class="breadcrumb-item active">{{ $plan->name }} — Items</li>
                    </ol>
                </nav>
                <h3 class="mb-0 fw-bold">{{ $plan->name }}</h3>
                <p class="text-muted mb-0 small">
                    {{ $plan->duration_label }} &middot; ₹{{ number_format($plan->price, 2) }}
                    &middot; <span class="badge {{ $plan->is_active ? 'badge-soft-success' : 'badge-soft-danger' }}">{{ $plan->is_active ? 'Active' : 'Inactive' }}</span>
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.subscription-plans') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="icon-arrow-left me-1"></i>Back to Plans
                </a>
                <form action="{{ route('admin.plan-items.force-sync', $plan) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Push all plan items to every restaurant with an active subscription for this plan?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning btn-sm">
                        <i class="icon-refresh-cw me-1"></i>Force Sync to All Restaurants
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible border-0 mb-4">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="row g-4">
            {{-- Item selection form --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-transparent border-bottom d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-semibold">Select Items for This Plan</h5>
                        <span class="badge bg-primary rounded-pill" id="selected-count">0 selected</span>
                    </div>
                    <form action="{{ route('admin.plan-items.sync', $plan) }}" method="POST">
                        @csrf
                        <div class="card-body p-0">
                            @php $planItemIds = $plan->items->pluck('id')->toArray(); @endphp

                            {{-- Group by category --}}
                            @php
                                $grouped = $allMasterItems->groupBy(fn($item) => $item->category?->name ?? 'Uncategorised');
                            @endphp

                            @forelse($grouped as $catName => $items)
                            <div class="border-bottom">
                                <div class="px-4 py-2 bg-light d-flex align-items-center justify-content-between">
                                    <small class="fw-semibold text-uppercase text-muted ls-1">{{ $catName }}</small>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-muted select-cat-btn" data-cat="{{ $loop->index }}">
                                        Select all
                                    </button>
                                </div>
                                @foreach($items as $item)
                                <label class="d-flex align-items-center gap-3 px-4 py-2 item-row border-bottom border-light cursor-pointer hover-bg"
                                       data-cat="{{ $loop->parent->index }}">
                                    <input type="checkbox" name="item_ids[]" value="{{ $item->id }}"
                                           class="form-check-input item-check mt-0 flex-shrink-0"
                                           {{ in_array($item->id, $planItemIds) ? 'checked' : '' }}>
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-medium text-truncate">{{ $item->name }}</div>
                                        @if($item->description)
                                        <div class="text-muted small text-truncate">{{ $item->description }}</div>
                                        @endif
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        <div class="fw-semibold">₹{{ number_format($item->price, 2) }}</div>
                                        @if($item->food_type === 'veg')
                                            <span class="badge badge-soft-success" style="font-size:10px;">Veg</span>
                                        @elseif($item->food_type === 'non_veg')
                                            <span class="badge badge-soft-danger" style="font-size:10px;">Non-Veg</span>
                                        @endif
                                    </div>
                                </label>
                                @endforeach
                            </div>
                            @empty
                            <div class="text-center py-5 text-muted">
                                No master items found. <a href="{{ route('admin.items.index') }}">Create items first.</a>
                            </div>
                            @endforelse
                        </div>

                        @if($allMasterItems->isNotEmpty())
                        <div class="card-footer bg-transparent border-top p-3 d-flex justify-content-between align-items-center">
                            <div>
                                <label class="d-flex align-items-center gap-2 mb-0 cursor-pointer">
                                    <input type="checkbox" id="select-all" class="form-check-input mt-0">
                                    <span class="text-muted small">Select / deselect all</span>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-save me-1"></i>Save Plan Items
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Summary sidebar --}}
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header bg-transparent border-bottom">
                        <h6 class="mb-0 fw-semibold">Currently in Plan
                            <span class="badge bg-secondary rounded-pill ms-1">{{ $plan->items->count() }}</span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        @forelse($plan->items->sortBy('name') as $item)
                        <div class="d-flex align-items-center gap-2 px-3 py-2 border-bottom border-light">
                            <i class="icon-check-circle text-success flex-shrink-0" style="font-size:14px;"></i>
                            <span class="small fw-medium text-truncate">{{ $item->name }}</span>
                            <span class="ms-auto small text-muted flex-shrink-0">₹{{ number_format($item->price, 2) }}</span>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted small">No items in this plan yet.</div>
                        @endforelse
                    </div>
                </div>

                <div class="card border-info">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-2"><i class="icon-info me-1 text-info"></i>How it works</h6>
                        <ul class="small text-muted mb-0 ps-3">
                            <li class="mb-1">Checked items are bundled with this plan.</li>
                            <li class="mb-1">When a restaurant subscribes to this plan, all checked items are <strong>auto-assigned</strong> to it.</li>
                            <li class="mb-1">Adding an item here pushes it immediately to all restaurants with an active subscription.</li>
                            <li>Removing an item removes plan-sourced assignments but keeps manual ones.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.hover-bg:hover { background: #f8f9fa; }
.cursor-pointer { cursor: pointer; }
.ls-1 { letter-spacing: 0.05em; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const checks  = () => document.querySelectorAll('.item-check');
    const counter = document.getElementById('selected-count');

    function updateCount() {
        const n = [...checks()].filter(c => c.checked).length;
        counter.textContent = n + ' selected';
    }

    document.querySelectorAll('.item-check').forEach(c => {
        c.addEventListener('change', updateCount);
    });

    document.getElementById('select-all').addEventListener('change', function () {
        checks().forEach(c => c.checked = this.checked);
        updateCount();
    });

    document.querySelectorAll('.select-cat-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const cat = this.dataset.cat;
            const rows = document.querySelectorAll(`.item-row[data-cat="${cat}"] .item-check`);
            const allChecked = [...rows].every(c => c.checked);
            rows.forEach(c => c.checked = !allChecked);
            this.textContent = allChecked ? 'Select all' : 'Deselect all';
            updateCount();
        });
    });

    updateCount();
});
</script>
@endsection
