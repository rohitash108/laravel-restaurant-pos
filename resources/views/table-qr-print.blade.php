<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Table QR – {{ $restaurant->name ?? 'Restaurant' }} – {{ $table->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800&family=Playfair+Display:ital,wght@1,700&display=swap" rel="stylesheet">
    <style>
        /*
         * Acrylic stand / bank style: A6 portrait (105×148mm).
         * IMPORTANT: in print dialog choose “Actual size / 100%”, not “Fit to page”.
         */
        @page { size: 105mm 148mm; margin: 0; }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #e8ecf1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .top-actions {
            position: fixed;
            top: 12px;
            right: 12px;
            display: flex;
            gap: 8px;
            z-index: 99;
        }
        .top-actions button {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #334155;
            cursor: pointer;
        }
        .top-actions .btn-print { background: #6b21a8; color: #fff; border-color: #6b21a8; }

        .standee {
            width: 105mm;
            height: 148mm;
            background: #fff;
            border-radius: 10mm;
            overflow: hidden;
            border: 0.5mm solid rgba(30, 41, 59, 0.35);
            box-shadow: 0 14px 44px rgba(0,0,0,0.16);
            display: flex;
            flex-direction: column;
        }

        .standee-inner {
            padding: 7mm 7mm 6mm;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 5mm;
        }

        .header {
            text-align: center;
        }
        .brand-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-style: italic;
            font-weight: 700;
            font-size: 7.2mm;
            color: #6b21a8;
            letter-spacing: 0.02em;
        }
        .restaurant-name {
            margin-top: 2.5mm;
            font-weight: 800;
            font-size: 5mm;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #111827;
        }
        .table-line {
            margin-top: 2.5mm;
            font-weight: 800;
            font-size: 4.6mm;
            color: #dc2626;
        }

        .body {
            display: flex;
            gap: 4.5mm;
            align-items: flex-start;
        }

        .qr-col { flex: 0 0 auto; }
        .qr-frame {
            background: linear-gradient(135deg, #ef4444, #7c3aed);
            padding: 1.2mm;
            border-radius: 5mm;
        }
        .qr-inner {
            background: #fff;
            padding: 3mm;
            border-radius: 4mm;
        }
        .qr-inner img {
            display: block;
            width: 46mm;
            height: 46mm;
            border-radius: 2mm;
            image-rendering: pixelated;
        }
        .scan-hint {
            margin-top: 3mm;
            text-align: center;
            font-size: 3.4mm;
            font-weight: 700;
            color: #334155;
        }

        .steps-col { flex: 1; padding-top: 1mm; }
        .steps-title {
            font-weight: 800;
            font-size: 3.6mm;
            color: #111827;
            margin-bottom: 3mm;
        }
        .step {
            display: flex;
            gap: 3mm;
            align-items: flex-start;
            margin-bottom: 3mm;
        }
        .step:last-child { margin-bottom: 0; }
        .step-icon {
            width: 8.5mm;
            height: 8.5mm;
            border-radius: 3mm;
            background: linear-gradient(135deg, #ec4899, #a855f7);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .step-icon svg {
            width: 5mm;
            height: 5mm;
            fill: none;
            stroke: #fff;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }
        .step-text strong {
            display: block;
            font-size: 3.3mm;
            font-weight: 800;
            color: #111827;
            line-height: 1.2;
        }
        .step-text span {
            display: block;
            margin-top: 0.6mm;
            font-size: 3mm;
            color: #64748b;
            line-height: 1.25;
        }

        .divider {
            height: 0.35mm;
            background: linear-gradient(90deg, rgba(239,68,68,0.55), rgba(124,58,237,0.55));
            border-radius: 1mm;
        }

        .footer {
            text-align: center;
            padding: 0 7mm 7mm;
        }
        .powered {
            font-weight: 700;
            color: #111827;
            font-size: 3.6mm;
        }
        .sub {
            margin-top: 1.8mm;
            color: #64748b;
            font-size: 3mm;
            font-weight: 600;
        }

        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            html, body { width: 105mm; height: 148mm; margin: 0; padding: 0; background: #fff !important; }
            body { display: block; }
            .top-actions { display: none !important; }
            .standee { box-shadow: none !important; border-radius: 8mm; }
        }
    </style>
</head>
<body>

<div class="top-actions d-print-none">
    <button type="button" class="btn-print" onclick="window.print()">Print</button>
    <button type="button" onclick="window.close()">Close</button>
</div>

<div class="standee">
    <div class="standee-inner">
        <div class="header">
            <div class="brand-name">OrderByQR</div>
            <div class="restaurant-name">{{ $restaurant->name ?? 'Restaurant' }}</div>
            <div class="table-line">Table Number-{{ $table->table_number ?? $table->name }}</div>
        </div>

        <div class="body">
            <div class="qr-col">
                <div class="qr-frame">
                    <div class="qr-inner">
                        <img src="{{ route('order.by-qr.qr-image', ['restaurant' => $table->restaurant->slug, 'table' => $table->slug ?? $table->id]) }}"
                             alt="QR – Table {{ $table->name }}">
                    </div>
                </div>
                <div class="scan-hint">Scan &amp; order</div>
            </div>

            <div class="steps-col">
                <div class="steps-title">How to order food</div>

                <div class="step">
                    <div class="step-icon">
                        <svg viewBox="0 0 24 24"><rect x="7" y="2" width="10" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    </div>
                    <div class="step-text">
                        <strong>Step 1</strong>
                        <span>Scan the QR with your phone</span>
                    </div>
                </div>

                <div class="step">
                    <div class="step-icon">
                        <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div class="step-text">
                        <strong>Step 2</strong>
                        <span>Menu will be displayed</span>
                    </div>
                </div>

                <div class="step">
                    <div class="step-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 4c-5 0-9 3-10 7 1 4 5 7 10 7s9-3 10-7c-1-4-5-7-10-7z"/><circle cx="12" cy="11" r="2"/></svg>
                    </div>
                    <div class="step-text">
                        <strong>Step 3</strong>
                        <span>Select food</span>
                    </div>
                </div>

                <div class="step">
                    <div class="step-icon">
                        <svg viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    </div>
                    <div class="step-text">
                        <strong>Step 4</strong>
                        <span>Place order</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider" aria-hidden="true"></div>
    </div>

    <div class="footer">
        <div class="powered">Powered by IT Softwar</div>
        <div class="sub">softwar.in &nbsp;|&nbsp; info@softwar.in</div>
    </div>
</div>

</body>
</html>
