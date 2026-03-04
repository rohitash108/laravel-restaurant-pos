@extends('layout.order-by-qr')

@section('content')

{{-- ========== HEADER ========== --}}
<div class="qr-header">
    <h1>{{ $restaurant->name }}</h1>
    <p class="mb-0"><span class="table-badge">🍽 Table {{ $table->name }}</span></p>
</div>

{{-- ========== PAYMENT QR (if configured) ========== --}}
@if($restaurant->payment_qr)
    <div style="padding: 0.75rem 1rem 0;">
        <div style="
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid rgba(148,163,184,0.2);
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        ">
            <div style="
                flex-shrink: 0;
                width: 80px;
                height: 80px;
                border-radius: 14px;
                padding: 5px;
                background: linear-gradient(135deg, #ef4444, #7c3aed);
                display: flex;
                align-items: center;
                justify-content: center;
            ">
                <img src="{{ asset('storage/' . $restaurant->payment_qr) }}"
                     alt="Payment QR"
                     style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px; background: #fff;">
            </div>
            <div style="flex: 1; min-width: 0;">
                <p style="font-size: 0.92rem; font-weight: 700; margin-bottom: 0.2rem; color: #1e293b;">
                    Scan & Pay
                </p>
                <p style="font-size: 0.76rem; margin: 0; color: #64748b; line-height: 1.5;">
                    After placing your order, <strong>scan this QR</strong> to pay. Show the payment to staff so they can mark your order as <strong>Paid</strong>.
                </p>
            </div>
        </div>
    </div>
@endif

{{-- ========== SEARCH ========== --}}
<div class="qr-search-wrap">
    <input type="text" id="menu-search" class="qr-search-input" placeholder="Search items..." autocomplete="off">
</div>

{{-- ========== CATEGORY PILLS ========== --}}
@php
    $activeCategories = $categories->filter(fn ($c) => $c->items->isNotEmpty());
@endphp

@if($activeCategories->isNotEmpty())
<nav class="category-pills" id="category-pills">
    @foreach($activeCategories as $cat)
        <a href="#" class="category-pill {{ $loop->first ? 'active' : '' }}" data-cat-id="{{ $cat->id }}" role="button">{{ $cat->name }}</a>
    @endforeach
</nav>
@endif

{{-- ========== ERROR ALERT ========== --}}
@if(session('error'))
    <div style="padding: 0.75rem 1rem;">
        <div style="background: #fef2f2; color: #dc2626; padding: 0.75rem 1rem; border-radius: 10px; font-size: 0.875rem;">{{ session('error') }}</div>
    </div>
@endif

{{-- ========== MENU ITEMS ========== --}}
<form id="order-form" action="{{ route('order.by-qr.place') }}" method="POST">
    @csrf
    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
    <input type="hidden" name="restaurant_table_id" value="{{ $table->id }}">

    @if($activeCategories->isNotEmpty())
        @foreach($activeCategories as $category)
            <div class="menu-section" id="cat-{{ $category->id }}">
                <h3 class="menu-section-title">{{ $category->name }}</h3>

                @foreach($category->items as $item)
                    @php
                        $itemAddons = $item->addons ?? collect();
                        $itemVariations = $item->variations ?? collect();
                        $hasOptions = $itemAddons->isNotEmpty() || $itemVariations->isNotEmpty();
                    @endphp
                    <div class="menu-item" data-item-id="{{ $item->id }}"
                        data-price="{{ $item->price }}"
                        data-name="{{ e($item->name) }}"
                        data-has-options="{{ $hasOptions ? '1' : '0' }}"
                        data-addons="{{ e($itemAddons->map(fn($a) => ['addon_name' => $a->addon_name, 'price' => (float) $a->price])->values()->toJson()) }}"
                        data-variations="{{ e($itemVariations->map(fn($v) => ['name' => $v->name, 'price' => (float) $v->price])->values()->toJson()) }}">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}" class="menu-item-img" loading="lazy">
                        @else
                            <div class="menu-item-placeholder">🍴</div>
                        @endif

                        <div class="menu-item-info">
                            <div class="menu-item-name">
                                @if($item->food_type === 'veg')
                                    <span class="food-dot veg" title="Vegetarian"></span>
                                @elseif($item->food_type === 'non-veg')
                                    <span class="food-dot non-veg" title="Non-Vegetarian"></span>
                                @endif
                                {{ $item->name }}
                            </div>
                            @if($item->description)
                                <p class="menu-item-desc">{{ $item->description }}</p>
                            @endif
                            @if($itemVariations->isNotEmpty())
                                <div class="menu-item-variations">
                                    <span class="menu-item-options-label">Variations:</span>
                                    @foreach($itemVariations as $v)
                                        <span class="menu-item-option-pill">{{ $v->name }}{{ (float)$v->price != 0 ? ' ' . ($currency_symbol ?? '₹') . number_format($v->price, 2) : '' }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @if($itemAddons->isNotEmpty())
                                <div class="menu-item-addons">
                                    <span class="menu-item-options-label">Add-ons:</span>
                                    @foreach($itemAddons as $a)
                                        <span class="menu-item-option-pill">{{ $a->addon_name }}{{ (float)$a->price != 0 ? ' +' . ($currency_symbol ?? '₹') . number_format($a->price, 2) : '' }}</span>
                                    @endforeach
                                </div>
                            @endif
                            <div class="menu-item-bottom">
                                <span class="menu-item-price">{{ $currency_symbol ?? '₹' }}{{ number_format($item->price, 2) }}</span>

                                {{-- ADD / QTY CONTROL --}}
                                <button type="button" class="add-btn {{ $hasOptions ? 'add-btn-options' : '' }}" id="add-btn-{{ $item->id }}"
                                    data-item-id="{{ $item->id }}"
                                    data-price="{{ $item->price }}"
                                    data-name="{{ e($item->name) }}"
                                    data-has-options="{{ $hasOptions ? '1' : '0' }}">ADD</button>
                                <div class="qty-control qty-control-inline" id="qty-ctrl-{{ $item->id }}" style="display:none;" data-item-id="{{ $item->id }}">
                                    <button type="button" class="qty-btn minus-btn" data-item-id="{{ $item->id }}">−</button>
                                    <span class="qty-val" id="qty-val-{{ $item->id }}">0</span>
                                    <button type="button" class="qty-btn plus-btn" data-item-id="{{ $item->id }}">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <h3>No items available</h3>
            <p>The menu is being updated. Please check back shortly.</p>
        </div>
    @endif

    <div class="page-bottom-spacer"></div>

    {{-- Hidden inputs for order items (populated by JS on submit) --}}
    <div id="order-items-container"></div>

    {{-- Customer name, notes, coupon (inside the form) --}}
    <input type="hidden" name="customer_name" id="hidden-customer-name">
    <input type="hidden" name="notes" id="hidden-notes">
    <input type="hidden" name="coupon_id" id="hidden-coupon-id" value="">
</form>

{{-- ========== STICKY CART BAR ========== --}}
<div class="cart-bar" id="cart-bar">
    <div class="cart-bar-left">
        <span class="cart-badge" id="cart-count">0 items</span>
        <span class="cart-bar-total" id="cart-total">{{ $currency_symbol ?? '₹' }}0.00</span>
    </div>
    <button type="button" class="cart-bar-btn" id="view-cart-btn">View Cart →</button>
</div>

{{-- ========== CART BOTTOM SHEET ========== --}}
<div class="sheet-overlay" id="sheet-overlay"></div>
<div class="cart-sheet" id="cart-sheet">
    <div class="cart-sheet-handle"></div>
    <div class="cart-sheet-header">
        <h2>Your Order</h2>
        <button type="button" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--qr-text-muted);" id="close-sheet-btn">✕</button>
    </div>
    <div class="cart-sheet-body" id="cart-sheet-body">
        <p style="color:var(--qr-text-muted);text-align:center;">Your cart is empty</p>
    </div>
    <div class="cart-sheet-footer">
        <div style="margin-bottom:0.75rem;">
            <label style="font-size:0.8125rem;font-weight:500;display:block;margin-bottom:0.25rem;">Your Name (optional)</label>
            <input type="text" id="sheet-customer-name" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--qr-border);border-radius:var(--qr-radius-sm);font-size:0.875rem;font-family:inherit;" placeholder="Name">
        </div>
        <div style="margin-bottom:0.75rem;">
            <label style="font-size:0.8125rem;font-weight:500;display:block;margin-bottom:0.25rem;">Special Requests</label>
            <textarea id="sheet-notes" rows="2" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--qr-border);border-radius:var(--qr-radius-sm);font-size:0.875rem;font-family:inherit;resize:none;" placeholder="Any allergies or preferences?"></textarea>
        </div>
        <div style="margin-bottom:1rem;" id="qr-coupon-wrap">
            <label style="font-size:0.8125rem;font-weight:500;display:block;margin-bottom:0.25rem;">Coupon code</label>
            <div style="display:flex;gap:0.35rem;">
                <input type="text" id="qr-coupon-code" style="flex:1;padding:0.5rem 0.75rem;border:1px solid var(--qr-border);border-radius:var(--qr-radius-sm);font-size:0.875rem;font-family:inherit;" placeholder="Enter code">
                <button type="button" id="qr-coupon-apply" style="padding:0.5rem 1rem;background:var(--qr-primary);color:#fff;border:none;border-radius:var(--qr-radius-sm);font-size:0.875rem;font-weight:600;cursor:pointer;">Apply</button>
            </div>
            <div id="qr-coupon-applied" style="display:none;margin-top:0.35rem;font-size:0.8rem;color:var(--qr-success);">
                <span id="qr-coupon-applied-label"></span>
                <button type="button" id="qr-coupon-remove" style="background:none;border:none;color:var(--qr-danger);cursor:pointer;margin-left:0.5rem;font-size:0.8rem;">Remove</button>
            </div>
            <p id="qr-coupon-error" style="display:none;margin-top:0.35rem;font-size:0.8rem;color:var(--qr-danger);"></p>
        </div>
        <button type="button" class="place-order-btn" id="place-order-btn" disabled>Place Order</button>
    </div>
