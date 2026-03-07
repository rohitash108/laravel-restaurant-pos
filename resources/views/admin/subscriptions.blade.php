@php $page = 'admin-subscriptions'; @endphp
@extends('layout.mainlayout')
@push('styles')
<style>
.subscription-stats .card { border-radius: 12px; transition: transform 0.15s ease; }
.subscription-stats .card:hover { transform: translateY(-2px); }
.subscription-stats .avatar { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
.admin-sub-tabs { flex-wrap: wrap; gap: 0.25rem; }
.admin-sub-tabs .nav-link { border-radius: 8px; padding: 0.5rem 0.85rem; font-weight: 500; }
.admin-sub-table { min-width: 1100px; }
.admin-sub-table th { white-space: nowrap; font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.02em; }
.admin-sub-table td { vertical-align: middle; }
.admin-sub-actions { display: flex; flex-wrap: wrap; gap: 0.35rem; justify-content: flex-end; }
#assignModal .modal-dialog { max-width: 520px; }
#payPendingModal .modal-dialog { max-width: 420px; }
@media (max-width: 576px) {
    .subscription-stats .card-body { padding: 1rem; }
    .admin-sub-actions .btn { padding: 0.35rem 0.5rem; font-size: 0.8rem; }
    #assignModal .modal-dialog, #payPendingModal .modal-dialog { margin: 0.5rem; max-width: calc(100% - 1rem); }
    .modal-body .row.g-3 > [class*="col-"] { flex: 0 0 100%; max-width: 100%; }
}
</style>
@endpush
@section('content')

    <div class="page-wrapper">
        <div class="content container-fluid">

            <!-- Page Header -->
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1">
                    <nav aria-label="breadcrumb" class="mb-1">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Subscriptions</li>
                        </ol>
                    </nav>
                    <h3 class="mb-0 fw-bold">Subscriptions</h3>
                    <p class="text-muted mb-0 small">Manage restaurant subscriptions and renewals.</p>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <a href="{{ route('admin.subscription-plans') }}" class="btn btn-outline-primary btn-sm"><i class="icon-credit-card me-1"></i>Plans</a>
                    <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="icon-circle-plus me-1"></i>Assign
                    </button>
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

            <!-- Stats Row -->
            <div class="row g-3 mb-4 subscription-stats">
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3 py-3">
                            <div class="avatar bg-success bg-opacity-10 text-success">
                                <i class="icon-check-circle fs-22"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small mb-0">Active</p>
                                <h4 class="mb-0 fw-bold">{{ $totalActive }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3 py-3">
                            <div class="avatar bg-warning bg-opacity-10 text-warning">
                                <i class="icon-clock fs-22"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small mb-0">Expiring soon (7d)</p>
                                <h4 class="mb-0 fw-bold">{{ $expiringSoon }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center gap-3 py-3">
                            <div class="avatar bg-danger bg-opacity-10 text-danger">
                                <i class="icon-x-circle fs-22"></i>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="text-muted small mb-0">Expired</p>
                                <h4 class="mb-0 fw-bold">{{ $totalExpired }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Tabs & Table -->
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-transparent border-0 pt-3 pb-0 px-3">
                    <ul class="nav nav-pills admin-sub-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="{{ route('admin.subscriptions', ['status' => 'all']) }}">All</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status === 'active' ? 'active' : '' }}" href="{{ route('admin.subscriptions', ['status' => 'active']) }}"><i class="icon-check-circle me-1 small"></i>Active</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status === 'expiring' ? 'active' : '' }}" href="{{ route('admin.subscriptions', ['status' => 'expiring']) }}"><i class="icon-clock me-1 small"></i>Expiring</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status === 'expired' ? 'active' : '' }}" href="{{ route('admin.subscriptions', ['status' => 'expired']) }}"><i class="icon-x-circle me-1 small"></i>Expired</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0 p-sm-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 admin-sub-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Restaurant</th>
                                    <th>Plan</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Days Left</th>
                                    <th>Plan Price</th>
                                    <th>Amount Paid</th>
                                    <th>Due / Credit</th>
                                    <th>Plan balance</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscriptions as $i => $sub)
                                    <tr>
                                        <td>{{ $subscriptions->firstItem() + $i }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-sm avatar-rounded bg-light d-flex align-items-center justify-content-center">
                                                    <i class="icon-warehouse text-muted"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $sub->restaurant->name ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $sub->plan->name ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $sub->starts_at->format('d M Y') }}</td>
                                        <td>{{ $sub->ends_at->format('d M Y') }}</td>
                                        <td>
                                            @if($sub->isActive())
                                                @php $days = $sub->daysRemaining(); @endphp
                                                @if($days <= 7)
                                                    <span class="badge badge-soft-warning">{{ $days }} days</span>
                                                @else
                                                    <span class="badge badge-soft-success">{{ $days }} days</span>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="fw-semibold">₹{{ number_format($sub->plan->price ?? 0, 2) }}</td>
                                        <td class="fw-semibold">₹{{ number_format($sub->amount_paid, 2) }}</td>
                                        <td>
                                            @php
                                                $planPrice = (float) ($sub->plan->price ?? 0);
                                                $paid = (float) $sub->amount_paid;
                                                $due = round($planPrice - $paid, 2);
                                            @endphp
                                            @if($due > 0)
                                                <span class="text-danger fw-semibold">Due ₹{{ number_format($due, 2) }}</span>
                                            @elseif($due < 0)
                                                <span class="text-success fw-semibold">Credit ₹{{ number_format(-$due, 2) }}</span>
                                            @else
                                                <span class="text-muted">Paid</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php $bal = (float) ($sub->balance ?? 0); @endphp
                                            @if($bal > 0)
                                                <a href="{{ route('admin.subscriptions.balance-history', $sub) }}" class="fw-semibold text-primary" title="View balance history">₹{{ number_format($bal, 2) }}</a>
                                            @else
                                                <span class="text-muted">₹{{ number_format($bal, 2) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sub->status === 'cancelled')
                                                <span class="badge badge-soft-secondary">Cancelled</span>
                                            @elseif($sub->isActive())
                                                <span class="badge badge-soft-success">Active</span>
                                            @elseif($sub->isExpired())
                                                <span class="badge badge-soft-danger">Expired</span>
                                            @else
                                                <span class="badge badge-soft-secondary">{{ ucfirst($sub->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="admin-sub-actions">
                                            @php
                                                $pendingAmt = max(0, round((float)($sub->plan->price ?? 0) - (float)$sub->amount_paid, 2));
                                                $creditAmt = max(0, round((float)$sub->amount_paid - (float)($sub->plan->price ?? 0), 2));
                                            @endphp
                                            @if($sub->status === 'active')
                                                <button type="button" class="btn btn-sm btn-soft-warning pay-pending-btn" data-bs-toggle="modal" data-bs-target="#payPendingModal" data-sub-url="{{ route('admin.subscriptions.record-payment', $sub) }}" data-sub-restaurant="{{ e($sub->restaurant->name ?? '') }}" data-sub-pending="{{ $pendingAmt }}" data-sub-credit="{{ $creditAmt }}" title="{{ $pendingAmt > 0 ? 'Pay due or add advance' : 'Add payment / advance' }}">
                                                    <i class="icon-wallet"></i><span class="d-none d-sm-inline ms-1">{{ $pendingAmt > 0 ? 'Pay' : 'Add' }}</span>
                                                </button>
                                            @endif
                                            @if((float)($sub->balance ?? 0) > 0 && $sub->isActive())
                                                <a href="{{ route('admin.subscriptions.balance-history', $sub) }}" class="btn btn-sm btn-soft-primary" title="Balance history"><i class="icon-history"></i></a>
                                            @endif
                                            @if(!$sub->isActive())
                                                <button type="button" class="btn btn-sm btn-soft-success" data-restaurant-id="{{ $sub->restaurant_id }}" data-restaurant-name="{{ e($sub->restaurant->name ?? '') }}" onclick="renewSubscription(this)" title="Renew"><i class="icon-refresh-ccw"></i></button>
                                            @endif
                                            @if($sub->status === 'active')
                                                <form action="{{ route('admin.subscriptions.destroy', $sub) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this subscription?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-soft-danger" title="Cancel"><i class="icon-x"></i></button>
                                                </form>
                                            @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center py-5">
                                            <i class="icon-credit-card fs-48 text-muted d-block mb-2"></i>
                                            <h6 class="mb-1">No subscriptions found</h6>
                                            <p class="text-muted mb-3">Assign a subscription to a restaurant to get started.</p>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignModal">
                                                <i class="icon-circle-plus me-1"></i>Assign Subscription
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($subscriptions->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $subscriptions->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- Assign Subscription Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <form action="{{ route('admin.subscriptions.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignModalTitle">Assign Subscription</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Restaurant <span class="text-danger">*</span></label>
                            <select name="restaurant_id" id="assignRestaurant" class="form-select" required onchange="updateAssignCreditRow()">
                                <option value="">Select Restaurant</option>
                                @foreach($restaurants as $restaurant)
                                    @php $creditsMap = $restaurantCredits ?? []; $cred = isset($creditsMap[$restaurant->id]) ? round((float)$creditsMap[$restaurant->id], 2) : 0; @endphp
                                    <option value="{{ $restaurant->id }}" data-credit="{{ $cred }}" {{ old('restaurant_id') == $restaurant->id ? 'selected' : '' }}>{{ $restaurant->name }}{{ $cred > 0 ? ' (Credit ₹' . number_format($cred, 2) . ')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subscription Plan <span class="text-danger">*</span></label>
                            <select name="subscription_plan_id" id="assignPlan" class="form-select" required onchange="updatePlanDetails()">
                                <option value="">Select Plan</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" data-duration="{{ $plan->duration_in_days }}" data-price="{{ $plan->price }}" {{ old('subscription_plan_id') == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} — ₹{{ number_format($plan->price, 2) }} ({{ $plan->duration_in_days }} days)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                <input type="date" name="starts_at" id="assignStartDate" class="form-control" value="{{ old('starts_at', date('Y-m-d')) }}" required onchange="updateEndDate()">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label">End Date (auto)</label>
                                <input type="text" id="assignEndDate" class="form-control bg-light" readonly>
                            </div>
                        </div>
                        <div id="assignCreditRow" class="mb-3 d-none">
                            <label class="form-label">Apply credit from previous (₹)</label>
                            <input type="number" id="assignApplyCredit" class="form-control" step="0.01" min="0" value="0" placeholder="0" onchange="onApplyCreditChange()" oninput="onApplyCreditChange()">
                            <div class="form-text text-success">This restaurant has credit from past subscriptions. Apply to reduce amount paid.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount Paid (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="amount_paid" id="assignAmount" class="form-control" step="0.01" min="0" value="{{ old('amount_paid') }}" required>
                            <div class="form-text">Cash collected. If applying credit above, amount paid = plan price − applied credit.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional remarks">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Assign Subscription</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pay / Add payment Modal -->
    <div class="modal fade" id="payPendingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <form id="payPendingForm" method="POST" action="">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Pay due or add advance</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Restaurant: <strong id="payPendingRestaurant"></strong></p>
                        <p class="mb-3 text-muted small">Due: <span id="payPendingDue" class="fw-semibold text-danger"></span> &nbsp; Credit: <span id="payPendingCredit" class="fw-semibold text-success"></span></p>
                        <div class="mb-3">
                            <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="payPendingInput" class="form-control" step="0.01" min="0.01" required>
                            <div class="form-text">Pay off due or enter more than due to add advance (credit for next time).</div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Notes (optional)</label>
                            <input type="text" name="notes" class="form-control" placeholder="e.g. Second instalment / Advance for next period" maxlength="500">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Record payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function updatePlanDetails() {
    var planSel = document.getElementById('assignPlan');
    var planOpt = planSel && planSel.options[planSel.selectedIndex];
    var price = planOpt ? parseFloat(planOpt.getAttribute('data-price')) : 0;
    if (price && document.getElementById('assignAmount')) {
        var applyCredit = parseFloat(document.getElementById('assignApplyCredit').value) || 0;
        document.getElementById('assignAmount').value = Math.max(0, (price - applyCredit)).toFixed(2);
    }
    updateEndDate();
}

function updateAssignCreditRow() {
    var restSel = document.getElementById('assignRestaurant');
    var opt = restSel && restSel.options[restSel.selectedIndex];
    var credit = opt ? parseFloat(opt.getAttribute('data-credit')) || 0 : 0;
    var row = document.getElementById('assignCreditRow');
    var applyInp = document.getElementById('assignApplyCredit');
    if (row) row.classList.toggle('d-none', credit <= 0);
    if (applyInp) {
        applyInp.value = 0;
        applyInp.setAttribute('max', credit);
    }
    updatePlanDetails();
}

function onApplyCreditChange() {
    var applyInp = document.getElementById('assignApplyCredit');
    var applyCredit = parseFloat(applyInp && applyInp.value) || 0;
    var restSel = document.getElementById('assignRestaurant');
    var restOpt = restSel && restSel.options[restSel.selectedIndex];
    var maxCredit = restOpt ? parseFloat(restOpt.getAttribute('data-credit')) || 0 : 0;
    var planSel = document.getElementById('assignPlan');
    var planOpt = planSel && planSel.options[planSel.selectedIndex];
    var price = planOpt ? parseFloat(planOpt.getAttribute('data-price')) : 0;
    applyCredit = Math.min(applyCredit, maxCredit, price);
    if (applyInp) applyInp.value = applyCredit.toFixed(2);
    if (document.getElementById('assignAmount')) {
        document.getElementById('assignAmount').value = Math.max(0, (price - applyCredit)).toFixed(2);
    }
}

function updateEndDate() {
    var sel = document.getElementById('assignPlan');
    var opt = sel.options[sel.selectedIndex];
    var duration = parseInt(opt.getAttribute('data-duration'));
    var startDateStr = document.getElementById('assignStartDate').value;

    if (duration && startDateStr) {
        var start = new Date(startDateStr);
        start.setDate(start.getDate() + duration);
        document.getElementById('assignEndDate').value = start.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
    } else {
        document.getElementById('assignEndDate').value = '';
    }
}

function renewSubscription(btn) {
    var id = btn.getAttribute('data-restaurant-id');
    var name = btn.getAttribute('data-restaurant-name') || '';
    document.getElementById('assignRestaurant').value = id;
    document.getElementById('assignModalTitle').textContent = 'Renew Subscription — ' + name;
    new bootstrap.Modal(document.getElementById('assignModal')).show();
}

// Init end date on page load. When there's an error (e.g. subscription already exists), do NOT open modal so the alert stays visible.
document.addEventListener('DOMContentLoaded', function() {
    updateEndDate();
    updateAssignCreditRow();
});

// Pay / Add payment modal: set form action and labels from button data
document.getElementById('payPendingModal')?.addEventListener('show.bs.modal', function(e) {
    var btn = e.relatedTarget;
    if (btn && btn.classList.contains('pay-pending-btn')) {
        var url = btn.getAttribute('data-sub-url') || '';
        var restaurant = btn.getAttribute('data-sub-restaurant') || '';
        var pending = parseFloat(btn.getAttribute('data-sub-pending')) || 0;
        var credit = parseFloat(btn.getAttribute('data-sub-credit')) || 0;
        document.getElementById('payPendingForm').action = url;
        document.getElementById('payPendingRestaurant').textContent = restaurant;
        document.getElementById('payPendingDue').textContent = pending > 0 ? '₹' + pending.toFixed(2) : '—';
        document.getElementById('payPendingCredit').textContent = credit > 0 ? '₹' + credit.toFixed(2) : '—';
        document.getElementById('payPendingInput').value = pending > 0 ? pending.toFixed(2) : '';
        document.getElementById('payPendingInput').removeAttribute('max');
    }
});
</script>
@endpush
