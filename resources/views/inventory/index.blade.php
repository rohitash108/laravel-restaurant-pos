<?php $page = 'inventory'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <h3 class="mb-0">Inventory</h3>
                <p class="text-muted small mb-0">Branch: <strong>{{ $branch->name ?? 'Main' }}</strong></p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('inventory.ingredients.create') }}" class="btn btn-white">Add ingredient</a>
                <a href="{{ route('inventory.ingredients.index') }}" class="btn btn-white">All ingredients</a>
                <a href="{{ route('inventory.stock-in') }}" class="btn btn-primary">Stock in</a>
                <a href="{{ route('inventory.waste') }}" class="btn btn-outline-secondary">Record waste</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card mb-0">
                    <div class="card-body">
                        <h6 class="mb-3">Low stock</h6>
                        @forelse($lowStock as $row)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span>{{ $row['ingredient']->name }} <small class="text-muted">({{ $row['ingredient']->unit }})</small></span>
                                <span class="badge bg-warning text-dark">{{ number_format((float)$row['on_hand'], 3) }} / {{ number_format((float)$row['threshold'], 3) }}</span>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No low-stock alerts (or thresholds not set).</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-0">
                    <div class="card-body">
                        <h6 class="mb-3">Expiring within 7 days</h6>
                        @forelse($expiring as $batch)
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                <span>{{ $batch->ingredient->name ?? '–' }}</span>
                                <span class="small">{{ $batch->expiry_date?->format('d M Y') }} · {{ number_format((float)$batch->quantity_remaining, 3) }} {{ $batch->ingredient->unit ?? '' }}</span>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">No batches expiring soon.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-0">
            <div class="card-body">
                <h6 class="mb-3">On-hand by ingredient</h6>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Ingredient</th><th>Unit</th><th>On hand</th></tr></thead>
                        <tbody>
                            @foreach($ingredients as $ing)
                                <tr>
                                    <td>{{ $ing->name }}</td>
                                    <td>{{ $ing->unit }}</td>
                                    <td>{{ number_format((float)($onHand[$ing->id] ?? 0), 4) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-4 mb-0">
            <div class="card-body">
                <h6 class="mb-3">Recent movements</h6>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>When</th>
                                <th>Type</th>
                                <th>Ingredient</th>
                                <th class="text-end">Qty Δ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentMovements as $m)
                                <tr>
                                    <td class="text-nowrap small">{{ $m->created_at->format('d M H:i') }}</td>
                                    <td><span class="badge bg-light text-dark">{{ $m->type }}</span></td>
                                    <td>{{ $m->ingredient->name ?? '–' }}</td>
                                    <td class="text-end font-monospace">{{ $m->quantity_change }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
