<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Table QR – {{ $restaurant->name ?? 'Restaurant' }} – {{ $table->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800&family=Playfair+Display:ital,wght@1,700&display=swap" rel="stylesheet">
    <style>
        /*
         * Compact sticker: ~70×100mm portrait (Paytm / bank QR standee style).
         * Set printer to 100% scale — @page tells the browser the physical size.
         */
        @page {
            size: 70mm 100mm;
            margin: 0;
        }

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
        .top-actions .btn-print {
            background: #6b21a8;
            color: #fff;
            border-color: #6b21a8;
        }

        /* Physical sticker: 70mm × 100mm */
        .sticker {
            width: 70mm;
            height: 100mm;
            background: #fff;
            border-radius: 3mm;
            border: 0.35mm solid #1e293b;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        .sticker-header {
            text-align: center;
            padding: 3mm 3mm 2mm;
            border-bottom: 0.25mm solid rgba(107, 33, 168, 0.25);
        }
        .brand-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-style: italic;
            font-weight: 700;
            font-size: 3.8mm;
            color: #6b21a8;
            line-height: 1.2;
        }
        .restaurant-name {
            font-weight: 800;
            font-size: 3.2mm;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #1e293b;
            margin-top: 1mm;
            line-height: 1.2;
        }
        .table-line {
            font-weight: 700;
            font-size: 2.9mm;
            color: #dc2626;
            margin-top: 0.8mm;
        }

        .sticker-qr-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2mm 3mm;
            min-height: 0;
        }
        .qr-frame {
            background: linear-gradient(135deg, #ef4444, #7c3aed);
            padding: 0.8mm;
            border-radius: 2.5mm;
        }
        .qr-inner {
            background: #fff;
            border-radius: 2mm;
            padding: 1.5mm;
            line-height: 0;
        }
        .qr-inner img {
            display: block;
            width: 42mm;
            height: 42mm;
            image-rendering: pixelated;
            border-radius: 1mm;
        }

        .sticker-footer {
            padding: 2mm 3mm 2.5mm;
            border-top: 0.25mm solid rgba(107, 33, 168, 0.25);
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 2mm;
            font-size: 2.4mm;
            color: #475569;
        }
        .sticker-meta {
            flex: 1;
            min-width: 0;
        }
        .sticker-meta strong {
            display: block;
            font-size: 2.5mm;
            color: #1e293b;
            margin-bottom: 0.5mm;
        }
        .badge-print {
            display: inline-block;
            padding: 0.4mm 1.2mm;
            border-radius: 1mm;
            font-size: 2.2mm;
            font-weight: 700;
        }
        .badge-print.available { background: #dcfce7; color: #166534; }
        .badge-print.occupied { background: #fef3c7; color: #92400e; }
        .badge-print.reserved { background: #fee2e2; color: #991b1b; }

        .powered {
            text-align: right;
            font-size: 2.3mm;
            font-weight: 600;
            color: #c2410c;
            line-height: 1.25;
            max-width: 28mm;
        }

        /* Screen preview: show sticker at readable scale (not full mm on monitor) */
        @media screen {
            .sticker {
                width: 280px;
                height: 400px;
                box-shadow: 0 12px 40px rgba(0,0,0,0.15);
            }
            .brand-name { font-size: 15px; }
            .restaurant-name { font-size: 13px; }
            .table-line { font-size: 12px; }
            .qr-inner img { width: 168px; height: 168px; }
            .sticker-footer { font-size: 10px; padding: 8px 12px 10px; }
            .sticker-meta strong { font-size: 10px; }
            .badge-print { font-size: 9px; padding: 2px 6px; }
            .powered { font-size: 9px; max-width: 110px; }
            .sticker-header { padding: 12px 12px 8px; }
            .sticker-qr-wrap { padding: 8px 12px; }
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            @page {
                size: 70mm 100mm;
                margin: 0;
            }
            html, body {
                width: 70mm;
                height: 100mm;
                margin: 0;
                padding: 0;
                background: #fff !important;
            }
            body {
                display: block;
                padding: 0;
            }
            .top-actions { display: none !important; }
            .sticker {
                width: 70mm !important;
                height: 100mm !important;
                max-width: 70mm !important;
                max-height: 100mm !important;
                box-shadow: none !important;
                border-radius: 2mm;
                page-break-inside: avoid;
            }
            .qr-inner img {
                width: 42mm !important;
                height: 42mm !important;
            }
        }
    </style>
</head>
<body>

<div class="top-actions d-print-none">
    <button type="button" class="btn-print" onclick="window.print()">Print</button>
    <button type="button" onclick="window.close()">Close</button>
</div>

@php
    $badgeClass = 'available';
    $badgeLabel = 'Available';
    if (! empty($tableOccupied)) {
        $badgeClass = 'occupied';
        $badgeLabel = 'Occupied';
    } elseif (($table->status ?? '') === 'reserved') {
        $badgeClass = 'reserved';
        $badgeLabel = 'Reserved';
    }
@endphp

<div class="sticker">
    <div class="sticker-header">
        <div class="brand-name">OrderByQR</div>
        <div class="restaurant-name">{{ $restaurant->name ?? 'Restaurant' }}</div>
        <div class="table-line">Table Number – {{ $table->name }}</div>
    </div>

    <div class="sticker-qr-wrap">
        <div class="qr-frame">
            <div class="qr-inner">
                <img src="{{ route('order.by-qr.qr-image', ['restaurant' => $table->restaurant->slug, 'table' => $table->slug ?? $table->id]) }}"
                     alt="QR – Table {{ $table->name }}">
            </div>
        </div>
    </div>

    <div class="sticker-footer">
        <div class="sticker-meta">
            <strong>Floor: {{ $table->floor ?: '–' }} · Capacity: {{ $table->capacity ?? '–' }}</strong>
            <span class="badge-print {{ $badgeClass }}">{{ $badgeLabel }}</span>
        </div>
        <div class="powered">Powered by IT Softwar</div>
    </div>
</div>

</body>
</html>
