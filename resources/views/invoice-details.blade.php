<?php $page = 'invoice-details'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <style>
        /* Invoice print: fit on a single page (desktop printers) */
        @media print {
            @page { size: A4; margin: 8mm; }

            /* Hide app chrome */
            .navbar-header,
            .sidebar,
            .app-site-footer,
            .top-actions,
            .btn,
            .dropdown,
            .mobile-btn,
            #mobile_btn {
                display: none !important;
            }

            html, body {
                background: #fff !important;
            }

            .page-wrapper {
                margin: 0 !important;
                min-height: auto !important;
            }

            .content {
                padding: 0 !important;
            }

            .card {
                box-shadow: none !important;
                border: 0 !important;
            }

            .card-body {
                padding: 0 !important;
            }

            /* Prevent responsive wrappers from clipping */
            .table-responsive {
                overflow: visible !important;
            }

            /* Compact typography so it stays one-page */
            h3 { font-size: 16px !important; margin: 0 0 8px !important; }
            h6 { font-size: 12px !important; margin-bottom: 6px !important; }
            p, td, th, small, span { font-size: 11px !important; }

            .mb-4 { margin-bottom: 10px !important; }
            .pb-4 { padding-bottom: 10px !important; }

            /* Avoid page breaks inside the main invoice blocks */
            .invoice-logo,
            .table,
            .row,
            .card-body > div {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }
    </style>

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1">
                    <h3 class="mb-0">Invoices Details <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="icon-upload me-1"></i>Export
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-3">
                            <li>
                                <a href="#" class="dropdown-item rounded">Export as PDF</a>
                            </li>
                            <li>
                                <a href="#" class="dropdown-item rounded">Export as Excel</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- End Page Header -->

            <a href="{{url('invoices')}}" class="d-inline-flex align-items-center mb-4"><i class="icon-arrow-left me-2"></i>Back</a>

            <!-- card start -->
            <div class="card mb-0">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="row justify-content-between align-items-center border-bottom pb-4">
                            <div class="col-md-6">
                                <h6 class="mb-2">#{{ $order->order_number ?? $order->id ?? '–' }}</h6>
                                <h6>{{ $order->restaurant->name ?? 'Restaurant' }}</h6>
                            </div>
                            <div class="col-md-6">
                                    <div class="mb-2 invoice-logo d-flex align-items-center justify-content-md-end justify-content-start">
                                        <span class="app-brand-logo app-brand-logo--invoice"><img src="{{URL::asset('build/img/logo.svg')}}" width="130" class="img-fluid logo" alt="logo"></span>
                                        <span class="app-brand-logo app-brand-logo--invoice"><img src="{{URL::asset('build/img/logo-white.svg')}}" width="130" class="img-fluid logo-white d-none" alt="logo"></span>
                                    </div>
                                </div>
                                </div>
                        </div>
                        <div class="mb-4">
                                <div class="row g-3 justify-content-between align-items-center border-bottom pb-4">
                                <div class="col-md-4">
                                    <h6 class="mb-2">Invoice From</h6>
                                    <p class="text-dark fw-semibold mb-2">{{ $order->restaurant->name ?? 'Restaurant' }}</p>
                                    <p class="mb-0">{{ $order->restaurant->address ?? '–' }}</p>
                                    <p class="mb-0">Phone : {{ $order->restaurant->phone ?? '–' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="mb-2">Bill To </h6>
                                    <p class="text-dark fw-semibold mb-2">{{ $order->customer_name ?: 'Walk-in' }}</p>
                                    <p class="mb-0">Phone : {{ $order->customer_phone ?? '–' }}</p>
                                    <p class="mb-0 small">{{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : '–' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center justify-content-md-center">
                                        <img src="{{URL::asset('build/img/invoices/paid-invoices.svg')}}" alt="paid-invoices-img">
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h6 class="mb-3">Items Details</h6>
                                <div class="table-responsive table-nowrap">
                                <table class="table mb-0 border ">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Item Details</th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items ?? [] as $idx => $item)
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td>{{ $item->item_name ?? 'Item' }}</td>
                                            <td>{{ $item->quantity ?? 0 }}</td>
                                            <td>{{ $currency_symbol }}{{ number_format($item->unit_price ?? 0, 2) }}</td>
                                            <td>{{ $currency_symbol }}{{ number_format($item->total_price ?? (($item->quantity ?? 0) * ($item->unit_price ?? 0)), 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="row g-3 justify-content-between align-items-center pb-4 border-bottom">
                                    <div class="col-md-6">
                                        <h6 class="mb-2">Terms and Conditions</h6>
                                        <div class="mb-4">
                                            <p class="mb-0">1. Goods once sold cannot be taken back or exchanged.</p>
                                            <p class="mb-0">2. We are not the manufacturers the company provides warranty</p>
                                        </div>
                                        <div class="px-3 py-2 bg-light">
                                            <p class="mb-0">Note : Please ensure payment is made within 7 days of invoice date.</p>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-md-5">
                                        <div class="mb-3">
                                            <div class="row align-items-center pb-3 border-bottom">
                                                <div class="col-6">
                                                    <p class="text-dark fw-semibold mb-3">Amount</p>
                                                    <p class="text-dark fw-semibold mb-3">CGST (9%)</p>
                                                    <p class="text-dark fw-semibold mb-3">SGST (9%)</p>
                                                    <p class="text-dark fw-semibold mb-0">Discount (25%)</p>
                                                </div>
                                                <div class="col-6 text-end">
                                                    <p class="text-dark fw-semibold mb-3">{{ $currency_symbol }}{{ number_format($order->subtotal ?? 0, 2) }}</p>
                                                    <p class="text-dark fw-semibold mb-3">{{ $currency_symbol }}{{ number_format($order->tax_amount ?? 0, 2) }}</p>
                                                    <p class="text-dark fw-semibold mb-3">{{ $currency_symbol }}{{ number_format($order->tax_amount ?? 0, 2) }}</p>
                                                    <p class="text-danger fw-semibold mb-0">- {{ $currency_symbol }}{{ number_format($order->discount_amount ?? 0, 2) }}</p>
                                                </div>
                                                </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6>Total</h6>
                                            <h6>{{ $currency_symbol }}{{ number_format($order->total ?? 0, 2) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($order->restaurant && $order->restaurant->payment_qr)
                            <div class="mb-4 p-4 border rounded bg-light text-center">
                                <h6 class="mb-2">Pay using QR</h6>
                                <p class="text-muted small mb-3">Scan the QR below to pay. After payment, inform staff so they can mark this order as paid.</p>
                                <img src="{{ asset('storage/' . $order->restaurant->payment_qr) }}" alt="Payment QR" class="img-fluid rounded" style="max-width: 180px;">
                                <p class="text-muted small mt-2 mb-0">Order total: {{ $currency_symbol }}{{ number_format($order->total ?? 0, 2) }}</p>
                            </div>
                            @endif
                            <div class="d-flex justify-content-center algin-item-center flex-wrap gap-3">
                                <button type="button" class="btn btn-white d-flex align-items-center"><i class="icon-download me-1"></i>Download PDF</button>
                            <button type="button" class="btn btn-white d-flex align-items-center"><i class="icon-printer me-1"></i>Print Invoice</button>
                    </div>
                </div>
            </div>
            <!-- card start -->

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

<script>
(function() {
    if (window.location.search.indexOf('print=1') !== -1) setTimeout(function() { window.print(); }, 500);
    var printBtn = document.querySelector('.btn .icon-printer');
    if (printBtn && printBtn.closest('button')) printBtn.closest('button').addEventListener('click', function() { window.print(); });
})();
</script>
@endsection
