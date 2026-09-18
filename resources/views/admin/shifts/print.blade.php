<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Rekap Shift #{{ $shift->id }} - Riak Coffee</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.3;
            margin: 0 auto;
            padding: 6px 4px;
            color: #000;
            width: 58mm;
            max-width: 58mm;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .divider-double {
            border-top: 1px double #000;
            margin: 5px 0;
        }
        .flex {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .signature-space {
            margin-top: 24px;
            text-align: center;
        }
        @media print {
            body {
                width: 100% !important;
                max-width: 58mm !important;
                margin: 0 !important;
                padding: 2mm !important;
            }
            @page {
                size: auto;
                margin: 0mm;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="text-center">
        <img src="{{ asset('images/riaklogo.jpeg') }}" 
            alt="Riak Coffee" 
            style="display: block; margin: 0 auto 4px auto; height: 55px; filter: grayscale(100%) contrast(125%);">
        <div class="font-bold" style="font-size: 13px; letter-spacing: 1px;">RIAK COFFEE</div>
        <div style="font-size: 9px;">Coffee &bull; Community &bull; Space</div>
        <div class="font-bold" style="margin-top: 4px; font-size: 11px;">REKAP TUTUP SHIFT KASIR</div>
    </div>

    <div class="divider"></div>

    <div class="flex">
        <span>No. Shift:</span>
        <span class="font-bold">#{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }}</span>
    </div>
    <div class="flex">
        <span>Kasir:</span>
        <span class="font-bold">{{ $shift->user->name ?? 'Kasir' }}</span>
    </div>
    <div class="flex">
        <span>Buka:</span>
        <span>{{ $shift->opened_at ? $shift->opened_at->format('d/m/y H:i') : '-' }}</span>
    </div>
    <div class="flex">
        <span>Tutup:</span>
        <span>{{ $shift->closed_at ? $shift->closed_at->format('d/m/y H:i') : '-' }}</span>
    </div>

    <div class="divider"></div>

    <div class="font-bold" style="margin-bottom: 2px;">PENJUALAN PER METODE:</div>
    @php
        $grandTotalSales = 0;
        $totalOrdersCount = 0;
    @endphp
    @forelse ($salesSummary as $row)
        @php
            $grandTotalSales += $row->total_amount;
            $totalOrdersCount += $row->trx_count;
        @endphp
        <div class="flex">
            <span>{{ strtoupper($row->payment_method) }} ({{ $row->trx_count }}x)</span>
            <span>Rp {{ number_format($row->total_amount, 0, ',', '.') }}</span>
        </div>
    @empty
        <div class="text-center" style="font-style: italic;">Tidak ada transaksi terbayar</div>
    @endforelse

    <div class="flex font-bold" style="margin-top: 3px; border-top: 1px dotted #000; padding-top: 2px;">
        <span>TOTAL OMZET ({{ $totalOrdersCount }} trx):</span>
        <span>Rp {{ number_format($grandTotalSales, 0, ',', '.') }}</span>
    </div>

    <div class="divider"></div>

    <div class="font-bold" style="margin-bottom: 2px;">ARUS KAS FISIK LACI:</div>
    <div class="flex">
        <span>Modal Awal Kas:</span>
        <span>Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</span>
    </div>
    @php
        $cashSales = (int) $shift->orders()->where('payment_method', 'cash')->where('status', 'paid')->sum('grand_total');
        $expectedCash = $shift->starting_cash + $cashSales;
    @endphp
    <div class="flex">
        <span>Penjualan Tunai:</span>
        <span>+Rp {{ number_format($cashSales, 0, ',', '.') }}</span>
    </div>
    <div class="flex font-bold" style="border-top: 1px dotted #000; margin-top: 2px; padding-top: 2px;">
        <span>Target Kas Fisik:</span>
        <span>Rp {{ number_format($expectedCash, 0, ',', '.') }}</span>
    </div>
    <div class="flex font-bold">
        <span>Kas Fisik Terhitung:</span>
        <span>Rp {{ number_format($shift->actual_ending_cash ?? 0, 0, ',', '.') }}</span>
    </div>

    <div class="divider-double"></div>

    <div class="flex font-bold" style="font-size: 12px;">
        <span>SELISIH KAS:</span>
        <span>
            @if ($shift->difference === 0)
                PAS (Rp 0)
            @elseif ($shift->difference > 0)
                +Rp {{ number_format($shift->difference, 0, ',', '.') }} (LEBIH)
            @else
                -Rp {{ number_format(abs($shift->difference), 0, ',', '.') }} (KURANG)
            @endif
        </span>
    </div>

    <div class="divider"></div>

    <div class="signature-space">
        <div>( ........................... )</div>
        <div style="font-size: 9px; margin-top: 2px;">Tanda Tangan Kasir / Spv</div>
        <div style="font-size: 8px; margin-top: 4px; color: #555;">Dicetak: {{ now()->format('d/m/Y H:i:s') }}</div>
    </div>

</body>
</html>