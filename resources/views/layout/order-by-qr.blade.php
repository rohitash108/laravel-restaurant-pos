<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $restaurant->name ?? 'Menu' }} | Order</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ URL::asset('build/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --qr-primary: #7c3aed;
            --qr-primary-light: #a78bfa;
            --qr-primary-dark: #5b21b6;
            --qr-success: #10b981;
            --qr-danger: #ef4444;
            --qr-bg: #f8f9fc;
            --qr-card: #ffffff;
            --qr-text: #1e293b;
            --qr-text-muted: #94a3b8;
            --qr-border: #e2e8f0;
            --qr-radius: 14px;
            --qr-radius-sm: 10px;
            --qr-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --qr-shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
        }

        * { box-sizing: border-box; }
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

        /* Header */
        .qr-header {
            background: linear-gradient(135deg, var(--qr-primary) 0%, var(--qr-primary-light) 100%);
            color: #fff;
            padding: 1rem 1rem 1.25rem;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .qr-header h1 { font-size: 1.125rem; font-weight: 700; margin: 0; }
        .qr-header .table-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(4px);
            padding: 0.2rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-top: 0.35rem;
        }

        /* Category pills */
        .category-pills {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding: 0.75rem 1rem;
            background: var(--qr-card);
            border-bottom: 1px solid var(--qr-border);
            position: sticky;
            top: 0; /* will be adjusted by JS */
            z-index: 90;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }
        .category-pills::-webkit-scrollbar { display: none; }
        .category-pill {
            flex-shrink: 0;
            padding: 0.4rem 1rem;
            border-radius: 999px;
            font-size: 0.8125rem;
            font-weight: 500;
            background: var(--qr-bg);
            color: var(--qr-text-muted);
            border: 1px solid var(--qr-border);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            white-space: nowrap;
        }
        .category-pill.active, .category-pill:active {
            background: var(--qr-primary);
            color: #fff;
            border-color: var(--qr-primary);
        }

        /* Menu section */
        .menu-section { padding: 0 1rem; }
        .menu-section-title {
            font-size: 1rem;
            font-weight: 700;
            margin: 1.25rem 0 0.75rem;
            padding-top: 0.5rem;
        }

        /* Item card */
        .menu-item {
            background: var(--qr-card);
            border-radius: var(--qr-radius);
            box-shadow: var(--qr-shadow);
            padding: 0.875rem;
            margin-bottom: 0.75rem;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }
        .menu-item-img {
            width: 90px;
            height: 90px;
            border-radius: var(--qr-radius-sm);
            object-fit: cover;
            flex-shrink: 0;
        }
        .menu-item-placeholder {
            width: 90px;
            height: 90px;
            border-radius: var(--qr-radius-sm);
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.5rem;
            color: var(--qr-text-muted);
        }
        .menu-item-info { flex: 1; min-width: 0; }
        .menu-item-name {
            font-size: 0.9375rem;
            font-weight: 600;
            margin: 0 0 0.15rem;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }
        .menu-item-desc {
            font-size: 0.75rem;
            color: var(--qr-text-muted);
            margin: 0 0 0.5rem;
            line-height: 1.4;
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
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--qr-primary-dark);
        }

        /* Food type dot */
        .food-dot {
            width: 14px; height: 14px;
            border: 2px solid;
            border-radius: 3px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .food-dot.veg { border-color: #22c55e; }
        .food-dot.veg::after { content: ''; width: 6px; height: 6px; border-radius: 50%; background: #22c55e; }
        .food-dot.non-veg { border-color: #ef4444; }
        .food-dot.non-veg::after { content: ''; width: 0; height: 0; border-left: 3.5px solid transparent; border-right: 3.5px solid transparent; border-bottom: 6px solid #ef4444; }

        /* Qty controls */
        .qty-control {
            display: inline-flex;
            align-items: center;
            border: 1.5px solid var(--qr-primary);
            border-radius: var(--qr-radius-sm);
            overflow: hidden;
            height: 34px;
        }
        .qty-btn {
            width: 34px;
            height: 100%;
            border: none;
            background: transparent;
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--qr-primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
            -webkit-tap-highlight-color: transparent;
        }
        .qty-btn:active { background: rgba(124,58,237,0.08); }
        .qty-val {
            width: 28px;
            text-align: center;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--qr-primary);
        }
        .add-btn {
            height: 34px;
            padding: 0 1.25rem;
            border: 1.5px solid var(--qr-primary);
            border-radius: var(--qr-radius-sm);
            background: transparent;
            color: var(--qr-primary);
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
            -webkit-tap-highlight-color: transparent;
        }
        .add-btn:active { background: var(--qr-primary); color: #fff; }

        /* Sticky cart bar */
        .cart-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--qr-primary);
            color: #fff;
            padding: 0.875rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 200;
            box-shadow: 0 -4px 20px rgba(124,58,237,0.3);
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding-bottom: calc(0.875rem + env(safe-area-inset-bottom, 0));
        }
        .cart-bar.visible { transform: translateY(0); }
        .cart-bar-left { display: flex; align-items: center; gap: 0.5rem; }
        .cart-badge {
            background: rgba(255,255,255,0.25);
            padding: 0.15rem 0.5rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .cart-bar-total { font-size: 0.9375rem; font-weight: 600; }
        .cart-bar-btn {
            background: #fff;
            color: var(--qr-primary);
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: var(--qr-radius-sm);
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
        }

        /* Bottom sheet overlay */
        .sheet-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 300;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }
        .sheet-overlay.open { opacity: 1; pointer-events: auto; }

        /* Bottom sheet */
        .cart-sheet {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--qr-card);
            border-radius: 20px 20px 0 0;
            z-index: 310;
            max-height: 85vh;
            overflow-y: auto;
            transform: translateY(100%);
            transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            padding-bottom: env(safe-area-inset-bottom, 0);
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
        .cart-sheet-header h2 { font-size: 1.125rem; font-weight: 700; margin: 0; }
        .cart-sheet-body { padding: 1rem 1.25rem; }
        .cart-sheet-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.625rem 0;
            border-bottom: 1px solid var(--qr-border);
        }
        .cart-sheet-item:last-child { border-bottom: none; }
        .cart-sheet-item-name { font-weight: 500; font-size: 0.875rem; }
        .cart-sheet-item-sub { font-size: 0.75rem; color: var(--qr-text-muted); }
        .cart-sheet-total {
            display: flex;
            justify-content: space-between;
            font-size: 1.125rem;
            font-weight: 700;
            padding: 1rem 0 0.5rem;
        }
        .cart-sheet-footer { padding: 0.75rem 1.25rem 1rem; }
        .place-order-btn {
            width: 100%;
            padding: 0.875rem;
            background: var(--qr-primary);
            color: #fff;
            border: none;
            border-radius: var(--qr-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        .place-order-btn:active { background: var(--qr-primary-dark); }
        .place-order-btn:disabled { opacity: 0.5; cursor: not-allowed; }

        /* Empty state */
        .empty-state { text-align: center; padding: 3rem 1rem; }
        .empty-state-icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-state h3 { font-size: 1.125rem; font-weight: 600; margin: 0 0 0.5rem; }
        .empty-state p { color: var(--qr-text-muted); font-size: 0.875rem; margin: 0; }

        /* Success page */
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
            font-weight: 700;
            color: var(--qr-primary);
            letter-spacing: 1px;
            margin-bottom: 1rem;
        }
        .order-again-btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: var(--qr-primary);
            color: #fff;
            border-radius: var(--qr-radius);
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }
        .order-again-btn:active { background: var(--qr-primary-dark); }

        /* Spacing for cart bar */
        .page-bottom-spacer { height: 80px; }

        /* Desktop max-width */
        @media (min-width: 768px) {
            .qr-page-wrap { max-width: 480px; margin: 0 auto; }
            .cart-bar, .cart-sheet { max-width: 480px; left: 50%; transform: translateX(-50%) translateY(100%); }
            .cart-bar.visible { transform: translateX(-50%) translateY(0); }
            .cart-sheet.open { transform: translateX(-50%) translateY(0); }
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
