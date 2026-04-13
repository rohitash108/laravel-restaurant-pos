<?php $page = 'inventory'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="mb-4">
            <a href="{{ route('inventory.index') }}" class="text-muted small">← Inventory</a>
            <h3 class="mb-0 mt-2">Wastage</h3>
        </div>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('inventory.waste.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Ingredient *</label>
                        <select name="ingredient_id" class="form-select" required>
                            @foreach($ingredients as $ing)
                                <option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->unit }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Quantity wasted *</label>
                        <input type="number" step="0.000001" name="quantity" class="form-control" required min="0.000001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Record</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
