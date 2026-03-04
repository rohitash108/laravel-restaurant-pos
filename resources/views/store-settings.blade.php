<?php $page = 'store-settings'; ?>
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
                    <h3 class="mb-0">Store Settings <a href="{{ route('store-settings') }}" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
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
                <div class="card mb-0">
                    <div class="card-body">
                        <form action="{{ route('store-settings.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <!-- start row -->
                            <div class="row">

                                <div class="col-xl-12">
                                    <div class="mb-3 d-flex align-items-center flex-wrap gap-3">
                                        <div class="avatar avatar-3xl border bg-light" id="store-logo-preview">
                                            @if($restaurant && $restaurant->logo)
                                                <img src="{{ asset('storage/' . $restaurant->logo) }}" alt="Store Logo" class="img-fluid">
                                            @else
                                                <i class="icon-images fs-28 text-dark"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <label class="form-label mb-1">Upload Store Image</label>
                                            <p class="fs-13 mb-3">Image should be within 5 MB (JPG, PNG, GIF)</p>
                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <div class="btn btn-icon btn-sm btn-white rounded-circle position-relative">
                                                    <input type="file" name="logo" id="store-logo-input" class="form-control position-absolute w-100 h-100 top-0 start-0 opacity-0" accept="image/jpeg,image/png,image/gif,image/webp">
                                                    <i class="icon-upload"></i>
                                                </div>
                                                @if($restaurant && $restaurant->logo)
                                                    <div class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" name="remove_logo" value="1" id="remove_logo">
                                                        <label class="form-check-label text-muted fs-13" for="remove_logo">Remove current logo</label>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-xl-12">
                                    <div class="mb-4 p-3 border rounded bg-light">
                                        <label class="form-label fw-semibold mb-2">Payment QR (for customer payments)</label>
                                        <p class="fs-13 text-muted mb-3">Upload your UPI/bank payment QR so customers can scan and pay. After they pay, mark the order as <strong>Paid</strong> from the <a href="{{ url('orders') }}">Orders</a> page.</p>
                                        <div class="d-flex align-items-center flex-wrap gap-3">
                                            <div class="avatar avatar-3xl border bg-white rounded" id="payment-qr-preview">
                                                @if($restaurant && $restaurant->payment_qr)
                                                    <img src="{{ asset('storage/' . $restaurant->payment_qr) }}" alt="Payment QR" class="img-fluid rounded">
                                                @else
                                                    <i class="icon-qrcode fs-28 text-muted"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="d-flex align-items-center flex-wrap gap-2">
                                                    <div class="btn btn-icon btn-sm btn-white rounded-circle position-relative">
                                                        <input type="file" name="payment_qr" id="payment-qr-input" class="form-control position-absolute w-100 h-100 top-0 start-0 opacity-0" accept="image/jpeg,image/png,image/gif,image/webp">
                                                        <i class="icon-upload"></i>
                                                    </div>
                                                    @if($restaurant && $restaurant->payment_qr)
                                                        <div class="form-check form-check-inline">
                                                            <input type="checkbox" class="form-check-input" name="remove_payment_qr" value="1" id="remove_payment_qr">
                                                            <label class="form-check-label text-muted fs-13" for="remove_payment_qr">Remove current QR</label>
                                                        </div>
                                                    @endif
                                                </div>
                                                <p class="fs-12 text-muted mb-0 mt-1">JPG, PNG, GIF or WebP, max 2 MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Store Name<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="store_name" value="{{ $restaurant->name ?? '' }}">
                                    </div>
                                </div> <!-- end col -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Address 1<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="address1" value="{{ $restaurant->address ?? '' }}">
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Address 2</label>
                                        <input type="text" class="form-control" name="address2" value="{{ $restaurant->address2 ?? '' }}">
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Country<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="country" value="{{ $restaurant->country ?? '' }}">
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">State<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="state" value="{{ $restaurant->state ?? '' }}">
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">City<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="city" value="{{ $restaurant->city ?? '' }}">
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label class="form-label">Pincode<span class="text-danger ms-1">*</span></label>
                                        <input type="text" class="form-control" name="pincode" value="{{ $restaurant->pincode ?? '' }}">
                                    </div>
                                </div> <!-- end col -->


                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email<span class="text-danger ms-1">*</span></label>
                                        <input type="email" class="form-control" name="email" value="{{ $restaurant->email ?? '' }}">
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phone<span class="text-danger ms-1">*</span></label>
                                        <input type="tel" class="form-control" name="phone" value="{{ $restaurant->phone ?? '' }}">
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label">Currency<span class="text-danger ms-1">*</span></label>
                                        <select class="select" name="currency">
                                            <option value="">Select</option>
                                            @foreach(['INR', 'AED', 'EUR', 'GBP'] as $cur)
                                                <option value="{{ $cur }}" {{ ($restaurant->currency ?? 'INR') === $cur ? 'selected' : '' }}>{{ $cur }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" role="switch" id="enable_qr_menu" name="enable_qr_menu" {{ ($settings['enable_qr_menu'] ?? '1') === '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_qr_menu">Enable QR Menu</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" role="switch" id="enable_take_away" name="enable_take_away" {{ ($settings['enable_take_away'] ?? '1') === '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_take_away">Enable Take Away</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" role="switch" id="enable_dine_in" name="enable_dine_in" {{ ($settings['enable_dine_in'] ?? '1') === '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_dine_in">Enable Dine In</label>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" id="enable_reservation" name="enable_reservation" {{ ($settings['enable_reservation'] ?? '0') === '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_reservation">Enable Reservation</label>
                                        </div>
                                    </div>
                                </div> <!-- end col -->

                                <div class="col-md-6">
                                    <div class="mb-0">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" role="switch" id="enable_order_via_qr" name="enable_order_via_qr" {{ ($settings['enable_order_via_qr'] ?? '1') === '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_order_via_qr">Enable Order Via QR Menu</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" role="switch" id="enable_delivery" name="enable_delivery" {{ ($settings['enable_delivery'] ?? '1') === '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_delivery">Enable Delivery</label>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" role="switch" id="enable_table" name="enable_table" {{ ($settings['enable_table'] ?? '1') === '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="enable_table">Enable Table</label>
                                        </div>
                                    </div>
                                </div> <!-- end col -->

                            </div>
                            <!-- end row -->

                            <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-2 border-top mt-2 pt-4">
                                <button type="button" class="btn btn-light me-2" onclick="window.location.reload()">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>

                        </form>
                    </div> <!-- end card body -->

                </div>
                <!-- card start -->
            </div>

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@push('scripts')
<script>
(function() {
    var logoInput = document.getElementById('store-logo-input');
    var logoPreview = document.getElementById('store-logo-preview');
    if (logoInput && logoPreview) {
        logoInput.addEventListener('change', function(e) {
            var f = e.target.files[0];
            if (f && f.type.indexOf('image') === 0) {
                var r = new FileReader();
                r.onload = function() { logoPreview.innerHTML = '<img src="' + r.result + '" alt="Preview" class="img-fluid">'; };
                r.readAsDataURL(f);
            }
        });
    }
    var qrInput = document.getElementById('payment-qr-input');
    var qrPreview = document.getElementById('payment-qr-preview');
    if (qrInput && qrPreview) {
        qrInput.addEventListener('change', function(e) {
            var f = e.target.files[0];
            if (f && f.type.indexOf('image') === 0) {
                var r = new FileReader();
                r.onload = function() { qrPreview.innerHTML = '<img src="' + r.result + '" alt="Payment QR Preview" class="img-fluid rounded">'; };
                r.readAsDataURL(f);
            }
        });
    }
})();
</script>
@endpush
@endsection
