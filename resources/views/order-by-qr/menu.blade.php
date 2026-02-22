@extends('layout.order-by-qr')

@section('content')

{{-- ========== HEADER ========== --}}
<div class="qr-header">
    <h1>{{ $restaurant->name }}</h1>
    <p class="mb-0"><span class="table-badge">🍽 Table {{ $table->name }}</span></p>
</div>

{{-- ========== CATEGORY PILLS ========== --}}
@php
    $activeCategories = $categories->filter(fn ($c) => $c->items->isNotEmpty());
@endphp

@if($activeCategories->isNotEmpty())
<nav class="category-pills" id="category-pills">
    @foreach($activeCategories as $cat)
        <a href="#cat-{{ $cat->id }}" class="category-pill {{ $loop->first ? 'active' : '' }}" data-cat-id="{{ $cat->id }}">{{ $cat->name }}</a>
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
                    <div class="menu-item">
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
                            <div class="menu-item-bottom">
                                <span class="menu-item-price">{{ $restaurant->currency ?? '$' }}{{ number_format($item->price, 2) }}</span>

                                {{-- ADD / QTY CONTROL --}}
                                <button type="button" class="add-btn" id="add-btn-{{ $item->id }}"
                                    data-item-id="{{ $item->id }}"
                                    data-price="{{ $item->price }}"
                                    data-name="{{ $item->name }}">ADD</button>
                                <div class="qty-control" id="qty-ctrl-{{ $item->id }}" style="display:none;">
                                    <button type="button" class="qty-btn minus-btn" data-item-id="{{ $item->id }}">−</button>
                                    <span class="qty-val" id="qty-val-{{ $item->id }}">0</span>
                                    <button type="button" class="qty-btn plus-btn" data-item-id="{{ $item->id }}" data-price="{{ $item->price }}" data-name="{{ $item->name }}">+</button>
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

    {{-- Customer name and notes (inside the form, in bottom sheet) --}}
    <input type="hidden" name="customer_name" id="hidden-customer-name">
    <input type="hidden" name="notes" id="hidden-notes">
</form>

