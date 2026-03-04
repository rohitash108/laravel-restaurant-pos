<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Table QR Standee – {{ $restaurant->name ?? 'Restaurant' }} – {{ $table->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@1,700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: 148mm 105mm;
            margin: 0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #e8ecf1;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card-outer {
            width: 520px;
            background: #fff;
            border-radius: 18px;
            overflow: hidden;
            border: 2.5px solid #1e293b;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18), 0 0 0 6px rgba(107, 33, 168, 0.12);
        }
        /* ── Header ── */
        .card-header-section {
            text-align: center;
            padding: 22px 20px 14px;
        }
        .brand-name {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-weight: 700;
            font-size: 1.6rem;
            color: #6b21a8;
            letter-spacing: 0.02em;
        }
        .restaurant-name {
            font-weight: 800;
            font-size: 1.05rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #1e293b;
            margin-top: 2px;
        }
        .table-number {
            font-weight: 700;
            font-size: 1rem;
            color: #dc2626;
            margin-top: 2px;
        }

        /* ── Body: two columns ── */
        .card-body-section {
            display: flex;
            align-items: flex-start;
            padding: 4px 24px 18px;
            gap: 20px;
        }

        /* ── QR Column ── */
        .qr-column {
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-frame {
            background: linear-gradient(135deg, #ef4444, #7c3aed);
            padding: 4px;
            border-radius: 16px;
        }
        .qr-inner {
            background: #ffffff;
            border-radius: 13px;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qr-inner img {
            display: block;
            width: 160px;
            height: 160px;
            image-rendering: pixelated;
            border-radius: 4px;
        }

        /* ── Steps Column ── */
        .steps-column {
            flex: 1;
            padding-top: 4px;
        }
        .steps-title {
            font-weight: 700;
            font-size: 0.92rem;
            color: #1e293b;
            margin-bottom: 12px;
        }
        .step-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .step-row:last-child {
            margin-bottom: 0;
        }
        .step-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #ec4899, #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-right: 12px;
        }
        .step-icon-box svg {
            width: 18px;
            height: 18px;
            fill: none;
            stroke: #fff;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .step-label strong {
            display: block;
            font-size: 0.82rem;
            font-weight: 700;
            color: #1e293b;
        }
        .step-label span {
            font-size: 0.74rem;
            color: #64748b;
            line-height: 1.2;
        }

        /* ── Footer ── */
        .card-footer-section {
            border-top: 2px solid #6b21a8;
            margin: 0 24px;
            padding: 10px 0 14px;
            text-align: center;
        }
        .powered-line {
            font-size: 0.78rem;
            font-weight: 600;
            color: #334155;
        }
        .powered-sub {
            font-size: 0.68rem;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* ── Top actions (screen only) ── */
        .top-actions {
            position: fixed;
            top: 12px;
            right: 12px;
            display: flex;
            gap: 6px;
            z-index: 99;
        }
        .top-actions button {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            border: 1.5px solid #cbd5e1;
            background: #fff;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s;
        }
        .top-actions button:hover {
            background: #f1f5f9;
        }
        .top-actions .btn-print {
            background: #6b21a8;
            color: #fff;
            border-color: #6b21a8;
        }
        .top-actions .btn-print:hover {
            background: #581c87;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            @page {
                size: 148mm 105mm;
                margin: 0;
            }
            html, body {
                width: 148mm;
                height: 105mm;
                margin: 0;
                padding: 0;
                background: #fff !important;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .top-actions {
                display: none !important;
            }
            .card-outer {
                box-shadow: none !important;
                border: 2.5px solid #1e293b !important;
                border-radius: 12px;
                width: 148mm;
                height: 105mm;
                max-width: 148mm;
                max-height: 105mm;
                overflow: hidden;
            }
            .card-header-section {
                padding: 14px 16px 8px;
            }
            .brand-name {
                font-size: 1.4rem;
            }
            .restaurant-name {
                font-size: 0.95rem;
            }
            .table-number {
                font-size: 0.9rem;
            }
            .card-body-section {
                padding: 2px 20px 12px;
                gap: 16px;
            }
            .qr-frame {
                background: linear-gradient(135deg, #ef4444, #7c3aed) !important;
            }
            .qr-inner img {
                width: 140px;
                height: 140px;
            }
            .step-icon-box {
                background: linear-gradient(135deg, #ec4899, #a855f7) !important;
                width: 32px;
                height: 32px;
            }
            .step-icon-box svg {
                width: 16px;
                height: 16px;
                stroke: #fff !important;
            }
            .step-row {
                margin-bottom: 6px;
            }
            .step-label strong {
                font-size: 0.78rem;
            }
            .step-label span {
                font-size: 0.7rem;
            }
            .card-footer-section {
                border-top: 2px solid #6b21a8 !important;
                padding: 6px 0 8px;
                margin: 0 20px;
            }
        }
    </style>
</head>
<body onload="window.print()">

<div class="top-actions d-print-none">
    <button class="btn-print" onclick="window.print()">🖨 Print</button>
    <button onclick="window.close()">✕ Close</button>
</div>

<div class="card-outer">
    <!-- Header -->
    <div class="card-header-section">
        <div class="brand-name">OrderByQR</div>
        <div class="restaurant-name">{{ $restaurant->name ?? 'Restaurant' }}</div>
        <div class="table-number">Table Number - {{ $table->name }}</div>
    </div>

    <!-- Body: QR + Steps -->
    <div class="card-body-section">
        <!-- QR Code -->
        <div class="qr-column">
            <div class="qr-frame">
                <div class="qr-inner">
                    <img src="{{ route('order.by-qr.qr-image', ['restaurant' => $table->restaurant->slug, 'table' => $table->slug ?? $table->id]) }}"
                         alt="QR – Table {{ $table->name }}">
                </div>
            </div>
        </div>

        <!-- Steps -->
        <div class="steps-column">
            <div class="steps-title">How to order food</div>

            <div class="step-row">
                <div class="step-icon-box">
                    <svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                </div>
                <div class="step-label">
                    <strong>Step 1</strong>
                    <span>Scan the QR with your phone</span>
                </div>
            </div>

            <div class="step-row">
                <div class="step-icon-box">
                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div class="step-label">
                    <strong>Step 2</strong>
                    <span>Menu will be displayed</span>
                </div>
            </div>

            <div class="step-row">
                <div class="step-icon-box">
                    <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                </div>
                <div class="step-label">
                    <strong>Step 3</strong>
                    <span>Select food</span>
                </div>
            </div>

            <div class="step-row">
                <div class="step-icon-box">
                    <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                </div>
                <div class="step-label">
                    <strong>Step 4</strong>
                    <span>Place order</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="card-footer-section">
        <div class="powered-line">Powered by CS Labs</div>
        <div class="powered-sub">www.cslabs.in &nbsp;|&nbsp; contact@cslabs.in</div>
    </div>
</div>

</body>
</html>
