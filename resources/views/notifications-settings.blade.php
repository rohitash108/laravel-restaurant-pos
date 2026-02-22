<?php $page = 'notifications-settings'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1">
                    <h3 class="mb-0">Notifications <a href="{{ route('notifications-settings') }}" class="btn btn-icon btn-sm btn-white rounded-circle ms-2" title="Refresh"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
            </div>
            <!-- End Page Header -->

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

            <div>
                <!-- card start -->
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('notifications-settings.update') }}" method="POST">
                            @csrf

                            <h6 class="mb-1">Global Controls</h6>
                            <p class="text-muted small mb-3">Enable or disable notification channels globally.</p>

                            <div class="border rounded p-3 mb-4">
                                <div class="form-check form-switch d-flex align-items-center justify-content-between ps-0 pb-3 border-bottom mb-3">
                                    <label class="form-check-label fw-medium text-dark" for="mobile_push">Mobile Push Notifications</label>
                                    <input class="form-check-input" type="checkbox" role="switch" id="mobile_push" name="mobile_push" {{ ($settings['mobile_push'] ?? '1') === '1' ? 'checked' : '' }}>
                                </div>
                                <div class="form-check form-switch d-flex align-items-center justify-content-between ps-0">
                                    <label class="form-check-label fw-medium text-dark" for="desktop_notifications">Desktop Notifications</label>
                                    <input class="form-check-input" type="checkbox" role="switch" id="desktop_notifications" name="desktop_notifications" {{ ($settings['desktop_notifications'] ?? '1') === '1' ? 'checked' : '' }}>
                                </div>
                            </div>

                            <h6 class="mb-1">General Notification</h6>
                            <p class="text-muted small mb-3">Choose how you want to be notified for each event type.</p>

                            <div class="border rounded p-3 mb-0">
                                @php
                                    $notifGroups = [
                                        ['label' => 'Payment', 'desc' => 'Payment received & refund alerts', 'keys' => ['payment_push', 'payment_sms', 'payment_email']],
                                        ['label' => 'Transaction', 'desc' => 'Order & transaction updates', 'keys' => ['transaction_push', 'transaction_sms', 'transaction_email']],
                                        ['label' => 'Activity', 'desc' => 'Staff login & actions', 'keys' => ['activity_push', 'activity_sms', 'activity_email']],
                                        ['label' => 'Account', 'desc' => 'Security & account changes', 'keys' => ['account_push', 'account_sms', 'account_email']],
                                    ];
                                @endphp

                                @foreach($notifGroups as $groupIndex => $group)
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 {{ $loop->last ? '' : 'mb-3 pb-3 border-bottom' }}">
                                    <div>
                                        <h6 class="fs-14 fw-medium mb-0">{{ $group['label'] }}</h6>
                                        <small class="text-muted">{{ $group['desc'] }}</small>
                                    </div>
                                    <div class="d-flex align-items-center flex-wrap gap-3 gap-sm-4">
                                        @foreach(['Push', 'SMS', 'Email'] as $typeIndex => $type)
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="{{ $group['keys'][$typeIndex] }}" name="{{ $group['keys'][$typeIndex] }}" {{ ($settings[$group['keys'][$typeIndex]] ?? '1') === '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $group['keys'][$typeIndex] }}">{{ $type }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-2 border-top mt-4 pt-4">
                                <button type="button" class="btn btn-light me-2" onclick="window.location.reload()">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>

                        </form>
                    </div> <!-- end card body -->

                </div>
                <!-- card end -->
            </div>

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
