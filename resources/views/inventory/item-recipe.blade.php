<?php $page = 'items'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="mb-4">
            <a href="{{ route('items') }}" class="text-muted small">← Items</a>
            <h3 class="mb-0 mt-2">Recipe: {{ $item->name }}</h3>
            <p class="text-muted small mb-0">Ingredient quantities consumed <strong>per 1</strong> unit sold (same unit as ingredient).</p>
        </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($ingredients->isEmpty())
            <div class="alert alert-warning">Create at least one ingredient under <a href="{{ route('inventory.ingredients.create') }}">Inventory → Ingredients</a> before mapping a recipe.</div>
        @else
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('inventory.item.recipe.update', $item) }}" id="recipe-form">
                    @csrf
                    @method('PUT')
                    <div id="recipe-lines">
                        @php $lines = old('lines', $item->recipeIngredients->map(fn ($r) => ['ingredient_id' => $r->ingredient_id, 'quantity' => $r->quantity])->toArray()); @endphp
                        @forelse($lines as $i => $line)
                            <div class="row g-2 mb-2 recipe-line">
                                <div class="col-md-6">
                                    <select name="lines[{{ $i }}][ingredient_id]" class="form-select" required>
                                        @foreach($ingredients as $ing)
                                            <option value="{{ $ing->id }}" @selected(($line['ingredient_id'] ?? '') == $ing->id)>{{ $ing->name }} ({{ $ing->unit }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" step="0.000001" name="lines[{{ $i }}][quantity]" class="form-control" value="{{ $line['quantity'] ?? '' }}" placeholder="Qty" required min="0.000001">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-white w-100 remove-line">Remove</button>
                                </div>
                            </div>
                        @empty
                            <div class="row g-2 mb-2 recipe-line">
                                <div class="col-md-6">
                                    <select name="lines[0][ingredient_id]" class="form-select" required>
                                        @foreach($ingredients as $ing)
                                            <option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->unit }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" step="0.000001" name="lines[0][quantity]" class="form-control" placeholder="Qty per 1 item" required min="0.000001">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-white w-100 remove-line">Remove</button>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" class="btn btn-white mb-3" id="add-recipe-line">Add line</button>
                    <div>
                        <button type="submit" class="btn btn-primary">Save recipe</button>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
<template id="recipe-line-template">
    <div class="row g-2 mb-2 recipe-line">
        <div class="col-md-6">
            <select name="lines[__I__][ingredient_id]" class="form-select" required>
                @foreach($ingredients as $ing)
                    <option value="{{ $ing->id }}">{{ $ing->name }} ({{ $ing->unit }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" step="0.000001" name="lines[__I__][quantity]" class="form-control" placeholder="Qty" required min="0.000001">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-white w-100 remove-line">Remove</button>
        </div>
    </div>
</template>
<script>
(function() {
    var wrap = document.getElementById('recipe-lines');
    var tpl = document.getElementById('recipe-line-template');
    var addBtn = document.getElementById('add-recipe-line');
    var idx = wrap ? wrap.querySelectorAll('.recipe-line').length : 0;
    if (addBtn && tpl && wrap) {
        addBtn.addEventListener('click', function() {
            var html = tpl.innerHTML.replace(/__I__/g, String(idx++));
            var div = document.createElement('div');
            div.innerHTML = html.trim();
            wrap.appendChild(div.firstElementChild);
        });
    }
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-line')) {
            var line = e.target.closest('.recipe-line');
            if (line && wrap && wrap.querySelectorAll('.recipe-line').length > 1) line.remove();
        }
    });
})();
</script>
@endsection