{{-- ========== STICKY CART BAR ========== --}}
<div class="cart-bar" id="cart-bar">
    <div class="cart-bar-left">
        <span class="cart-badge" id="cart-count">0 items</span>
        <span class="cart-bar-total" id="cart-total">{{ $restaurant->currency ?? '$' }}0.00</span>
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
        <div style="margin-bottom:1rem;">
            <label style="font-size:0.8125rem;font-weight:500;display:block;margin-bottom:0.25rem;">Special Requests</label>
            <textarea id="sheet-notes" rows="2" style="width:100%;padding:0.5rem 0.75rem;border:1px solid var(--qr-border);border-radius:var(--qr-radius-sm);font-size:0.875rem;font-family:inherit;resize:none;" placeholder="Any allergies or preferences?"></textarea>
        </div>
        <button type="button" class="place-order-btn" id="place-order-btn" disabled>Place Order</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const currency = '{{ $restaurant->currency ?? "$" }}';
    const cart = {};

    // ===== DOM References =====
    const cartBar = document.getElementById('cart-bar');
    const cartCount = document.getElementById('cart-count');
    const cartTotal = document.getElementById('cart-total');
    const cartSheetBody = document.getElementById('cart-sheet-body');
    const cartSheet = document.getElementById('cart-sheet');
    const sheetOverlay = document.getElementById('sheet-overlay');
    const placeOrderBtn = document.getElementById('place-order-btn');
    const orderForm = document.getElementById('order-form');

    // ===== Cart Logic =====
    function getCartItemCount() {
        let count = 0;
        for (const id in cart) { if (cart[id].qty > 0) count += cart[id].qty; }
        return count;
    }

    function getCartTotal() {
        let total = 0;
        for (const id in cart) { if (cart[id].qty > 0) total += cart[id].qty * cart[id].price; }
        return total;
    }

    function updateUI() {
        const count = getCartItemCount();
        const total = getCartTotal();

        // Sticky bar
        cartCount.textContent = count + (count === 1 ? ' item' : ' items');
        cartTotal.textContent = currency + total.toFixed(2);
        cartBar.classList.toggle('visible', count > 0);

        // Sheet body
        let html = '';
        for (const id in cart) {
            if (cart[id].qty < 1) continue;
            const d = cart[id];
            html += '<div class="cart-sheet-item">' +
                '<div><div class="cart-sheet-item-name">' + escapeHtml(d.name) + '</div>' +
                '<div class="cart-sheet-item-sub">' + currency + parseFloat(d.price).toFixed(2) + ' × ' + d.qty + '</div></div>' +
                '<div style="display:flex;align-items:center;gap:0.5rem;">' +
                '<div class="qty-control" style="height:28px;">' +
                '<button type="button" class="qty-btn sheet-minus" data-id="' + id + '" style="width:28px;font-size:0.875rem;">−</button>' +
                '<span class="qty-val" style="width:22px;font-size:0.75rem;">' + d.qty + '</span>' +
                '<button type="button" class="qty-btn sheet-plus" data-id="' + id + '" style="width:28px;font-size:0.875rem;">+</button>' +
                '</div>' +
                '<span style="font-weight:600;font-size:0.875rem;min-width:55px;text-align:right;">' + currency + (d.price * d.qty).toFixed(2) + '</span>' +
                '</div></div>';
        }
        if (count === 0) {
            html = '<p style="color:var(--qr-text-muted);text-align:center;padding:1rem 0;">Your cart is empty</p>';
        } else {
            html += '<div class="cart-sheet-total"><span>Total</span><span>' + currency + total.toFixed(2) + '</span></div>';
        }
        cartSheetBody.innerHTML = html;
        placeOrderBtn.disabled = count === 0;

        // Rebind sheet +/- buttons
        cartSheetBody.querySelectorAll('.sheet-minus').forEach(function (btn) {
            btn.addEventListener('click', function () { changeQty(this.dataset.id, -1); });
        });
        cartSheetBody.querySelectorAll('.sheet-plus').forEach(function (btn) {
            btn.addEventListener('click', function () { changeQty(this.dataset.id, 1); });
        });
    }

    function changeQty(itemId, delta) {
        if (!cart[itemId]) return;
        cart[itemId].qty = Math.max(0, cart[itemId].qty + delta);
        syncItemUI(itemId);
        updateUI();
    }

    function syncItemUI(itemId) {
        const qty = cart[itemId] ? cart[itemId].qty : 0;
        const addBtn = document.getElementById('add-btn-' + itemId);
        const qtyCtrl = document.getElementById('qty-ctrl-' + itemId);
        const qtyVal = document.getElementById('qty-val-' + itemId);
        if (addBtn) addBtn.style.display = qty > 0 ? 'none' : '';
        if (qtyCtrl) qtyCtrl.style.display = qty > 0 ? '' : 'none';
        if (qtyVal) qtyVal.textContent = qty;
    }

    // ===== Event Listeners =====

    // ADD button (first tap)
    document.querySelectorAll('.add-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.itemId;
            const price = parseFloat(this.dataset.price);
            const name = this.dataset.name;
            if (!cart[id]) cart[id] = { price: price, name: name, qty: 0 };
            cart[id].qty = 1;
            syncItemUI(id);
            updateUI();
        });
    });

    // +/- in item card
    document.querySelectorAll('.plus-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = this.dataset.itemId;
            if (!cart[id]) cart[id] = { price: parseFloat(this.dataset.price), name: this.dataset.name, qty: 0 };
            cart[id].qty++;
            syncItemUI(id);
            updateUI();
        });
    });
    document.querySelectorAll('.minus-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            changeQty(this.dataset.itemId, -1);
        });
    });

    // View Cart / Close Sheet
    document.getElementById('view-cart-btn').addEventListener('click', function () {
        cartSheet.classList.add('open');
        sheetOverlay.classList.add('open');
    });
    document.getElementById('close-sheet-btn').addEventListener('click', closeSheet);
    sheetOverlay.addEventListener('click', closeSheet);

    function closeSheet() {
        cartSheet.classList.remove('open');
        sheetOverlay.classList.remove('open');
    }

    // Place Order
    placeOrderBtn.addEventListener('click', function () {
        // Sync name + notes from sheet into hidden form fields
        document.getElementById('hidden-customer-name').value = document.getElementById('sheet-customer-name').value;
        document.getElementById('hidden-notes').value = document.getElementById('sheet-notes').value;

        // Build hidden inputs for items
        const container = document.getElementById('order-items-container');
        container.innerHTML = '';
        let i = 0;
        for (const id in cart) {
            if (cart[id].qty < 1) continue;
            container.innerHTML += '<input type="hidden" name="items[' + i + '][item_id]" value="' + id + '">' +
                '<input type="hidden" name="items[' + i + '][quantity]" value="' + cart[id].qty + '">';
            i++;
        }
        if (i === 0) return;
        placeOrderBtn.disabled = true;
        placeOrderBtn.textContent = 'Placing order…';
        orderForm.submit();
    });

    // ===== Category Pills =====
    const pills = document.querySelectorAll('.category-pill');
    const headerEl = document.querySelector('.qr-header');
    const pillsNav = document.getElementById('category-pills');

    pills.forEach(function (pill) {
        pill.addEventListener('click', function (e) {
            e.preventDefault();
            pills.forEach(function (p) { p.classList.remove('active'); });
            this.classList.add('active');
            const target = document.getElementById('cat-' + this.dataset.catId);
            if (target) {
                const offset = (headerEl ? headerEl.offsetHeight : 0) + (pillsNav ? pillsNav.offsetHeight : 0) + 8;
                window.scrollTo({ top: target.offsetTop - offset, behavior: 'smooth' });
            }
        });
    });

    // Highlight active pill on scroll
    if (pills.length > 0) {
        const sections = document.querySelectorAll('.menu-section');
        let ticking = false;
        window.addEventListener('scroll', function () {
            if (!ticking) {
                window.requestAnimationFrame(function () {
                    const offset = (headerEl ? headerEl.offsetHeight : 0) + (pillsNav ? pillsNav.offsetHeight : 0) + 20;
                    let current = '';
                    sections.forEach(function (sec) {
                        if (sec.offsetTop - offset <= window.scrollY) {
                            current = sec.id.replace('cat-', '');
                        }
                    });
                    if (current) {
                        pills.forEach(function (p) {
                            p.classList.toggle('active', p.dataset.catId === current);
                            if (p.dataset.catId === current) {
                                p.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                            }
                        });
                    }
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    // Set pills sticky top based on header height
    if (headerEl && pillsNav) {
        pillsNav.style.top = headerEl.offsetHeight + 'px';
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
