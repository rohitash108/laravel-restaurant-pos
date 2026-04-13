<?php $page = 'inventory'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="mb-4">
            <a href="{{ route('inventory.ingredients.index') }}" class="text-muted small">← Ingredients</a>
            <h3 class="mb-0 mt-2">{{ $ingredient ? 'Edit ingredient' : 'New ingredient' }}</h3>
        </div>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $ingredient ? route('inventory.ingredients.update', $ingredient) : route('inventory.ingredients.store') }}">
                    @csrf
                    @if($ingredient)
                        @method('PUT')
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $ingredient->name ?? '') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $ingredient->sku ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Unit * <span class="text-muted small">(kg, g, l, ml, pcs…)</span></label>
                        <input type="text" name="unit" class="form-control" value="{{ old('unit', $ingredient->unit ?? 'pcs') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Low stock threshold</label>
                            <input type="number" step="0.000001" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', $ingredient->low_stock_threshold ?? 0) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reorder point</label>
                            <input type="number" step="0.000001" name="reorder_point" class="form-control" value="{{ old('reorder_point', $ingredient->reorder_point ?? '') }}">
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="track_expiry" value="1" class="form-check-input" id="track_expiry" @checked(old('track_expiry', $ingredient->track_expiry ?? false))>
                        <label class="form-check-label" for="track_expiry">Track expiry on batches</label>
                    </div>
                    @if($ingredient)
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $ingredient->is_active ?? true))>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $ingredient->notes ?? '') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
