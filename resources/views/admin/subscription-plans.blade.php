@php $page = 'admin-subscription-plans'; @endphp
@extends('layout.mainlayout')
@push('styles')
<style>
.admin-plans-table { min-width: 720px; }
.admin-plans-table th { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.02em; }
#planModal .modal-dialog { max-width: 480px; }
@media (max-width: 576px) { #planModal .modal-dialog { margin: 0.5rem; max-width: calc(100% - 1rem); } .modal-body .row.g-3 > [class*="col-"] { flex: 0 0 100%; max-width: 100%; } }
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
                            <li class="breadcrumb-item active" aria-current="page">Subscription Plans</li>
                        </ol>
                    </nav>
                    <h3 class="mb-0 fw-bold">Subscription Plans</h3>
                    <p class="text-muted mb-0 small">Define pricing plans for restaurant subscriptions.</p>
                </div>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <a href="{{ route('admin.subscriptions') }}" class="btn btn-outline-primary btn-sm"><i class="icon-list me-1"></i>Subscriptions</a>
                    <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#planModal" onclick="resetPlanModal()">
                        <i class="icon-circle-plus me-1"></i>Add Plan
                    </button>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible border-0 mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Plans Table -->
            <div class="card border-0 shadow-sm overflow-hidden">
                <div class="card-body p-0 p-sm-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 admin-plans-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Plan Name</th>
                                    <th>Duration</th>
                                    <th>Price</th>
                                    <th>Credit</th>
                                    <th>Status</th>
                                    <th>Subscriptions</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $i => $plan)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <div class="fw-medium">{{ $plan->name }}</div>
                                            @if($plan->description)
                                                <small class="text-muted">{{ Str::limit($plan->description, 60) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $plan->duration_in_days }} days</span>
                                        </td>
                                        <td class="fw-semibold">₹{{ number_format($plan->price, 2) }}</td>
                                        <td>
                                            @if($plan->credit_amount !== null && (float)$plan->credit_amount > 0)
                                                <span class="badge bg-success bg-opacity-10 text-success">₹{{ number_format($plan->credit_amount, 2) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($plan->is_active)
                                                <span class="badge badge-soft-success">Active</span>
                                            @else
                                                <span class="badge badge-soft-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $plan->subscriptions_count }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.plan-items', $plan) }}" class="btn btn-sm btn-soft-success me-1" title="Manage Items">
                                                <i class="icon-layers"></i>
                                            </a>
                                            <button class="btn btn-sm btn-soft-primary me-1" onclick="editPlan({{ json_encode($plan) }})">
                                                <i class="icon-pencil-line"></i>
                                            </button>
                                            <form action="{{ route('admin.subscription-plans.destroy', $plan) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this plan? Subscriptions using it will also be deleted.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-soft-danger"><i class="icon-trash-2"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
                                            <i class="icon-credit-card fs-48 text-muted d-block mb-2"></i>
                                            <h6 class="mb-1">No plans yet</h6>
                                            <p class="text-muted mb-3">Create your first subscription plan.</p>
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#planModal" onclick="resetPlanModal()">
                                                <i class="icon-circle-plus me-1"></i>Add Plan
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Plan Create/Edit Modal -->
    <div class="modal fade" id="planModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <form id="planForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="planMethod" value="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="planModalTitle">Add Subscription Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="planName" class="form-control" placeholder="e.g. Monthly, Quarterly, Yearly" required>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Duration (days) <span class="text-danger">*</span></label>
                                <input type="number" name="duration_in_days" id="planDuration" class="form-control" min="1" placeholder="30" required>
                                <div class="form-text small">30=Monthly, 90=Quarterly, 365=Yearly</div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
                                <input type="number" name="price" id="planPrice" class="form-control" step="0.01" min="0" placeholder="999.00" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Credit amount (₹)</label>
                            <input type="number" name="credit_amount" id="planCreditAmount" class="form-control" step="0.01" min="0" placeholder="e.g. 500">
                            <div class="form-text">Initial balance for the hotel (e.g. 500). When they use 400, remaining = 100. Leave empty if not used.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="planDescription" class="form-control" rows="2" placeholder="Optional plan description"></textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="planIsActive" value="1" checked>
                            <label class="form-check-label" for="planIsActive">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="planSubmitBtn">Create Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function resetPlanModal() {
    document.getElementById('planForm').action = "{{ route('admin.subscription-plans.store') }}";
    document.getElementById('planMethod').value = 'POST';
    document.getElementById('planModalTitle').textContent = 'Add Subscription Plan';
    document.getElementById('planSubmitBtn').textContent = 'Create Plan';
    document.getElementById('planName').value = '';
    document.getElementById('planDuration').value = '';
    document.getElementById('planPrice').value = '';
    document.getElementById('planCreditAmount').value = '';
    document.getElementById('planDescription').value = '';
    document.getElementById('planIsActive').checked = true;
}

function editPlan(plan) {
    document.getElementById('planForm').action = "{{ url('admin/subscription-plans') }}/" + plan.id;
    document.getElementById('planMethod').value = 'PUT';
    document.getElementById('planModalTitle').textContent = 'Edit Plan: ' + plan.name;
    document.getElementById('planSubmitBtn').textContent = 'Update Plan';
    document.getElementById('planName').value = plan.name;
    document.getElementById('planDuration').value = plan.duration_in_days;
    document.getElementById('planPrice').value = plan.price;
    document.getElementById('planCreditAmount').value = plan.credit_amount != null ? plan.credit_amount : '';
    document.getElementById('planDescription').value = plan.description || '';
    document.getElementById('planIsActive').checked = plan.is_active;
    new bootstrap.Modal(document.getElementById('planModal')).show();
}
</script>
@endpush
