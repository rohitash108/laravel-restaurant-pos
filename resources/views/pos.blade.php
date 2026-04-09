<?php $page = 'pos'; ?>
{{-- POS page: data from PosController ($categories, $tables, $recentOrders, $customers). Cart & Place Order need public/build/js/script.js — after editing resources/js/script.js run: npm run copy:script --}}
@extends('layout.mainlayout')
@section('content')
    @php
        $posCouponsForJs = ($coupons ?? collect())->map(function ($c) {
            return ['id' => $c->id, 'code' => $c->code, 'discount_type' => $c->discount_type, 'discount_amount' => (float) $c->discount_amount, 'category_id' => $c->category_id];
        })->values();
        $posReceiptLogo = auth()->user()->restaurant?->logo
            ? asset('storage/' . auth()->user()->restaurant->logo)
            : asset('build/img/logo-small.svg');
        $posReceiptRestaurantName = auth()->user()->restaurant?->name ?? 'Restaurant';
    @endphp
    <script>window.posTaxRate = @json($tax_rate ?? 0); window.posTaxName = @json($tax_name ?? 'Tax'); window.posCoupons = @json($posCouponsForJs);</script>
    <div id="pos-receipt-meta" class="d-none"
         data-logo="{{ $posReceiptLogo }}"
         data-restaurant="{{ $posReceiptRestaurantName }}"></div>
    <style>
        .pos-item-click { cursor: pointer; }
        /* Fix: override theme's .input-item .btn-icon absolute positioning for customer row */
        .pos-customer-row .btn-icon { position: static !important; width: 32px !important; height: 32px !important; }
        .pos-customer-row { border: none !important; }
        #pos-modal-addons .pos-addon-item { cursor: pointer; transition: border-color .15s, background-color .15s; }
        #pos-modal-addons .pos-addon-item:hover { background-color: rgba(0,0,0,.03); }
        #pos-modal-addons .pos-addon-item.active { border-color: var(--bs-primary); background-color: rgba(var(--bs-primary-rgb), 0.08); }
        /* Print receipt */
        #pos-print-receipt .pos-receipt-menus .pos-receipt-item-list { max-height: 220px; overflow-y: auto; }
        #pos-print-receipt .pos-receipt-item { font-size: 12px; line-height: 1.4; padding: 3px 0; }
        #pos-print-receipt .pos-receipt-item .text-truncate { max-width: 72%; }
        /* POS menu: smaller item cards */
        .pos-item-card-inner { padding: 6px !important; }
        .pos-item-card .food-items { margin-bottom: 6px !important; }
        .pos-item-card .food-items .pos-item-placeholder,
        .pos-item-card .food-items .bg-light.rounded,
        .pos-item-card .food-items img { height: 72px !important; min-height: 72px; object-fit: cover; }
        .pos-item-card .fs-12 { font-size: 10px !important; }
        .pos-item-card .fs-14 { font-size: 12px !important; }
        .pos-item-card .small { font-size: 10px !important; }
        .pos-item-card .mb-2 { margin-bottom: 4px !important; }
        .pos-item-card .quantity-control .quantity-input { width: 28px; font-size: 11px; }
        .pos-item-card .quantity-control button { padding: 2px 6px; font-size: 10px; }

        /* ===== MOBILE & TABLET RESPONSIVE ===== */
        @media (max-width: 991.98px) {
            /* --- Recent Orders: collapsible --- */
            .slider-wrapper { padding-bottom: 0 !important; margin-bottom: 0.5rem !important; }
            .slider-wrapper .tab-content { display: none; }
            .slider-wrapper.pos-orders-open .tab-content { display: block; }
            .pos-orders-toggle { display: inline-flex !important; }

            /* --- Category pills: horizontal scroll --- */
            .category-slider { display: flex !important; flex-wrap: nowrap !important; overflow-x: auto !important; gap: 0.5rem; padding-bottom: 0.5rem; -webkit-overflow-scrolling: touch; scrollbar-width: none; }
            .category-slider::-webkit-scrollbar { display: none; }
            .category-slider .nav-item { flex-shrink: 0; }
            .category-slider .nav-link { white-space: nowrap; padding: 0.35rem 0.75rem !important; font-size: 0.8125rem; }
            .category-slider .nav-link .avatar { width: 28px !important; height: 28px !important; }
            .category-slider .nav-link .avatar img,
            .category-slider .nav-link .avatar i { font-size: 14px !important; }
            .category-slider .nav-link div h6 { font-size: 0.75rem !important; margin-bottom: 0 !important; }
            .category-slider .nav-link div p { display: none !important; }
            .category-prev, .category-next { display: none !important; }

            /* --- Item grid: 2 cols mobile --- */
            .pos-item-card { width: 50% !important; flex: 0 0 50% !important; max-width: 50% !important; }
            .pos-item-card-inner { padding: 4px !important; }
            .pos-item-card .food-items .pos-item-placeholder,
            .pos-item-card .food-items .bg-light.rounded,
            .pos-item-card .food-items img { height: 60px !important; min-height: 60px; }

            /* --- Veg/Non-veg filters: compact --- */
            .orders-list { gap: 0.5rem !important; }
            .orders-list label { font-size: 0.75rem !important; }

            /* --- Search bar: full width --- */
            .pos-search-menu { min-width: 120px; }

            /* --- Hide desktop cart panel --- */
            .pos-right-desktop { display: none !important; }

            /* --- Sticky cart bar (mobile only) --- */
            .pos-mobile-cart-bar {
                display: flex !important;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: var(--bs-primary, #7c3aed);
                color: #fff;
                padding: 0.75rem 1rem;
                z-index: 1050;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
                padding-bottom: calc(0.75rem + env(safe-area-inset-bottom, 0));
            }
            .pos-mobile-cart-bar .cart-bar-left { display: flex; align-items: center; gap: 0.5rem; }
            .pos-mobile-cart-bar .cart-bar-badge { background: rgba(255,255,255,0.25); padding: 0.15rem 0.5rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
            .pos-mobile-cart-bar .cart-bar-total { font-size: 0.9375rem; font-weight: 600; }
            .pos-mobile-cart-bar .cart-bar-btn { background: #fff; color: var(--bs-primary, #7c3aed); border: none; padding: 0.45rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.8125rem; cursor: pointer; }

            /* --- Bottom spacer for sticky bar --- */
            .pos-bottom-spacer { height: 68px; }

            /* --- Offcanvas cart: slide from bottom --- */
            .offcanvas-bottom.pos-cart-offcanvas { height: auto; max-height: 85vh; border-radius: 16px 16px 0 0; }
            .offcanvas-bottom.pos-cart-offcanvas .offcanvas-header { border-bottom: 1px solid var(--bs-border-color); }
            .offcanvas-bottom.pos-cart-offcanvas .pos-right { border: none !important; box-shadow: none; }

            /* --- Action buttons: compact 3x2 --- */
            .pos-action-row .col-sm-4 { flex: 0 0 33.333%; max-width: 33.333%; padding: 0.15rem !important; }
            .pos-action-row .btn { font-size: 0.6875rem !important; padding: 0.3rem 0.4rem !important; }
            .pos-action-row .btn i { font-size: 0.75rem; }

            /* --- Order tabs: smaller --- */
            .pos-tab .nav-link { font-size: 0.75rem !important; padding: 0.3rem 0.25rem !important; }
            .pos-tab .nav-link i { font-size: 0.875rem; }

            /* --- Tab form selects: stack --- */
            .pos-right .tab-content .row .col-lg-5,
            .pos-right .tab-content .row .col-lg-6,
            .pos-right .tab-content .row .col-lg-11,
            .pos-right .tab-content .row .col-lg-12 { width: 100% !important; flex: 0 0 100%; max-width: 100%; }
            .pos-right .tab-content .row .col-lg-1 { display: none; }
        }

        /* Tablet: 3 cols */
        @media (min-width: 576px) and (max-width: 991.98px) {
            .pos-item-card { width: 33.333% !important; flex: 0 0 33.333% !important; max-width: 33.333% !important; }
        }

        /* Desktop: hide mobile elements */
        @media (min-width: 992px) {
            .pos-mobile-cart-bar { display: none !important; }
            .pos-bottom-spacer { display: none !important; }
            .pos-orders-toggle { display: none !important; }
        }
    </style>
    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content-->
        <div class="content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- start row -->
            <div class="row g-0">
                <div class="col-lg-8 pos-left">

                    <div class="slider-wrapper mb-4 pb-4 border-bottom">

                        <div class="d-flex align-items-center flex-wrap gap-3 mb-4">
                                <h3 class="mb-0">Recent Orders</h3>
                                <button type="button" class="btn btn-sm btn-outline-primary pos-orders-toggle" style="display:none;" onclick="this.closest('.slider-wrapper').classList.toggle('pos-orders-open');this.textContent=this.closest('.slider-wrapper').classList.contains('pos-orders-open')?'Hide':'Show'">Show</button>
                            <div class="d-flex align-items-center flex-wrap  justify-content-between gap-2 flex-fill">
                                <ul class="nav nav-tabs nav-tabs-solid border-0 align-items-center flex-wrap gap-2" role="tablist">
                                    <li class="nav-item">
                                        <a href="#" class="nav-link active shadow-sm" data-bs-toggle="tab" data-bs-target="#all">All Orders</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link shadow-sm" data-bs-toggle="tab"  data-bs-target="#dinein">Dine In</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link shadow-sm" data-bs-toggle="tab"  data-bs-target="#takeaway">Take Away</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link shadow-sm" data-bs-toggle="tab"  data-bs-target="#delivery">Delivery</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link shadow-sm" data-bs-toggle="tab"  data-bs-target="#table">Table</a>
                                    </li>
                                </ul>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="slick-arrow all-prev"><i class="icon-arrow-left"></i></button>
                                    <button type="button" class="slick-arrow all-next"><i class="icon-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="tab-content">

                            <!-- Tab -->
                            <div class="tab-pane fade active show" id="all">

                                <div class="all-slider slick-slider">
                                    @forelse($recentOrders ?? [] as $order)
                                    @php
                                        $orderType = $order->order_type ?? 'dine_in';
                                        $typeIcon = $orderType === 'delivery' || $orderType === 'takeaway' ? 'icon-check-check' : ($orderType === 'dine_in' ? 'icon-wine' : 'icon-concierge-bell');
                                        $progress = $order->status === 'completed' ? 100 : ($order->status === 'cancelled' ? 0 : 50);
                                        $elapsed = $order->created_at->diffForHumans(null, true, true);
                                    @endphp
                                    <div class="slide-item">
                                        <div class="order-item">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                                <div>
                                                    <p class="fs-12 mb-1">#{{ $order->order_number }}</p>
                                                    <h6 class="fs-14 fw-semibold mb-1">{{ $order->customer_name ?: 'Walk-in' }}</h6>
                                                    <p class="fs-13 mb-0 d-flex align-items-center gap-2">{{ $order->created_at->format('h:i A') }}@if($order->table)<span class="even-line"></span>{{ $order->table->name }}@endif</p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-light text-dark d-flex align-items-center mb-3"><i class="{{ $typeIcon }} text-dark me-1"></i>{{ ucfirst(str_replace('_', ' ', $orderType)) }}</span>
                                                    <div class="time badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }} rounded-pill fs-12 fw-normal">
                                                        <span class="me-1"><i class="icon-clock-arrow-up"></i></span>{{ $elapsed }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between gap-3">
                                                <div class="progress-item flex-grow-1">
                                                    <div class="progress-bar bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }}" style="width: {{ $progress }}%;"></div>
                                                </div>
                                                <p class="mb-0 fs-10 fw-normal d-flex align-items-center"><i class="icon-clock me-1"></i>{{ $currency_symbol }}{{ number_format($order->total, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="slide-item">
                                        <div class="order-item p-3 text-center text-muted">
                                            <p class="mb-0">No recent orders.</p>
                                        </div>
                                    </div>
                                    @endforelse
                                </div>

                            </div>

                            <!-- Tab: Dine In (dynamic) -->
                            <div class="tab-pane fade" id="dinein">
                                <div class="dinein-slider slick-slider">
                                    @forelse(($recentOrders ?? collect())->where('order_type', 'dine_in') as $order)
                                    @php $progress = $order->status === 'completed' ? 100 : ($order->status === 'cancelled' ? 0 : 50); $elapsed = $order->created_at->diffForHumans(null, true, true); @endphp
                                    <div class="slide-item">
                                        <div class="order-item">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                                <div>
                                                    <p class="fs-12 mb-1">#{{ $order->order_number }}</p>
                                                    <h6 class="fs-14 fw-semibold mb-1">{{ $order->customer_name ?: 'Walk-in' }}</h6>
                                                    <p class="fs-13 mb-0 d-flex align-items-center gap-2">{{ $order->created_at->format('h:i A') }}@if($order->table)<span class="even-line"></span>{{ $order->table->name }}@endif</p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-light text-dark d-flex align-items-center mb-3"><i class="icon-wine text-dark me-1"></i>Dine In</span>
                                                    <div class="time badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }} rounded-pill fs-12 fw-normal"><span class="me-1"><i class="icon-clock-arrow-up"></i></span>{{ $elapsed }}</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between gap-3">
                                                <div class="progress-item flex-grow-1">
                                                    <div class="progress-bar bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }}" style="width: {{ $progress }}%;"></div>
                                                </div>
                                                <p class="mb-0 fs-10 fw-normal d-flex align-items-center"><i class="icon-clock me-1"></i>{{ $currency_symbol }}{{ number_format($order->total, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="slide-item"><div class="order-item p-3 text-center text-muted"><p class="mb-0">No dine-in orders.</p></div></div>
                                    @endforelse
                                </div>
                            </div>

                                <!-- Tab: Take Away (dynamic) -->
                            <div class="tab-pane fade" id="takeaway">
                                <div class="takeaway-slider slick-slider">
                                    @forelse(($recentOrders ?? collect())->where('order_type', 'takeaway') as $order)
                                    @php $progress = $order->status === 'completed' ? 100 : ($order->status === 'cancelled' ? 0 : 50); $elapsed = $order->created_at->diffForHumans(null, true, true); @endphp
                                    <div class="slide-item">
                                        <div class="order-item">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                                <div>
                                                    <p class="fs-12 mb-1">#{{ $order->order_number }}</p>
                                                    <h6 class="fs-14 fw-semibold mb-1">{{ $order->customer_name ?: 'Walk-in' }}</h6>
                                                    <p class="fs-13 mb-0">{{ $order->created_at->format('h:i A') }}</p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-light text-dark d-flex align-items-center mb-3"><i class="icon-check-check text-dark me-1"></i>Take Away</span>
                                                    <div class="time badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }} rounded-pill fs-12 fw-normal"><span class="me-1"><i class="icon-clock-arrow-up"></i></span>{{ $elapsed }}</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between gap-3">
                                                <div class="progress-item flex-grow-1">
                                                    <div class="progress-bar bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }}" style="width: {{ $progress }}%;"></div>
                                                </div>
                                                <p class="mb-0 fs-10 fw-normal d-flex align-items-center"><i class="icon-clock me-1"></i>{{ $currency_symbol }}{{ number_format($order->total, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="slide-item"><div class="order-item p-3 text-center text-muted"><p class="mb-0">No takeaway orders.</p></div></div>
                                    @endforelse
                                </div>
                            </div>

                                <!-- Tab: Delivery (dynamic) -->
                            <div class="tab-pane fade" id="delivery">
                                <div class="delivery-slider slick-slider">
                                    @forelse(($recentOrders ?? collect())->where('order_type', 'delivery') as $order)
                                    @php $progress = $order->status === 'completed' ? 100 : ($order->status === 'cancelled' ? 0 : 50); $elapsed = $order->created_at->diffForHumans(null, true, true); @endphp
                                    <div class="slide-item">
                                        <div class="order-item">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                                <div>
                                                    <p class="fs-12 mb-1">#{{ $order->order_number }}</p>
                                                    <h6 class="fs-14 fw-semibold mb-1">{{ $order->customer_name ?: 'Walk-in' }}</h6>
                                                    <p class="fs-13 mb-0">{{ $order->created_at->format('h:i A') }}</p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-light text-dark d-flex align-items-center mb-3"><i class="icon-check-check text-dark me-1"></i>Delivery</span>
                                                    <div class="time badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }} rounded-pill fs-12 fw-normal"><span class="me-1"><i class="icon-clock-arrow-up"></i></span>{{ $elapsed }}</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between gap-3">
                                                <div class="progress-item flex-grow-1">
                                                    <div class="progress-bar bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }}" style="width: {{ $progress }}%;"></div>
                                                </div>
                                                <p class="mb-0 fs-10 fw-normal d-flex align-items-center"><i class="icon-clock me-1"></i>{{ $currency_symbol }}{{ number_format($order->total, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="slide-item"><div class="order-item p-3 text-center text-muted"><p class="mb-0">No delivery orders.</p></div></div>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Tab: Table / QR (dynamic) -->
                            <div class="tab-pane fade" id="table">
                                <div class="table-slider slick-slider">
                                    @php $tableOrders = ($recentOrders ?? collect())->filter(fn($o) => $o->restaurant_table_id); @endphp
                                    @forelse($tableOrders as $order)
                                    @php $progress = $order->status === 'completed' ? 100 : ($order->status === 'cancelled' ? 0 : 50); $elapsed = $order->created_at->diffForHumans(null, true, true); @endphp
                                    <div class="slide-item">
                                        <div class="order-item">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                                <div>
                                                    <p class="fs-12 mb-1">#{{ $order->order_number }}</p>
                                                    <h6 class="fs-14 fw-semibold mb-1">{{ $order->customer_name ?: 'Walk-in' }}</h6>
                                                    <p class="fs-13 mb-0 d-flex align-items-center gap-2">{{ $order->created_at->format('h:i A') }}@if($order->table)<span class="even-line"></span>{{ $order->table->name }}@endif</p>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-light text-dark d-flex align-items-center mb-3"><i class="icon-concierge-bell text-dark me-1"></i>Table</span>
                                                    <div class="time badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }} rounded-pill fs-12 fw-normal"><span class="me-1"><i class="icon-clock-arrow-up"></i></span>{{ $elapsed }}</div>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between gap-3">
                                                <div class="progress-item flex-grow-1">
                                                    <div class="progress-bar bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'primary') }}" style="width: {{ $progress }}%;"></div>
                                                </div>
                                                <p class="mb-0 fs-10 fw-normal d-flex align-items-center"><i class="icon-clock me-1"></i>{{ $currency_symbol }}{{ number_format($order->total, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="slide-item"><div class="order-item p-3 text-center text-muted"><p class="mb-0">No table orders.</p></div></div>
                                    @endforelse
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- Menu Item (theme: Menu Categories + Veg/Non Veg/Egg filter) -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h5 class="mb-0 me-2">Menu Categories</h5>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2 orders-list">
                            <label class="d-flex align-items-center fs-14 text-dark pos-filter-veg">
                                <input class="form-check-input m-0 me-2 pos-filter-check" type="checkbox" value="veg" checked>
                                <span class="dot success me-1"></span>Veg
                            </label>
                            <label class="d-flex align-items-center fs-14 text-dark pos-filter-nonveg">
                                <input class="form-check-input m-0 me-2 pos-filter-check" type="checkbox" value="non_veg" checked>
                                <span class="dot me-1"></span>Non Veg
                            </label>
                            <label class="d-flex align-items-center fs-14 text-dark pos-filter-egg">
                                <input class="form-check-input m-0 me-2 pos-filter-check" type="checkbox" value="egg">
                                <span class="dot warning me-1"></span>Egg
                            </label>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="input-group input-group-flat w-auto">
                                <input type="text" class="form-control pos-search-menu" placeholder="Search menu">
                                <span class="input-group-text"><i class="icon-search text-dark"></i></span>
                            </div>
                            <a href="#" class="btn btn-icon btn-sm btn-outline-white rounded-circle" aria-label="refresh" id="pos-refresh-menu"><i class="icon-refresh-ccw"></i></a>
                            <a href="#" class="btn btn-icon btn-sm btn-outline-white rounded-circle" aria-label="filter"><i class="icon-list-filter"></i></a>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="slick-arrow category-prev"><i class="icon-arrow-left"></i></button>
                                <button type="button" class="slick-arrow category-next"><i class="icon-arrow-right"></i></button>
                            </div>
                        </div>
                    </div>

                    @if(($categories ?? collect())->isEmpty())
                    <div class="card border">
                        <div class="card-body text-center py-5 text-muted">
                            <i class="icon-layout-list fs-48 mb-3"></i>
                            <p class="mb-0">No menu categories yet. Add categories and items from <a href="{{ route('categories') }}">Categories</a> and <a href="{{ route('items') }}">Items</a>.</p>
                        </div>
                    </div>
                    @else
                    <!-- Category Slider (dynamic) -->
                    <ul class="nav nav-tabs nav-tabs-solid category-tab border-0 category-slider mb-4" role="tablist">
                        <li class="nav-item">
                            <a href="#pos-all-menu" class="nav-link active shadow" data-bs-toggle="tab">
                                <div class="avatar avatar-lg rounded-circle flex-shrink-0 bg-light d-flex align-items-center justify-content-center">
                                    <i class="icon-layout-list text-dark fs-20"></i>
                                </div>
                                <div>
                                    <h6 class="fs-14 fw-semibold mb-1">All Menus</h6>
                                    <p class="text-body fw-normal mb-0">{{ $categories->sum(fn($c) => $c->items->count()) }} Menus</p>
                                </div>
                            </a>
                        </li>
                        @foreach($categories as $cat)
                        <li class="nav-item">
                            <a href="#pos-cat-{{ $cat->id }}" class="nav-link shadow" data-bs-toggle="tab">
                                <div class="avatar avatar-lg rounded-circle flex-shrink-0 bg-light d-flex align-items-center justify-content-center">
                                    @if($cat->image)
                                    <img src="{{ asset('storage/' . $cat->image) }}" alt="{{ $cat->name }}" class="img-fluid rounded-circle">
                                    @else
                                    <i class="icon-folder text-dark fs-20"></i>
                                    @endif
                                </div>
                                <div>
                                    <h6 class="fs-14 fw-semibold mb-1">{{ $cat->name }}</h6>
                                    <p class="text-body fw-normal mb-0">{{ $cat->items->count() }} Menus</p>
                                </div>
                            </a>
                        </li>
                        @endforeach
                    </ul>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="pos-all-menu">
                            <div class="row g-2">
                                @foreach($categories as $cat)
                                @foreach($cat->items as $item)
                                <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-4 col-sm-6 pos-item-card" data-item-id="{{ $item->id }}" data-price="{{ $item->price }}" data-name="{{ $item->name }}" data-description="{{ e($item->description ?? '') }}" data-image="{{ $item->image ? asset('storage/' . $item->image) : '' }}" data-addons="{{ e(json_encode($item->addons ? $item->addons->map(fn($a) => ['addon_name' => $a->addon_name, 'price' => (float) $a->price])->values()->toArray() : [])) }}" data-variations="{{ e(json_encode($item->variations ? $item->variations->map(fn($v) => ['name' => $v->name, 'price' => (float) $v->price])->values()->toArray() : [])) }}" data-food-type="{{ $item->food_type ?? 'veg' }}">
                                    <div class="bg-white rounded border p-2 pos-item-card-inner">
                                        <div class="food-items position-relative mb-2 pos-item-click">
                                            @if($item->image ?? null)
                                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="img-fluid w-100 rounded">
                                            @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center pos-item-placeholder"><i class="icon-layout-list text-muted fs-20"></i></div>
                                            @endif
                                        </div>
                                        <div class="pos-item-click">
                                            <p class="fs-12 mb-1">{{ $item->category->name ?? '' }}</p>
                                            @php $ft = $item->food_type ?? 'veg'; @endphp
                                            <p class="mb-1 small"><i class="icon-square-dot {{ $ft === 'non_veg' ? 'text-danger' : ($ft === 'egg' ? 'text-warning' : 'text-success') }} me-1"></i>{{ $ft === 'non_veg' ? 'Non Veg' : ($ft === 'egg' ? 'Egg' : 'Veg') }}</p>
                                            <h6 class="fs-14 fw-semibold text-truncate mb-2">{{ $item->name }}</h6>
                                            <div class="d-flex align-items-center justify-content-between flex gap-2">
                                                <p class="mb-0 text-dark">{{ $currency_symbol }}{{ number_format($item->price ?? 0, 2) }}</p>
                                                <div class="quantity-control" data-pos-no-modal="1">
                                                    <button type="button" class="minus-btn"><i class="icon-minus"></i></button>
                                                    <input type="text" class="quantity-input" value="0" aria-label="Quantity" data-item-id="{{ $item->id }}">
                                                    <button type="button" class="add-btn"><i class="icon-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endforeach
                            </div>
                        </div>

                        @foreach($categories as $category)
                        <div class="tab-pane fade" id="pos-cat-{{ $category->id }}">
                            <div class="row g-2">
                                @foreach($category->items as $item)
                                <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-4 col-sm-6 pos-item-card" data-item-id="{{ $item->id }}" data-price="{{ $item->price }}" data-name="{{ $item->name }}" data-description="{{ e($item->description ?? '') }}" data-image="{{ $item->image ? asset('storage/' . $item->image) : '' }}" data-addons="{{ e(json_encode($item->addons ? $item->addons->map(fn($a) => ['addon_name' => $a->addon_name, 'price' => (float) $a->price])->values()->toArray() : [])) }}" data-variations="{{ e(json_encode($item->variations ? $item->variations->map(fn($v) => ['name' => $v->name, 'price' => (float) $v->price])->values()->toArray() : [])) }}" data-food-type="{{ $item->food_type ?? 'veg' }}">
                                    <div class="bg-white rounded border p-2 pos-item-card-inner">
                                        <div class="food-items position-relative mb-2 pos-item-click">
                                            @if($item->image ?? null)
                                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="img-fluid w-100 rounded">
                                            @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center pos-item-placeholder"><i class="icon-layout-list text-muted fs-20"></i></div>
                                            @endif
                                        </div>
                                        <div class="pos-item-click">
                                            <p class="fs-12 mb-1">{{ $category->name }}</p>
                                            @php $ft = $item->food_type ?? 'veg'; @endphp
                                            <p class="mb-1 small"><i class="icon-square-dot {{ $ft === 'non_veg' ? 'text-danger' : ($ft === 'egg' ? 'text-warning' : 'text-success') }} me-1"></i>{{ $ft === 'non_veg' ? 'Non Veg' : ($ft === 'egg' ? 'Egg' : 'Veg') }}</p>
                                            <h6 class="fs-14 fw-semibold text-truncate mb-2">{{ $item->name }}</h6>
                                            <div class="d-flex align-items-center justify-content-between flex gap-2">
                                                <p class="mb-0 text-dark">{{ $currency_symbol }}{{ number_format($item->price ?? 0, 2) }}</p>
                                                <div class="quantity-control" data-pos-no-modal="1">
                                                    <button type="button" class="minus-btn"><i class="icon-minus"></i></button>
                                                    <input type="text" class="quantity-input" value="0" aria-label="Quantity" data-item-id="{{ $item->id }}">
                                                    <button type="button" class="add-btn"><i class="icon-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
                    @endif

                {{-- Desktop cart panel --}}
                <div class="col-lg-4 pos-right-desktop">
                    <div class="pos-right" id="pos-right" data-customer-store-url="{{ route('customer.store') }}">

                        <!-- Title (theme: Order # / date) -->
                        <div class="p-3 d-flex align-items-center justify-content-between flex-wrap border-bottom">
                            <h6 class="mb-0">Order #<span id="pos-order-display-id">{{ str_pad((($recentOrders ?? collect())->count() + 1), 5, '0', STR_PAD_LEFT) }}</span></h6>
                            <p class="mb-0">{{ now()->format('d M, Y, h:i A') }}</p>
                        </div>

                        <!-- Tabs  -->
                        <div class="item border-bottom">
                            <!-- Tabs -->
                            <ul class="nav nav-tabs nav-tabs-solid border-0 mb-3 align-items-center justify-content-between flex-wrap gap-1 pos-tab" role="tablist">
                                <li class="nav-item flex-fill">
                                    <a href="#order-tab1" class="nav-link active justify-content-center shadow-sm" data-bs-toggle="tab"><i class="icon-wine"></i>Dine In</a>
                                </li>
                                <li class="nav-item flex-fill">
                                    <a href="#order-tab2" class="nav-link justify-content-center shadow-sm" data-bs-toggle="tab"><i class="icon-shopping-bag"></i>Take Away</a>
                                </li>
                                <li class="nav-item flex-fill">
                                    <a href="#order-tab3" class="nav-link flex-fill justify-content-center shadow-sm" data-bs-toggle="tab"><i class="icon-check-check"></i>Delivery</a>
                                </li>
                                <li class="nav-item flex-fill">
                                    <a href="#order-tab4" class="nav-link flex-fill justify-content-center shadow-sm" data-bs-toggle="tab"><i class="icon-land-plot"></i>Table</a>
                                </li>
                            </ul>

                            <div class="tab-content">

                                <!-- TAb 1: Dine In (theme: Waiter, Table, Select Customer + plus) -->
                                <div class="tab-pane show active" id="order-tab1">
                                    <div class="row g-2">
                                        <div class="col-lg-5">
                                            <label class="form-label small mb-1">Waiter</label>
                                            <select class="form-select pos-waiter-select" id="pos-waiter-dinein" name="waiter_id">
                                                <option value="">Waiter</option>
                                                @foreach($waiters ?? [] as $w)
                                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label small mb-1">Customer</label>
                                            <div class="input-item pos-customer-row d-flex align-items-center gap-2">
                                                <select class="form-select pos-customer-select flex-grow-1" data-tab="order-tab1">
                                                    <option value="" data-name="">Select Customer</option>
                                                    <option value="" data-name="">Walk-in</option>
                                                    @foreach($customers ?? [] as $c)
                                                    <option value="{{ $c->id }}" data-name="{{ e($c->name) }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-primary btn-icon flex-shrink-0 pos-add-customer-trigger" title="Add new customer"><i class="icon-plus"></i></button>
                                                <button type="button" class="btn btn-icon btn-white border flex-shrink-0" data-bs-toggle="modal" data-bs-target="#edit_customer" title="Edit customer"><i class="icon-pencil-line"></i></button>
                                            </div>
                                            <input type="hidden" class="pos-customer-name" name="customer_name" value="">
                                        </div>
                                        <div class="col-lg-12">
                                            <label class="form-label small mb-1">Table <span class="text-danger">*</span></label>
                                            <select class="form-select pos-table-select" id="pos-table-dinein" name="restaurant_table_id">
                                                <option value="">Select Table</option>
                                                @foreach($tables ?? [] as $t)
                                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- TAb 2: Take Away -->
                                <div class="tab-pane show" id="order-tab2">
                                    <div class="row g-2">
                                        <div class="col-lg-11">
                                            <label class="form-label small mb-1">Customer</label>
                                            <div class="input-item pos-customer-row d-flex align-items-center gap-2">
                                                <select class="form-select pos-customer-select flex-grow-1" data-tab="order-tab2">
                                                    <option value="" data-name="">Select Customer</option>
                                                    <option value="" data-name="">Walk-in</option>
                                                    @foreach($customers ?? [] as $c)
                                                    <option value="{{ $c->id }}" data-name="{{ e($c->name) }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-primary btn-icon flex-shrink-0 pos-add-customer-trigger" title="Add new customer"><i class="icon-plus"></i></button>
                                                <button type="button" class="btn btn-icon btn-white border flex-shrink-0" data-bs-toggle="modal" data-bs-target="#edit_customer" title="Edit customer"><i class="icon-pencil-line"></i></button>
                                            </div>
                                            <input type="hidden" class="pos-customer-name" name="customer_name" value="">
                                        </div>
                                        <div class="col-lg-1"></div>
                                    </div>
                                </div>

                                <!-- TAb 3: Delivery -->
                                <div class="tab-pane show" id="order-tab3">
                                    <div class="row g-2">
                                        <div class="col-lg-11">
                                            <label class="form-label small mb-1">Customer</label>
                                            <div class="input-item pos-customer-row d-flex align-items-center gap-2">
                                                <select class="form-select pos-customer-select flex-grow-1" data-tab="order-tab3">
                                                    <option value="" data-name="">Select Customer</option>
                                                    <option value="" data-name="">Walk-in</option>
                                                    @foreach($customers ?? [] as $c)
                                                    <option value="{{ $c->id }}" data-name="{{ e($c->name) }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-primary btn-icon flex-shrink-0 pos-add-customer-trigger" title="Add new customer"><i class="icon-plus"></i></button>
                                                <button type="button" class="btn btn-icon btn-white border flex-shrink-0" data-bs-toggle="modal" data-bs-target="#edit_customer" title="Edit customer"><i class="icon-pencil-line"></i></button>
                                            </div>
                                            <input type="hidden" class="pos-customer-name" name="customer_name" value="">
                                        </div>
                                        <div class="col-lg-1"></div>
                                    </div>
                                </div>

                                <!-- TAb 4: Table (theme: Table, Select Customer + plus) -->
                                <div class="tab-pane show" id="order-tab4">
                                    <div class="row g-2">
                                        <div class="col-lg-5">
                                            <label class="form-label small mb-1">Table <span class="text-danger">*</span></label>
                                            <select class="form-select pos-table-select" id="pos-table-tab4">
                                                <option value="">Table</option>
                                                @foreach($tables ?? [] as $t)
                                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label small mb-1">Customer</label>
                                            <div class="input-item pos-customer-row d-flex align-items-center gap-2">
                                                <select class="form-select pos-customer-select flex-grow-1" data-tab="order-tab4">
                                                    <option value="" data-name="">Select Customer</option>
                                                    <option value="" data-name="">Walk-in</option>
                                                    @foreach($customers ?? [] as $c)
                                                    <option value="{{ $c->id }}" data-name="{{ e($c->name) }}">{{ $c->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-primary btn-icon flex-shrink-0 pos-add-customer-trigger" title="Add new customer"><i class="icon-plus"></i></button>
                                                <button type="button" class="btn btn-icon btn-white border flex-shrink-0" data-bs-toggle="modal" data-bs-target="#edit_customer" title="Edit customer"><i class="icon-pencil-line"></i></button>
                                            </div>
                                            <input type="hidden" class="pos-customer-name" name="customer_name" value="">
                                        </div>
                                        <div class="col-lg-1"></div>
                                    </div>
                                </div>

                                <!-- New customer form (shown when "+ New customer" is selected) -->
                                <div id="pos-new-customer-form" class="mt-3 p-3 border rounded bg-light d-none">
                                    <h6 class="mb-2">Add new customer</h6>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm" id="pos-new-customer-name" placeholder="Name *" required>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control form-control-sm" id="pos-new-customer-phone" placeholder="Phone">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="email" class="form-control form-control-sm" id="pos-new-customer-email" placeholder="Email">
                                        </div>
                                        <div class="col-12">
                                            <button type="button" class="btn btn-sm btn-primary" id="pos-add-customer-btn">Add customer</button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="pos-cancel-new-customer">Cancel</button>
                                        </div>
                                    </div>
                                    <p class="small text-muted mb-0 mt-1" id="pos-new-customer-msg"></p>
                                </div>

                            </div>
                        </div>

                        <!-- Ordered Item (dynamic cart) -->
                        <div class="p-3 border-bottom">
                            <div class="d-flex align-items-center justify-content-between mb-3 gap-2 flex-wrap">
                                <h6 class="mb-0">Ordered Menus</h6>
                                <p class="mb-0 d-flex align-items-center text-dark">Total Menus : <span id="pos-total-menus" class="d-flex align-items-center justify-content-center fs-14 btn btn-icon btn-xs rounded-circle border flex-shrink-0 ms-1 text-dark">0</span></p>
                            </div>
                            <div id="pos-cart-items" class="mb-3 min-height-cart">
                                <p class="text-muted small mb-0 text-center py-3" id="pos-cart-empty">Add items from the menu using + button.</p>
                            </div>
                            <div class="price-item d-none" id="pos-cart-summary">
                                <h6 class="mb-2">Payment Summary</h6>
                                <p class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0">Sub Total <span id="pos-subtotal" class="fw-medium text-dark">{{ $currency_symbol }}0.00</span></p>
                                <p class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0"><span id="pos-tax-label">{{ $tax_name ?? 'Tax' }} ({{ $tax_rate ?? 0 }}%)</span> <span id="pos-tax" class="fw-medium text-dark">{{ $currency_symbol }}0.00</span></p>
                                <p class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-2 mt-2">Discount <span id="pos-discount" class="fw-medium text-dark">{{ $currency_symbol }}0.00</span></p>
                                <div class="mb-2" id="pos-coupon-row">
                                    <div class="input-group input-group-sm mb-1" id="pos-coupon-apply-wrap">
                                        <input type="text" class="form-control" id="pos-coupon-code" placeholder="Coupon code" aria-label="Coupon code" autocomplete="off">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="pos-coupon-apply">Apply</button>
                                    </div>
                                    <div class="d-none small mt-1 mb-1" id="pos-coupon-applied-wrap">
                                        <span class="text-success fw-medium" id="pos-coupon-applied-label">Applied: </span>
                                        <button type="button" class="btn btn-link btn-sm p-0 ms-1 text-danger" id="pos-coupon-remove">Remove</button>
                                    </div>
                                    <p class="small text-danger mb-0 d-none" id="pos-coupon-error"></p>
                                </div>
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text">Discount ({{ $currency_symbol }})</span>
                                    <input type="number" step="0.01" min="0" id="pos-discount-input" class="form-control" placeholder="0" value="0" aria-label="Discount amount">
                                </div>
                                <p class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0 pt-1 border-top"><strong>Amount to Pay</strong> <span id="pos-amount-paid" class="fw-semibold text-dark">{{ $currency_symbol }}0.00</span></p>
                                <div class="input-group input-group-sm mt-2 mb-1">
                                    <span class="input-group-text">Received ({{ $currency_symbol }})</span>
                                    <input type="number" step="0.01" min="0" id="pos-received-input" class="form-control" placeholder="0" value="0" aria-label="Received amount">
                                </div>
                                <p class="fs-14 fw-normal d-flex align-items-center justify-content-between mb-0 mt-2"><strong>Change / Balance due</strong> <span id="pos-change" class="fw-semibold">{{ $currency_symbol }}0.00</span></p>
                            </div>
                        </div>

                        <!-- Place Order form (action/method set by JS when editing) -->
                        <form id="pos-order-form" action="{{ isset($editOrder) ? route('orders.update', $editOrder) : route('orders.store') }}" method="POST" class="d-none">
                            @csrf
                            @if(isset($editOrder))
                            @method('PUT')
                            @endif
                            <input type="hidden" name="order_type" id="pos-order-type" value="dine_in">
                            <input type="hidden" name="restaurant_table_id" id="pos-form-table-id" value="">
                            <input type="hidden" name="customer_id" id="pos-form-customer-id" value="">
                            <input type="hidden" name="customer_name" id="pos-form-customer-name" value="">
                            <input type="hidden" name="coupon_id" id="pos-form-coupon-id" value="">
                            <input type="hidden" name="discount_amount" id="pos-form-discount" value="0">
                            <input type="hidden" name="received_amount" id="pos-form-received" value="">
                        </form>
                        <div class="p-3">
                            <button type="button" class="btn btn-primary w-100 mb-4" id="pos-place-order-btn">{{ isset($editOrder) ? 'Update Order' : 'Place an Order' }}</button>
                            <!-- start row -->
                            <div class="row g-3 pos-action-row">
                                <div class="col-sm-4">
                                    <a href="#" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#pos-print-receipt"><i class="icon-printer"></i>Print</a>
                                </div>
                                <div class="col-sm-4">
                                    <a href="#" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#pos-view-invoices"><i class="icon-file-chart-column"></i>Invoice</a>
                                </div>
                                <div class="col-sm-4">
                                    <a href="#" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#pos-draft-details"><i class="icon-files"></i>Draft</a>
                                </div>
                                <div class="col-sm-4">
                                    <a href="#" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#pos-cancel-modal"><i class="icon-x"></i>Cancel</a>
                                </div>
                                <div class="col-sm-4">
                                    <a href="#" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#pos-void-modal"><i class="icon-zap"></i>Void</a>
                                </div>
                                <div class="col-sm-4">
                                    <a href="#" class="btn btn-outline-light btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#pos-transactions-details"><i class="icon-file-chart-line"></i>Transactions</a>
                                </div>
                            </div>
                            <!-- end row -->

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            {{-- Bottom spacer for mobile sticky bar --}}
            <div class="pos-bottom-spacer"></div>

        </div>
        <!-- End Content-->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

    <!-- POS Product Details Modal (customize then Add to Cart) -->
    <div class="modal fade" id="pos-item-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title" id="pos-modal-title">Item</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="bg-light p-3 rounded">
                                <img id="pos-modal-image" src="" alt="" class="img-fluid w-100 rounded" style="max-height:220px;object-fit:cover;">
                                <div id="pos-modal-image-placeholder" class="d-none bg-light rounded d-flex align-items-center justify-content-center" style="height:220px"><i class="icon-layout-list text-muted fs-24"></i></div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <p id="pos-modal-description" class="text-muted small mb-3"></p>
                            <div class="mb-3 pb-3 border-bottom" id="pos-modal-sizes-wrap">
                                <h6 class="fw-semibold mb-2">Sizes</h6>
                                <div class="d-flex flex-wrap gap-2 size-group" id="pos-modal-sizes">
                                    <!-- Filled by JS from data-variations -->
                                </div>
                            </div>
                            <div class="mb-3 pb-3 border-bottom">
                                <h6 class="fw-semibold mb-2">Add-ons & Upgrades</h6>
                                <div class="row g-2" id="pos-modal-addons">
                                    <p class="small text-muted mb-0">No add-ons</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h5 class="mb-0">Total <span id="pos-modal-total">{{ $currency_symbol }}0.00</span></h5>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="quantity-control">
                                        <button type="button" class="minus-btn" id="pos-modal-qty-minus"><i class="icon-minus"></i></button>
                                        <input type="text" class="quantity-input" id="pos-modal-qty" value="1" aria-label="Quantity">
                                        <button type="button" class="add-btn" id="pos-modal-qty-plus"><i class="icon-plus"></i></button>
                                    </div>
                                    <button type="button" class="btn btn-primary d-flex align-items-center gap-2" id="pos-modal-add-to-cart"><i class="icon-shopping-bag"></i> Add to Cart</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- POS: Print Receipt (current cart) - content filled by JS --}}
    <div class="modal fade" id="pos-print-receipt" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Print Receipt</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pos-print-receipt-body py-3">
                    <p class="text-muted small mb-0">Cart is empty. Add items to print receipt.</p>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-between flex-nowrap gap-2 border-0 pt-0">
                    <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary w-100 m-0" id="pos-print-receipt-btn"><i class="icon-printer me-1"></i>Print</button>
                </div>
            </div>
        </div>
    </div>

    {{-- POS: Invoice list (completed orders) --}}
    <div class="modal fade" id="pos-view-invoices" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Invoices</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover border">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Order No</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactionOrders ?? [] as $order)
                                <tr>
                                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer_name ?: 'Walk-in' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $order->order_type ?? '')) }}</td>
                                    <td>{{ $currency_symbol }}{{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <a href="{{ route('invoice-details', $order) }}" target="_blank" class="btn btn-icon btn-sm btn-white rounded-circle me-1" title="View invoice"><i class="icon-eye"></i></a>
                                        <a href="{{ route('invoice-details', $order) }}?print=1" target="_blank" class="btn btn-icon btn-sm btn-white rounded-circle" title="Print"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No completed orders yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- POS: Draft orders (pending/active) --}}
    <div class="modal fade" id="pos-draft-details" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Draft Orders</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover border">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Ref</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($draftOrders ?? [] as $order)
                                <tr>
                                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer_name ?: 'Walk-in' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $order->order_type ?? '')) }}</td>
                                    <td>{{ $currency_symbol }}{{ number_format($order->total, 2) }}</td>
                                    <td><span class="badge bg-primary">{{ ucfirst($order->status) }}</span></td>
                                    <td>
                                        <a href="{{ route('invoice-details', $order) }}" target="_blank" class="btn btn-icon btn-sm btn-white rounded-circle me-1" title="View"><i class="icon-eye"></i></a>
                                        <a href="{{ route('invoice-details', $order) }}?print=1" target="_blank" class="btn btn-icon btn-sm btn-white rounded-circle" title="Print"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">No draft orders.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- POS: Cancel (clear cart) --}}
    <div class="modal fade" id="pos-cancel-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-warning-subtle d-flex align-items-center justify-content-center mx-auto">
                            <i class="icon-x fs-24 text-warning"></i>
                        </span>
                    </div>
                    <h4 class="mb-1">Cancel Order</h4>
                    <p class="mb-4">Clear current cart? This cannot be undone.</p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning w-100" id="pos-cancel-cart-btn">Clear Cart</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- POS: Void (clear cart) --}}
    <div class="modal fade" id="pos-void-modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-4">
                        <span class="avatar avatar-xxl rounded-circle bg-danger-subtle d-flex align-items-center justify-content-center mx-auto">
                            <i class="icon-zap fs-24 text-danger"></i>
                        </span>
                    </div>
                    <h4 class="mb-1">Void</h4>
                    <p class="mb-4">Void and clear current cart?</p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light w-100" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger w-100" id="pos-void-cart-btn">Void & Clear</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- POS: Transactions (completed sales) --}}
    <div class="modal fade" id="pos-transactions-details" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h4 class="modal-title">Transactions</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover border">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Ref</th>
                                    <th>Customer</th>
                                    <th>Type</th>
                                    <th>Grand Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactionOrders ?? [] as $order)
                                <tr>
                                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->customer_name ?: 'Walk-in' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $order->order_type ?? '')) }}</td>
                                    <td>{{ $currency_symbol }}{{ number_format($order->total, 2) }}</td>
                                    <td>
                                        <a href="{{ route('invoice-details', $order) }}" target="_blank" class="btn btn-icon btn-sm btn-white rounded-circle me-1" title="View"><i class="icon-eye"></i></a>
                                        <a href="{{ route('invoice-details', $order) }}?print=1" target="_blank" class="btn btn-icon btn-sm btn-white rounded-circle" title="Print"><i class="icon-printer"></i></a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">No transactions yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($editOrder) && $editOrder)
    @php
        $posEditData = [
            'order_type' => $editOrder->order_type ?? 'dine_in',
            'restaurant_table_id' => $editOrder->restaurant_table_id,
            'customer_name' => $editOrder->customer_name ?? '',
            'items' => $editOrder->items->map(fn($i) => [
                'item_id' => $i->item_id,
                'item_name' => $i->item_name,
                'unit_price' => (float) $i->unit_price,
                'quantity' => (int) $i->quantity,
            ])->values()->toArray(),
        ];
    @endphp
    <script>
    window.posEditOrder = @json($posEditData);
    </script>
    @endif

    {{-- ===== MOBILE: Sticky Cart Bar ===== --}}
    <div class="pos-mobile-cart-bar" id="pos-mobile-cart-bar" data-bs-toggle="offcanvas" data-bs-target="#pos-cart-offcanvas" style="cursor:pointer">
        <div class="cart-bar-left">
            <span class="cart-bar-badge" id="pos-mobile-item-count">0 items</span>
            <span class="cart-bar-total" id="pos-mobile-total">{{ $currency_symbol }}0.00</span>
        </div>
        <span class="cart-bar-btn">View Cart →</span>
    </div>

    {{-- ===== MOBILE: Cart Offcanvas (Bottom Sheet) ===== --}}
    <div class="offcanvas offcanvas-bottom pos-cart-offcanvas" tabindex="-1" id="pos-cart-offcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Your Order</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0" id="pos-cart-offcanvas-body">
            {{-- Cloned cart content will be mirrored here --}}
        </div>
    </div>

@push('scripts')
<script>
(function(){
    'use strict';
    // Only run on mobile
    if (window.innerWidth >= 992) return;

    const desktopRight = document.getElementById('pos-right');
    const offcanvasBody = document.getElementById('pos-cart-offcanvas-body');
    const mobileCountEl = document.getElementById('pos-mobile-item-count');
    const mobileTotalEl = document.getElementById('pos-mobile-total');
    const cartBar = document.getElementById('pos-mobile-cart-bar');

    if (!desktopRight || !offcanvasBody) return;

    // Move the desktop cart content into the offcanvas on mobile
    offcanvasBody.appendChild(desktopRight);
    desktopRight.style.display = '';

    // Sync cart counts to the sticky bar
    function syncCartBar() {
        var menusEl = document.getElementById('pos-total-menus');
        var amountEl = document.getElementById('pos-amount-paid');
        var count = menusEl ? parseInt(menusEl.textContent, 10) || 0 : 0;
        var total = amountEl ? amountEl.textContent.trim() : '';

        mobileCountEl.textContent = count + (count === 1 ? ' item' : ' items');
        if (total) mobileTotalEl.textContent = total;

        // Show/hide bar based on cart content
        if (count > 0) {
            cartBar.style.display = 'flex';
        }
    }

    // Watch for changes in the cart
    var observer = new MutationObserver(syncCartBar);
    var cartItems = document.getElementById('pos-cart-items');
    if (cartItems) observer.observe(cartItems, { childList: true, subtree: true, characterData: true });
    var totalMenus = document.getElementById('pos-total-menus');
    if (totalMenus) observer.observe(totalMenus, { childList: true, characterData: true, subtree: true });
    var amountPaid = document.getElementById('pos-amount-paid');
    if (amountPaid) observer.observe(amountPaid, { childList: true, characterData: true, subtree: true });

    // Initial sync
    syncCartBar();

    // Also listen for resize in case user rotates device
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
            // Move cart back to desktop position
            var desktopCol = document.querySelector('.pos-right-desktop');
            if (desktopCol && desktopRight.parentElement !== desktopCol) {
                desktopCol.appendChild(desktopRight);
            }
        } else {
            if (desktopRight.parentElement !== offcanvasBody) {
                offcanvasBody.appendChild(desktopRight);
            }
        }
    });
})();
</script>
@endpush

@endsection
