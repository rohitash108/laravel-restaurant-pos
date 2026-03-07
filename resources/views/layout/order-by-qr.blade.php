<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $restaurant->name ?? 'Menu' }} | Order</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <link href="{{ URL::asset('build/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --qr-primary: #7c3aed;
            --qr-primary-light: #a78bfa;
            --qr-primary-dark: #5b21b6;
            --qr-primary-glow: rgba(124, 58, 237, 0.15);
            --qr-success: #10b981;
            --qr-danger: #ef4444;
            --qr-warning: #f59e0b;
            --qr-bg: #f1f0f7;
            --qr-card: #ffffff;
            --qr-text: #1e293b;
            --qr-text-muted: #94a3b8;
            --qr-border: rgba(148, 163, 184, 0.25);
            --qr-radius: 16px;
            --qr-radius-sm: 12px;
            --qr-shadow: 0 2px 8px rgba(0,0,0,0.06), 0 0 1px rgba(0,0,0,0.04);
            --qr-shadow-lg: 0 12px 32px rgba(0,0,0,0.12);
        }

        * { box-sizing: border-box; -webkit-tap-highlight-color: transparent; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--qr-bg);
            color: var(--qr-text);
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            padding-bottom: env(safe-area-inset-bottom, 0);
        }

        /* ===== Header ===== */
        .qr-header {
            background: linear-gradient(135deg, #6d28d9 0%, #7c3aed 40%, #a78bfa 100%);
            color: #fff;
            padding: 1.25rem 1rem 1.5rem;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 20px rgba(109, 40, 217, 0.3);
        }
        .qr-header::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 0;
            right: 0;
            height: 12px;
            background: linear-gradient(to bottom, rgba(109,40,217,0.08), transparent);
            pointer-events: none;
        }
        .qr-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.01em;
            text-shadow: 0 1px 4px rgba(0,0,0,0.15);
        }
        .qr-header .table-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            padding: 0.3rem 0.85rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            margin-top: 0.45rem;
            border: 1px solid rgba(255,255,255,0.15);
        }

        /* ===== Search ===== */
        .qr-search-wrap {
            padding: 0.65rem 1rem;
            background: var(--qr-card);
            border-bottom: 1px solid var(--qr-border);
        }
        .qr-search-input {
            width: 100%;
            padding: 0.65rem 1rem 0.65rem 2.75rem;
            border: 1.5px solid var(--qr-border);
            border-radius: var(--qr-radius);
            font-size: 0.9375rem;
            background: var(--qr-bg) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E") no-repeat 0.85rem center;
            color: var(--qr-text);
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .qr-search-input::placeholder { color: var(--qr-text-muted); }
        .qr-search-input:focus {
            outline: none;
            border-color: var(--qr-primary);
            box-shadow: 0 0 0 3px var(--qr-primary-glow);
        }

        /* ===== Search filter hidden states ===== */
        .menu-section.qr-section-hidden { display: none !important; }
        .menu-item.qr-item-hidden { display: none !important; }
        .category-pill.qr-pill-hidden { display: none !important; }

        /* ===== Category pills ===== */
        .category-pills {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding: 0.75rem 1rem;
            background: var(--qr-card);
            border-bottom: 1px solid var(--qr-border);
            position: sticky;
            top: 0;
            z-index: 90;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .category-pills::-webkit-scrollbar { display: none; }
        .category-pill {
            flex-shrink: 0;
            padding: 0.45rem 1.1rem;
            border-radius: 999px;
            font-size: 0.8125rem;
            font-weight: 600;
            background: var(--qr-bg);
            color: var(--qr-text-muted);
            border: 1.5px solid var(--qr-border);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            white-space: nowrap;
        }
        .category-pill.active, .category-pill:active {
            background: var(--qr-primary);
            color: #fff;
            border-color: var(--qr-primary);
            box-shadow: 0 3px 12px rgba(124, 58, 237, 0.3);
            transform: scale(1.02);
        }

        /* ===== Menu section ===== */
        .menu-section { padding: 0 1rem; }
        .menu-section-title {
            font-size: 1.05rem;
            font-weight: 800;
            margin: 1.25rem 0 0.75rem;
            padding-top: 0.5rem;
            color: var(--qr-text);
            letter-spacing: -0.01em;
        }

        /* ===== Item card ===== */
        .menu-item {
            background: var(--qr-card);
            border-radius: var(--qr-radius);
            box-shadow: var(--qr-shadow);
            padding: 0.875rem;
            margin-bottom: 0.75rem;
            display: flex;
            gap: 0.875rem;
            align-items: flex-start;
            transition: transform 0.15s, box-shadow 0.15s;
            border: 1px solid var(--qr-border);
        }
        .menu-item:active {
            transform: scale(0.985);
        }
        .menu-item-img {
            width: 88px;
            height: 88px;
            border-radius: var(--qr-radius-sm);
            object-fit: cover;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .menu-item-placeholder {
            width: 88px;
            height: 88px;
            border-radius: var(--qr-radius-sm);
            background: linear-gradient(135deg, #ede9fe, #e0e7ff);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.75rem;
        }
        .menu-item-info { flex: 1; min-width: 0; }
        .menu-item-name {
            font-size: 0.9375rem;
            font-weight: 700;
            margin: 0 0 0.2rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            color: var(--qr-text);
        }
        .menu-item-desc {
            font-size: 0.75rem;
            color: var(--qr-text-muted);
            margin: 0 0 0.5rem;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .menu-item-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .menu-item-price {
            font-size: 1rem;
            font-weight: 800;
            color: var(--qr-primary-dark);
        }

        /* ===== Food type dot ===== */
        .food-dot {
            width: 15px; height: 15px;
            border: 2px solid;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .food-dot.veg { border-color: #22c55e; }
        .food-dot.veg::after { content: ''; width: 7px; height: 7px; border-radius: 50%; background: #22c55e; }
        .food-dot.non-veg { border-color: #ef4444; }
        .food-dot.non-veg::after { content: ''; width: 0; height: 0; border-left: 4px solid transparent; border-right: 4px solid transparent; border-bottom: 7px solid #ef4444; }

        /* ===== Qty controls ===== */
        .qty-control {
            display: inline-flex;
            align-items: center;
            border: 2px solid var(--qr-primary);
            border-radius: var(--qr-radius-sm);
            overflow: hidden;
            height: 36px;
            background: var(--qr-card);
            box-shadow: 0 2px 8px var(--qr-primary-glow);
        }
        .qty-btn {
            width: 36px;
            height: 100%;
            border: none;
            background: transparent;
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--qr-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
        }
        .qty-btn:active { background: var(--qr-primary-glow); }
        .qty-val {
            width: 28px;
            text-align: center;
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--qr-primary);
        }
        .add-btn {
            height: 36px;
            padding: 0 1.35rem;
            border: 2px solid var(--qr-primary);
            border-radius: var(--qr-radius-sm);
            background: transparent;
            color: var(--qr-primary);
            font-size: 0.8125rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            cursor: pointer;
            transition: all 0.2s;
            text-transform: uppercase;
        }
        .add-btn:active {
            background: var(--qr-primary);
            color: #fff;
            transform: scale(0.95);
        }

        /* ===== Sticky cart bar ===== */
        .cart-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #6d28d9, #7c3aed);
            color: #fff;
            padding: 0.9rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 200;
            box-shadow: 0 -6px 24px rgba(109, 40, 217, 0.35);
            transform: translateY(100%);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            padding-bottom: calc(0.9rem + env(safe-area-inset-bottom, 0));
            border-radius: 18px 18px 0 0;
        }
        .cart-bar.visible { transform: translateY(0); }
        .cart-bar-left { display: flex; align-items: center; gap: 0.6rem; }
        .cart-badge {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(4px);
            padding: 0.2rem 0.6rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .cart-bar-total { font-size: 1rem; font-weight: 700; }
        .cart-bar-btn {
            background: #fff;
            color: var(--qr-primary);
            border: none;
            padding: 0.55rem 1.35rem;
            border-radius: var(--qr-radius-sm);
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            transition: transform 0.15s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .cart-bar-btn:active { transform: scale(0.95); }

        /* ===== Bottom sheet overlay ===== */
        .sheet-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            z-index: 300;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }
        .sheet-overlay.open { opacity: 1; pointer-events: auto; }

        /* ===== Bottom sheet ===== */
        .cart-sheet {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--qr-card);
            border-radius: 24px 24px 0 0;
            z-index: 310;
            max-height: 85vh;
            overflow-y: auto;
            transform: translateY(100%);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            padding-bottom: env(safe-area-inset-bottom, 0);
            box-shadow: 0 -10px 40px rgba(0,0,0,0.15);
        }
        .cart-sheet.open { transform: translateY(0); }
        .cart-sheet-handle {
            width: 40px;
            height: 4px;
            background: #d1d5db;
            border-radius: 999px;
            margin: 0.75rem auto;
        }
        .cart-sheet-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem 0.75rem;
            border-bottom: 1px solid var(--qr-border);
        }
        .cart-sheet-header h2 { font-size: 1.125rem; font-weight: 800; margin: 0; }
        .cart-sheet-body { padding: 1rem 1.25rem; }
        .cart-sheet-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.7rem 0;
            border-bottom: 1px solid var(--qr-border);
        }
        .cart-sheet-item:last-child { border-bottom: none; }
        .cart-sheet-item-name { font-weight: 600; font-size: 0.875rem; }
        .cart-sheet-item-sub { font-size: 0.75rem; color: var(--qr-text-muted); }
        .cart-sheet-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.15rem;
            font-weight: 800;
            padding: 1rem 0 0.5rem;
            border-top: 2px dashed var(--qr-border);
            margin-top: 0.5rem;
        }
        .cart-sheet-footer { padding: 0.75rem 1.25rem 1rem; }
        .place-order-btn {
            width: 100%;
            padding: 0.95rem;
            background: linear-gradient(135deg, #6d28d9, #7c3aed);
            color: #fff;
            border: none;
            border-radius: var(--qr-radius);
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(109, 40, 217, 0.3);
            letter-spacing: 0.02em;
        }
        .place-order-btn:active {
            background: var(--qr-primary-dark);
            transform: scale(0.97);
        }
        .place-order-btn:disabled { opacity: 0.5; cursor: not-allowed; box-shadow: none; }

        /* ===== Empty state ===== */
        .empty-state { text-align: center; padding: 3rem 1rem; }
        .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-state h3 { font-size: 1.125rem; font-weight: 700; margin: 0 0 0.5rem; }
        .empty-state p { color: var(--qr-text-muted); font-size: 0.875rem; margin: 0; }

        /* ===== Success page ===== */
        .success-card {
            background: var(--qr-card);
            border-radius: var(--qr-radius);
            box-shadow: var(--qr-shadow-lg);
            padding: 2.5rem 1.5rem;
            text-align: center;
            max-width: 400px;
            margin: 2rem auto;
        }
        .success-check {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(16,185,129,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            animation: popIn 0.4s ease-out;
        }
        .success-check svg { width: 40px; height: 40px; color: var(--qr-success); }
        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }
        .success-order-num {
            display: inline-block;
            background: var(--qr-bg);
            padding: 0.5rem 1.25rem;
            border-radius: var(--qr-radius-sm);
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--qr-primary);
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }
        .order-again-btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #6d28d9, #7c3aed);
            color: #fff;
            border-radius: var(--qr-radius);
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(109, 40, 217, 0.3);
        }
        .order-again-btn:active { background: var(--qr-primary-dark); }

        /* ===== Success: payment status badge ===== */
        .payment-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: var(--qr-radius-sm);
            font-size: 0.875rem;
            font-weight: 600;
        }
        .payment-status-badge .payment-status-icon { font-size: 1.1rem; }
        .payment-status-paid {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
        }
        .payment-status-unpaid {
            background: rgba(245, 158, 11, 0.15);
            color: #b45309;
        }
        /* ===== Success: Scan & Pay card ===== */
        .qr-scan-pay-card {
            background: var(--qr-card);
            border-radius: var(--qr-radius);
            box-shadow: var(--qr-shadow);
            border: 1px solid var(--qr-border);
            padding: 1rem;
        }
        .qr-pay-qr-wrap {
            width: 100px;
            height: 100px;
            padding: 6px;
            background: linear-gradient(135deg, #ef4444, #7c3aed);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-pay-qr-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            background: #fff;
        }

        /* ===== Spacing for cart bar ===== */
        .page-bottom-spacer { height: 85px; }

        /* ===== Item options sheet (variations & addons) ===== */
        .qr-options-sheet { z-index: 350; }
        .qr-option-pill {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0.25rem 0.25rem 0.25rem 0;
            border-radius: var(--qr-radius-sm);
            border: 2px solid var(--qr-border);
            background: var(--qr-card);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        .qr-option-pill.active { border-color: var(--qr-primary); background: rgba(124, 58, 237, 0.1); color: var(--qr-primary); }
        .qr-addon-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.6rem 0.75rem;
            margin-bottom: 0.35rem;
            border: 2px solid var(--qr-border);
            border-radius: var(--qr-radius-sm);
            background: var(--qr-card);
            cursor: pointer;
            transition: all 0.2s;
        }
        .qr-addon-row.active { border-color: var(--qr-primary); background: rgba(124, 58, 237, 0.08); }
        .qr-addon-row span:last-child { font-weight: 600; color: var(--qr-primary-dark); }
        .menu-item-options-hint { font-size: 0.7rem; color: var(--qr-text-muted); margin: 0 0 0.35rem; }
        .menu-item-variations, .menu-item-addons {
            margin-bottom: 0.4rem;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.25rem 0.35rem;
        }
        .menu-item-options-label {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--qr-text-muted);
            margin-right: 0.15rem;
            flex-shrink: 0;
        }
        .menu-item-option-pill {
            display: inline-block;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            background: rgba(124, 58, 237, 0.08);
            color: var(--qr-primary-dark);
            border-radius: 6px;
            font-weight: 500;
            border: 1px solid rgba(124, 58, 237, 0.2);
        }

        /* ===== Desktop max-width ===== */
        @media (min-width: 768px) {
            .qr-page-wrap { max-width: 480px; margin: 0 auto; box-shadow: var(--qr-shadow-lg); min-height: 100vh; background: var(--qr-bg); }
            .cart-bar, .cart-sheet { max-width: 480px; left: 50%; transform: translateX(-50%) translateY(100%); }
            .cart-bar.visible { transform: translateX(-50%) translateY(0); }
            .cart-sheet.open { transform: translateX(-50%) translateY(0); }
            .qr-options-sheet.open { transform: translateX(-50%) translateY(0); }
        }
    </style>
</head>
<body>
    <div class="qr-page-wrap">
        @yield('content')
    </div>
    <script src="{{ URL::asset('build/js/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