</div>

{{-- ========== ITEM OPTIONS MODAL (variations & addons) ========== --}}
<div class="sheet-overlay" id="options-overlay"></div>
<div class="cart-sheet qr-options-sheet" id="item-options-sheet">
    <div class="cart-sheet-handle"></div>
    <div class="cart-sheet-header">
        <h2 id="options-sheet-title">Customize</h2>
        <button type="button" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:var(--qr-text-muted);" id="close-options-sheet-btn">✕</button>
    </div>
    <div class="cart-sheet-body" id="item-options-body">
        <div id="options-variations-wrap" style="margin-bottom:1rem;display:none;">
            <label style="font-size:0.8125rem;font-weight:600;display:block;margin-bottom:0.5rem;">Choose option</label>
            <div id="options-variations-list"></div>
        </div>
        <div id="options-addons-wrap" style="margin-bottom:1rem;display:none;">
            <label style="font-size:0.8125rem;font-weight:600;display:block;margin-bottom:0.5rem;">Add-ons</label>
            <div id="options-addons-list"></div>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:0.75rem;border-top:1px solid var(--qr-border);">
            <span style="font-weight:700;font-size:1rem;">Total</span>
            <span id="options-sheet-total" style="font-weight:800;font-size:1.1rem;color:var(--qr-primary);">₹0.00</span>
        </div>
    </div>
    <div class="cart-sheet-footer">
        <button type="button" class="place-order-btn" id="options-add-to-cart-btn">Add to cart</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const currency = '{{ $currency_symbol ?? "₹" }}';
    const qrCoupons = @json($coupons ?? []);
    const cart = {};
    let optionsLineIndex = 0;
    let appliedCoupon = null;

    // ===== DOM References =====
    const cartBar = document.getElementById('cart-bar');
    const cartCount = document.getElementById('cart-count');
    const cartTotal = document.getElementById('cart-total');
    const cartSheetBody = document.getElementById('cart-sheet-body');
    const cartSheet = document.getElementById('cart-sheet');
    const sheetOverlay = document.getElementById('sheet-overlay');
    const placeOrderBtn = document.getElementById('place-order-btn');
    const orderForm = document.getElementById('order-form');
    const optionsSheet = document.getElementById('item-options-sheet');
    const optionsOverlay = document.getElementById('options-overlay');
    const optionsAddToCartBtn = document.getElementById('options-add-to-cart-btn');

    function getCartItemCount() {
        let count = 0;
        for (const k in cart) { if (cart[k].qty > 0) count += cart[k].qty; }
        return count;
    }

    function getCartTotal() {
        let total = 0;
        for (const k in cart) { if (cart[k].qty > 0) total += cart[k].qty * cart[k].unit_price; }
        return total;
    }

    function getDiscountAmount(subtotal) {
        if (!appliedCoupon || subtotal <= 0) return 0;
        if (appliedCoupon.discount_type === 'percentage') {
            return Math.round(subtotal * parseFloat(appliedCoupon.discount_amount) / 100 * 100) / 100;
        }
        return Math.min(parseFloat(appliedCoupon.discount_amount), subtotal);
    }

    function getQtyForItem(itemId) {
        let sum = 0;
        for (const k in cart) {
            if (cart[k].item_id == itemId && cart[k].qty > 0) sum += cart[k].qty;
        }
        return sum;
    }

    function updateUI() {
        const count = getCartItemCount();
        const subtotal = getCartTotal();
        const discount = getDiscountAmount(subtotal);
        const totalAfterDiscount = Math.max(0, subtotal - discount);
        cartCount.textContent = count + (count === 1 ? ' item' : ' items');
        cartTotal.textContent = currency + totalAfterDiscount.toFixed(2);
        cartBar.classList.toggle('visible', count > 0);

        document.getElementById('hidden-coupon-id').value = appliedCoupon ? appliedCoupon.id : '';

        let html = '';
        for (const k in cart) {
            if (cart[k].qty < 1) continue;
            const d = cart[k];
            const sub = (d.notes ? '<div class="cart-sheet-item-sub" style="font-size:0.75rem;color:var(--qr-text-muted);">' + escapeHtml(d.notes) + '</div>' : '') +
                '<div class="cart-sheet-item-sub">' + currency + parseFloat(d.unit_price).toFixed(2) + ' × ' + d.qty + '</div>';
            html += '<div class="cart-sheet-item">' +
                '<div><div class="cart-sheet-item-name">' + escapeHtml(d.name) + '</div>' + sub + '</div>' +
                '<div style="display:flex;align-items:center;gap:0.5rem;">' +
                '<div class="qty-control" style="height:28px;">' +
                '<button type="button" class="qty-btn sheet-minus" data-id="' + escapeHtml(k) + '" style="width:28px;font-size:0.875rem;">−</button>' +
                '<span class="qty-val" style="width:22px;font-size:0.75rem;">' + d.qty + '</span>' +
                '<button type="button" class="qty-btn sheet-plus" data-id="' + escapeHtml(k) + '" style="width:28px;font-size:0.875rem;">+</button>' +
                '</div>' +
                '<span style="font-weight:600;font-size:0.875rem;min-width:55px;text-align:right;">' + currency + (d.unit_price * d.qty).toFixed(2) + '</span>' +
                '</div></div>';
        }
        if (count === 0) {
            html = '<p style="color:var(--qr-text-muted);text-align:center;padding:1rem 0;">Your cart is empty</p>';
        } else {
            html += '<div class="cart-sheet-total"><span>Subtotal</span><span>' + currency + subtotal.toFixed(2) + '</span></div>';
            if (discount > 0 && appliedCoupon) {
                html += '<div class="cart-sheet-total" style="color:var(--qr-success);"><span>Discount (' + escapeHtml(appliedCoupon.code) + ')</span><span>- ' + currency + discount.toFixed(2) + '</span></div>';
            }
            html += '<div class="cart-sheet-total"><span>Total</span><span>' + currency + totalAfterDiscount.toFixed(2) + '</span></div>';
        }
        cartSheetBody.innerHTML = html;
        placeOrderBtn.disabled = count === 0;

        cartSheetBody.querySelectorAll('.sheet-minus').forEach(function (btn) {
            btn.addEventListener('click', function () { changeQty(this.dataset.id, -1); });
        });
        cartSheetBody.querySelectorAll('.sheet-plus').forEach(function (btn) {
            btn.addEventListener('click', function () { changeQty(this.dataset.id, 1); });
        });

        syncAllItemCards();
    }

    function changeQty(lineKey, delta) {
        if (!cart[lineKey]) return;
        cart[lineKey].qty = Math.max(0, cart[lineKey].qty + delta);
        if (cart[lineKey].qty === 0) delete cart[lineKey];
        syncAllItemCards();
        updateUI();
    }

    function syncAllItemCards() {
        document.querySelectorAll('.menu-item[data-item-id]').forEach(function (card) {
            const itemId = card.getAttribute('data-item-id');
            const hasOptions = card.getAttribute('data-has-options') === '1' || card.querySelector('.add-btn-options');
            const totalQty = getQtyForItem(itemId);
            const addBtn = card.querySelector('.add-btn');
            const qtyCtrl = card.querySelector('.qty-control-inline');
            const qtyVal = card.querySelector('.qty-val');
            if (hasOptions) {
                if (addBtn) addBtn.style.display = '';
                if (qtyCtrl) qtyCtrl.style.display = 'none';
                let badge = card.querySelector('.qr-cart-badge');
                if (totalQty > 0) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'qr-cart-badge';
                        badge.style.cssText = 'font-size:0.7rem;color:var(--qr-primary);font-weight:600;margin-left:0.25rem;';
                        if (addBtn && addBtn.parentNode) addBtn.parentNode.appendChild(badge);
                    }
                    badge.textContent = totalQty + ' in cart';
                } else if (badge) badge.remove();
            } else {
                const lineKey = String(itemId);
                const qty = cart[lineKey] ? cart[lineKey].qty : 0;
                if (addBtn) addBtn.style.display = qty > 0 ? 'none' : '';
                if (qtyCtrl) qtyCtrl.style.display = qty > 0 ? '' : 'none';
                if (qtyVal) qtyVal.textContent = qty;
            }
        });
    }

    // ===== ADD button =====
    document.querySelectorAll('.add-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const card = this.closest('.menu-item');
            const itemId = this.dataset.itemId;
            const basePrice = parseFloat(this.dataset.price);
            const name = this.dataset.name;
            const hasOptions = (card && card.getAttribute('data-has-options') === '1') || this.dataset.hasOptions === '1';
            const addonsJson = (card && card.getAttribute('data-addons')) || '[]';
            const variationsJson = (card && card.getAttribute('data-variations')) || '[]';
            let addons = [], variations = [];
            try {
                const unescapedAddons = (addonsJson + '').replace(/&quot;/g, '"').replace(/&#34;/g, '"');
                addons = JSON.parse(unescapedAddons);
            } catch (e) { addons = []; }
            try {
                const unescapedVariations = (variationsJson + '').replace(/&quot;/g, '"').replace(/&#34;/g, '"');
                variations = JSON.parse(unescapedVariations);
            } catch (e) { variations = []; }
            if (!Array.isArray(addons)) addons = [];
            if (!Array.isArray(variations)) variations = [];

            if (hasOptions && (addons.length > 0 || variations.length > 0)) {
                openOptionsSheet({ itemId: itemId, name: name, basePrice: basePrice, addons: addons, variations: variations });
            } else {
                const key = String(itemId);
                if (!cart[key]) cart[key] = { item_id: itemId, name: name, unit_price: basePrice, qty: 0, notes: null };
                cart[key].qty += 1;
                syncAllItemCards();
                updateUI();
            }
        });
    });

    document.querySelectorAll('.plus-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const itemId = this.dataset.itemId;
            const key = String(itemId);
            const card = this.closest('.menu-item');
            const basePrice = parseFloat(card.getAttribute('data-price'));
            const name = card.getAttribute('data-name');
            if (!cart[key]) cart[key] = { item_id: itemId, name: name, unit_price: basePrice, qty: 0, notes: null };
            cart[key].qty++;
            syncAllItemCards();
            updateUI();
        });
    });
    document.querySelectorAll('.minus-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            changeQty(String(this.dataset.itemId), -1);
        });
    });

    // ===== Item options sheet =====
    let currentOptions = null;
    function openOptionsSheet(opts) {
        currentOptions = opts;
        const titleEl = document.getElementById('options-sheet-title');
        const variationsWrap = document.getElementById('options-variations-wrap');
        const variationsList = document.getElementById('options-variations-list');
        const addonsWrap = document.getElementById('options-addons-wrap');
        const addonsList = document.getElementById('options-addons-list');
        if (titleEl) titleEl.textContent = opts.name;
        variationsWrap.style.display = opts.variations.length ? 'block' : 'none';
        addonsWrap.style.display = opts.addons.length ? 'block' : 'none';
        variationsList.innerHTML = '';
        addonsList.innerHTML = '';
        opts.variations.forEach(function (v, i) {
            const price = parseFloat(v.price) || 0;
            const pill = document.createElement('button');
            pill.type = 'button';
            pill.className = 'qr-option-pill' + (i === 0 ? ' active' : '');
            pill.textContent = (v.name || '') + (price > 0 ? ' (+' + currency + price.toFixed(2) + ')' : '');
            pill.dataset.price = price;
            pill.dataset.name = v.name || '';
            pill.addEventListener('click', function () {
                variationsList.querySelectorAll('.qr-option-pill').forEach(function (p) { p.classList.remove('active'); });
                this.classList.add('active');
                updateOptionsTotal();
            });
            variationsList.appendChild(pill);
        });
        opts.addons.forEach(function (a) {
            const row = document.createElement('div');
            row.className = 'qr-addon-row';
            const name = a.addon_name || a.name || '';
            const price = parseFloat(a.price) || 0;
            row.innerHTML = '<span>' + escapeHtml(name) + '</span><span>' + (price > 0 ? currency + price.toFixed(2) : '') + '</span>';
            row.dataset.price = price;
            row.dataset.name = name;
            row.addEventListener('click', function () {
                this.classList.toggle('active');
                updateOptionsTotal();
            });
            addonsList.appendChild(row);
        });
        updateOptionsTotal();
        optionsSheet.classList.add('open');
        optionsOverlay.classList.add('open');
    }

    function updateOptionsTotal() {
        if (!currentOptions) return;
        let total = currentOptions.basePrice;
        const variationsList = document.getElementById('options-variations-list');
        const addonsList = document.getElementById('options-addons-list');
        const activeVar = variationsList && variationsList.querySelector('.qr-option-pill.active');
        if (activeVar) total += parseFloat(activeVar.dataset.price) || 0;
        (addonsList ? addonsList.querySelectorAll('.qr-addon-row.active') : []).forEach(function (r) {
            total += parseFloat(r.dataset.price) || 0;
        });
        const totalEl = document.getElementById('options-sheet-total');
        if (totalEl) totalEl.textContent = currency + total.toFixed(2);
    }

    function closeOptionsSheet() {
        optionsSheet.classList.remove('open');
        optionsOverlay.classList.remove('open');
        currentOptions = null;
    }

    optionsOverlay.addEventListener('click', closeOptionsSheet);
    document.getElementById('close-options-sheet-btn').addEventListener('click', closeOptionsSheet);

    optionsAddToCartBtn.addEventListener('click', function () {
        if (!currentOptions) return;
        let unitPrice = currentOptions.basePrice;
        const parts = [];
        const variationsList = document.getElementById('options-variations-list');
        const addonsList = document.getElementById('options-addons-list');
        const activeVar = variationsList && variationsList.querySelector('.qr-option-pill.active');
        if (activeVar) {
            unitPrice += parseFloat(activeVar.dataset.price) || 0;
            parts.push(activeVar.dataset.name);
        }
        const selectedAddons = addonsList ? addonsList.querySelectorAll('.qr-addon-row.active') : [];
        selectedAddons.forEach(function (r) {
            unitPrice += parseFloat(r.dataset.price) || 0;
            parts.push(r.dataset.name);
        });
        const notes = parts.length ? parts.join(', ') : null;
        const lineKey = currentOptions.itemId + '_' + (++optionsLineIndex);
        cart[lineKey] = {
            item_id: currentOptions.itemId,
            name: currentOptions.name,
            unit_price: unitPrice,
            qty: 1,
            notes: notes
        };
        closeOptionsSheet();
        syncAllItemCards();
        updateUI();
    });

    // View Cart / Close Sheet
    document.getElementById('view-cart-btn').addEventListener('click', function () {
        cartSheet.classList.add('open');
        sheetOverlay.classList.add('open');
    });
    document.getElementById('close-sheet-btn').addEventListener('click', closeSheet);
    sheetOverlay.addEventListener('click', closeSheet);

    // Coupon Apply
    document.getElementById('qr-coupon-apply').addEventListener('click', function () {
        const code = (document.getElementById('qr-coupon-code').value || '').trim();
        const errEl = document.getElementById('qr-coupon-error');
        const appliedEl = document.getElementById('qr-coupon-applied');
        const labelEl = document.getElementById('qr-coupon-applied-label');
        errEl.style.display = 'none';
        appliedEl.style.display = 'none';
        if (!code) {
            errEl.textContent = 'Enter a coupon code';
            errEl.style.display = 'block';
            return;
        }
        const found = (qrCoupons || []).find(function (c) {
            return (c.code || '').trim().toLowerCase() === code.toLowerCase();
        });
        if (!found) {
            errEl.textContent = 'Invalid or expired coupon code';
            errEl.style.display = 'block';
            return;
        }
        appliedCoupon = { id: found.id, code: found.code, discount_type: found.discount_type, discount_amount: found.discount_amount };
        labelEl.textContent = 'Applied: ' + (found.code || '');
        appliedEl.style.display = 'block';
        updateUI();
    });

    document.getElementById('qr-coupon-remove').addEventListener('click', function () {
        appliedCoupon = null;
        document.getElementById('qr-coupon-code').value = '';
        document.getElementById('qr-coupon-applied').style.display = 'none';
        document.getElementById('qr-coupon-error').style.display = 'none';
        updateUI();
    });

    function closeSheet() {
        cartSheet.classList.remove('open');
        sheetOverlay.classList.remove('open');
    }

    // Place Order
    placeOrderBtn.addEventListener('click', function () {
        document.getElementById('hidden-customer-name').value = document.getElementById('sheet-customer-name').value;
        document.getElementById('hidden-notes').value = document.getElementById('sheet-notes').value;

        const container = document.getElementById('order-items-container');
        container.innerHTML = '';
        let i = 0;
        for (const k in cart) {
            if (cart[k].qty < 1) continue;
            const line = cart[k];
            container.innerHTML += '<input type="hidden" name="items[' + i + '][item_id]" value="' + escapeHtml(String(line.item_id)) + '">' +
                '<input type="hidden" name="items[' + i + '][quantity]" value="' + line.qty + '">' +
                '<input type="hidden" name="items[' + i + '][unit_price]" value="' + line.unit_price + '">' +
                (line.notes ? '<input type="hidden" name="items[' + i + '][notes]" value="' + (function(s){ return (s||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); })(line.notes) + '">' : '');
            i++;
        }
        if (i === 0) return;
        placeOrderBtn.disabled = true;
        placeOrderBtn.textContent = 'Placing order…';
        orderForm.submit();
    });

    // ===== Search: filter by item name/description only. Category = click pill only. =====
    const searchInput = document.getElementById('menu-search');

    function filterMenu() {
        var query = (searchInput && searchInput.value ? searchInput.value : '').trim();
        query = query.toLowerCase();
        var sections = document.querySelectorAll('.menu-section');
        var pills = document.querySelectorAll('.category-pill');

        if (query === '') {
            sections.forEach(function (sec) {
                sec.classList.remove('qr-section-hidden');
                sec.querySelectorAll('.menu-item').forEach(function (it) { it.classList.remove('qr-item-hidden'); });
            });
            pills.forEach(function (p) { p.classList.remove('qr-pill-hidden'); });
            return;
        }

        sections.forEach(function (sec) {
            var items = sec.querySelectorAll('.menu-item');
            var sectionHasVisible = false;
            items.forEach(function (item) {
                var nameEl = item.querySelector('.menu-item-name');
                var descEl = item.querySelector('.menu-item-desc');
                var itemName = (nameEl ? nameEl.textContent : '').replace(/\s+/g, ' ').trim().toLowerCase();
                var itemDesc = (descEl ? descEl.textContent : '').replace(/\s+/g, ' ').trim().toLowerCase();
                var match = itemName.indexOf(query) >= 0 || itemDesc.indexOf(query) >= 0;
                if (match) {
                    item.classList.remove('qr-item-hidden');
                    sectionHasVisible = true;
                } else {
                    item.classList.add('qr-item-hidden');
                }
            });
            if (sectionHasVisible) {
                sec.classList.remove('qr-section-hidden');
            } else {
                sec.classList.add('qr-section-hidden');
            }
        });

        pills.forEach(function (pill) {
            var catId = pill.getAttribute('data-cat-id');
            var section = document.getElementById('cat-' + catId);
            var hide = !section || section.classList.contains('qr-section-hidden');
            if (hide) {
                pill.classList.add('qr-pill-hidden');
            } else {
                pill.classList.remove('qr-pill-hidden');
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterMenu);
        searchInput.addEventListener('paste', function () { setTimeout(filterMenu, 0); });
    }

    // ===== Category Pills =====
    const pills = document.querySelectorAll('.category-pill');
    const headerEl = document.querySelector('.qr-header');
    const pillsNav = document.getElementById('category-pills');

    var searchWrapEl = document.querySelector('.qr-search-wrap');
    function scrollToCategorySection(sectionEl) {
        if (!sectionEl) return;
        var headerH = headerEl ? headerEl.offsetHeight : 0;
        var searchH = searchWrapEl ? searchWrapEl.offsetHeight : 0;
        var pillsH = pillsNav ? pillsNav.offsetHeight : 0;
        var offset = headerH + searchH + pillsH + 10;
        var sectionTop = sectionEl.getBoundingClientRect().top + window.pageYOffset;
        var scrollToY = sectionTop - offset;
        window.scrollTo({ top: Math.max(0, scrollToY), behavior: 'smooth' });
    }

    pills.forEach(function (pill) {
        pill.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            pills.forEach(function (p) { p.classList.remove('active'); });
            this.classList.add('active');
            var catId = this.getAttribute('data-cat-id');
            var target = document.getElementById('cat-' + catId);
            scrollToCategorySection(target);
        });
    });

    // Highlight active pill based on scroll position (only visible sections)
    if (pills.length > 0) {
        var sections = document.querySelectorAll('.menu-section');
        var ticking = false;
        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    var headerH = headerEl ? headerEl.offsetHeight : 0;
                    var searchH = searchWrapEl ? searchWrapEl.offsetHeight : 0;
                    var pillsH = pillsNav ? pillsNav.offsetHeight : 0;
                    var scrollOffset = headerH + searchH + pillsH + 20;
                    var current = '';
                    sections.forEach(function (sec) {
                        if (sec.classList.contains('qr-section-hidden')) return;
                        var top = sec.getBoundingClientRect().top + window.pageYOffset;
                        if (top - scrollOffset <= window.scrollY) {
                            current = (sec.id || '').replace('cat-', '');
                        }
                    });
                    if (current) {
                        pills.forEach(function (p) {
                            var isActive = p.getAttribute('data-cat-id') === current;
                            p.classList.toggle('active', isActive);
                        });
                    }
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    // Set pills sticky top based on header + search height
    var searchWrap = document.querySelector('.qr-search-wrap');
    if (headerEl && pillsNav) {
        var topOffset = headerEl.offsetHeight + (searchWrap ? searchWrap.offsetHeight : 0);
        pillsNav.style.top = topOffset + 'px';
    }

    function escapeHtml(s) {
        var div = document.createElement('div');
        div.textContent = s;
        return div.innerHTML;
    }

    // Init
    updateUI();
})();
</script>
@endpush
