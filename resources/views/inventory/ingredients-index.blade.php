<?php $page = 'inventory'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Ingredients</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('inventory.index') }}" class="btn btn-white">Dashboard</a>
                <a href="{{ route('inventory.ingredients.create') }}" class="btn btn-primary">Add</a>
            </div>
        </div>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Name</th><th>SKU</th><th>Unit</th><th>Low stock</th><th>Expiry</th><th></th></tr></thead>
                        <tbody>
                            @foreach($ingredients as $ing)
                                <tr>
                                    <td>{{ $ing->name }}</td>
                                    <td>{{ $ing->sku ?? '–' }}</td>
                                    <td>{{ $ing->unit }}</td>
                                    <td>{{ $ing->low_stock_threshold }}</td>
                                    <td>{{ $ing->track_expiry ? 'Yes' : 'No' }}</td>
                                    <td><a href="{{ route('inventory.ingredients.edit', $ing) }}" class="btn btn-sm btn-white">Edit</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3">{{ $ingredients->links() }}</div>
    </div>
</div>
@endsection
