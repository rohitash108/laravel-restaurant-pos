@php $page = 'admin-subscriptions'; @endphp
@extends('layout.mainlayout')
@push('styles')
<style>
.balance-history-card .card { border-radius: 12px; }
.balance-history-table { min-width: 640px; }
.balance-history-table th { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.02em; }
#debitModal .modal-dialog { max-width: 400px; }
@media (max-width: 576px) { #debitModal .modal-dialog { margin: 0.5rem; max-width: calc(100% - 1rem); } }
</style>
@endpush
@section('content')

    <div class="page-wrapper">
        <div class="content container-fluid">

            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.subscriptions') }}" class="text-muted">Subscriptions</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Balance History</li>
                </ol>
            </nav>

            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1 min-w-0">
                    <h3 class="mb-1 fw-bold">Balance History</h3>
                    <p class="text-muted mb-0 small">{{ $subscription->restaurant->name ?? 'N/A' }} — {{ $subscription->plan->name ?? 'N/A' }}</p>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <div class="bg-primary bg-opacity-10 rounded-3 px-3 py-2 text-primary">
                        <span class="small d-block">Current balance</span>
                        <span class="fw-bold fs-5">₹{{ number_format($subscription->balance, 2) }}</span>
                    </div>
                    <a href="{{ route('admin.subscriptions') }}" class="btn btn-outline-primary btn-sm"><i class="icon-arrow-left me-1"></i>Back</a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible border-0 mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible border-0 mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm overflow-hidden balance-history-card">
                <div class="card-header bg-transparent border-0 d-flex align-items-center justify-content-between flex-wrap gap-2 py-3">
                    <h5 class="mb-0 fw-semibold">Transactions</h5>
                    @if($subscription->isActive() && (float) $subscription->balance > 0)
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#debitModal">
                            <i class="icon-minus-circle me-1"></i>Use balance
                        </button>
                    @endif
                </div>
                <div class="card-body p-0 p-sm-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 balance-history-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Balance after</th>
                                    <th>Description</th>
                                    <th>By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscription->balanceTransactions as $tx)
                                    <tr>
                                        <td>{{ $tx->created_at->format('d M Y, H:i') }}</td>
                                        <td>
                                            @if($tx->type === 'credit')
                                                <span class="badge badge-soft-success">Credit</span>
                                            @else
                                                <span class="badge badge-soft-warning">Debit</span>
                                            @endif
                                        </td>
                                        <td class="fw-semibold {{ $tx->type === 'credit' ? 'text-success' : 'text-danger' }}">
                                            {{ $tx->type === 'credit' ? '+' : '-' }} ₹{{ number_format($tx->amount, 2) }}
                                        </td>
                                        <td>₹{{ number_format($tx->balance_after, 2) }}</td>
                                        <td>{{ $tx->description ?? '—' }}</td>
                                        <td class="text-muted small">{{ $tx->createdByUser->name ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">No transactions yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Use balance (debit) modal -->
    <div class="modal fade" id="debitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <form action="{{ route('admin.subscriptions.debit-balance', $subscription) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Use balance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3">Current balance: <strong class="text-dark">₹{{ number_format($subscription->balance, 2) }}</strong></p>
                        <div class="mb-3">
                            <label class="form-label">Amount to deduct (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" max="{{ $subscription->balance }}" placeholder="e.g. 400" required>
                            <div class="form-text">Hotel pays this amount from plan balance. Remaining = current − this.</div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Description (optional)</label>
                            <input type="text" name="description" class="form-control" placeholder="e.g. Monthly usage" maxlength="255">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Deduct</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
