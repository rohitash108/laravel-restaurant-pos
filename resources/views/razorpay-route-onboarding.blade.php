<?php $page = 'payment-settings'; ?>
@extends('layout.mainlayout')

@section('content')
<div class="page-wrapper">
    <div class="content">

        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <h3 class="mb-0">Online Payments — Razorpay Route</h3>
                <p class="text-muted mb-0" style="font-size:.85rem;">
                    Submit your bank details once and money from every customer order will settle directly to your bank account.
                </p>
            </div>
            <a href="{{ route('payment-settings') }}" class="btn btn-light btn-sm">← Back to Payment Types</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(! $masterConfigured)
            <div class="alert alert-warning">
                <strong>Platform not yet configured.</strong>
                The Razorpay master keys are missing in <code>.env</code> (<code>RAZORPAY_MASTER_KEY_ID</code>,
                <code>RAZORPAY_MASTER_KEY_SECRET</code>). Onboarding will not work until the platform owner sets these
                and enables the Route product on their Razorpay partner dashboard.
            </div>
        @endif

        {{-- ===== Status card ===== --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Account Status</h5>
                        @php
                            $st = $restaurant->razorpay_account_status;
                            $badge = match($st) {
                                'activated'        => 'success',
                                'under_review',
                                'created'          => 'warning',
                                'needs_clarification' => 'info',
                                'rejected', 'suspended' => 'danger',
                                default            => 'secondary',
                            };
                            $label = $st ?: 'Not started';
                        @endphp
                        <span class="badge bg-{{ $badge }}">{{ ucwords(str_replace('_', ' ', $label)) }}</span>
                        @if($restaurant->razorpay_linked_account_id)
                            <code class="ms-2" style="font-size:.8rem;">{{ $restaurant->razorpay_linked_account_id }}</code>
                        @endif
                        @if($restaurant->razorpay_status_reason)
                            <p class="text-muted mb-0 mt-1" style="font-size:.8rem;">
                                Reason: {{ $restaurant->razorpay_status_reason }}
                            </p>
                        @endif
                    </div>
                    @if($restaurant->razorpay_linked_account_id)
                        <form method="POST" action="{{ route('razorpay.route.refresh') }}">
                            @csrf
                            <button type="submit" class="btn btn-light btn-sm">
                                <i class="icon-refresh-ccw"></i> Refresh from Razorpay
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== Onboarding form ===== --}}
        <div class="card">
            <div class="card-body">
                <form action="{{ route('razorpay.route.onboarding.store') }}" method="POST" autocomplete="off">
                    @csrf

                    <h5 class="mb-3">Business Details</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Legal business name <span class="text-danger">*</span></label>
                            <input type="text" name="legal_business_name" class="form-control"
                                   value="{{ old('legal_business_name', $restaurant->name) }}" required>
                            <small class="text-muted">Exactly as on PAN / GST.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Business type <span class="text-danger">*</span></label>
                            <select name="business_type" class="form-select" required>
                                <option value="">— Select —</option>
                                @foreach($businessTypes as $val => $label)
                                    <option value="{{ $val }}"
                                        {{ old('business_type', $restaurant->razorpay_business_type) === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">PAN <span class="text-danger">*</span></label>
                            <input type="text" name="pan" class="form-control text-uppercase"
                                   value="{{ old('pan') }}" maxlength="10"
                                   pattern="^[A-Z]{5}[0-9]{4}[A-Z]{1}$" required>
                            <small class="text-muted">Format: ABCDE1234F</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">GSTIN (optional)</label>
                            <input type="text" name="gst" class="form-control text-uppercase"
                                   value="{{ old('gst', $restaurant->gst_number) }}" maxlength="15">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Contact person <span class="text-danger">*</span></label>
                            <input type="text" name="contact_name" class="form-control"
                                   value="{{ old('contact_name') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control"
                                   value="{{ old('email', $restaurant->email) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control"
                                   value="{{ old('phone', $restaurant->phone) }}" required>
                            <small class="text-muted">10-digit Indian number.</small>
                        </div>
                    </div>

                    <h5 class="mb-3">Registered Address</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label">Street <span class="text-danger">*</span></label>
                            <input type="text" name="street1" class="form-control"
                                   value="{{ old('street1', $restaurant->address) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Apt / Suite</label>
                            <input type="text" name="street2" class="form-control"
                                   value="{{ old('street2', $restaurant->address2) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control"
                                   value="{{ old('city', $restaurant->city) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">State <span class="text-danger">*</span></label>
                            <input type="text" name="state" class="form-control"
                                   value="{{ old('state', $restaurant->state) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Pincode <span class="text-danger">*</span></label>
                            <input type="text" name="postal_code" class="form-control"
                                   value="{{ old('postal_code', $restaurant->pincode) }}"
                                   maxlength="6" pattern="^\d{6}$" required>
                        </div>
                    </div>

                    <h5 class="mb-3">Settlement Bank Account</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Account holder name <span class="text-danger">*</span></label>
                            <input type="text" name="beneficiary_name" class="form-control"
                                   value="{{ old('beneficiary_name') }}" required>
                            <small class="text-muted">Must match the name on your bank passbook / cancelled cheque exactly. Mismatches are the #1 reason onboarding fails.</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Account number <span class="text-danger">*</span></label>
                            <input type="text" name="bank_account_number" class="form-control"
                                   value="{{ old('bank_account_number') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">IFSC <span class="text-danger">*</span></label>
                            <input type="text" name="bank_ifsc" class="form-control text-uppercase"
                                   value="{{ old('bank_ifsc') }}"
                                   maxlength="11" pattern="^[A-Z]{4}0[A-Z0-9]{6}$" required>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="tnc_accepted" id="tnc_accepted" value="1" required>
                        <label class="form-check-label" for="tnc_accepted">
                            I authorise this platform to onboard my business as a Razorpay Linked Account on my behalf,
                            and I accept the <a href="https://razorpay.com/terms/" target="_blank">Razorpay Terms</a>
                            and the <a href="https://razorpay.com/route/policy/" target="_blank">Route policy</a>.
                            Razorpay will charge ~2% MDR per transaction. Settlement is T+2.
                            @if($platformFeePc > 0)
                                The platform also retains <strong>{{ $platformFeePc }}%</strong> per transaction.
                            @endif
                        </label>
                    </div>

                    <div class="border-top pt-3 d-flex justify-content-end gap-2">
                        <a href="{{ route('payment-settings') }}" class="btn btn-light">Cancel</a>
                        <button type="submit" class="btn btn-primary"
                                @if(! $masterConfigured) disabled @endif>
                            Submit for Verification
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection
