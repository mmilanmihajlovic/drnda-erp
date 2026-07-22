<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Racun {{ $invoice->invoice_number }}</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');

*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Plus Jakarta Sans',sans-serif;font-size:12px;color:#111;background:white;padding:0}

.page{max-width:800px;margin:0 auto;padding:40px 40px 60px}

/* Header */
.inv-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:32px;padding-bottom:20px;border-bottom:2px solid #c9a84c}
.company-name{font-size:22px;font-weight:700;color:#1a1f2e;letter-spacing:.02em}
.company-sub{font-size:11px;color:#6b7280;margin-top:3px}
.inv-title{text-align:right}
.inv-title h1{font-size:26px;font-weight:700;color:#c9a84c;letter-spacing:.04em;font-family:'IBM Plex Mono',monospace}
.inv-title .inv-date{font-size:11px;color:#6b7280;margin-top:4px}

/* Meta */
.inv-meta{display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:28px}
.meta-block label{font-size:9px;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;font-weight:600;display:block;margin-bottom:3px}
.meta-block .val{font-size:13px;font-weight:600;color:#111}
.meta-block .val-sub{font-size:11px;color:#6b7280;margin-top:1px}

/* Department section */
.dept-section{margin-bottom:20px}
.dept-title{background:#1a1f2e;color:#c9a84c;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:6px 12px;margin-bottom:0}
table{width:100%;border-collapse:collapse}
thead th{background:#f9fafb;padding:7px 12px;text-align:left;font-size:9px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;border-bottom:1px solid #e5e7eb}
thead th.r{text-align:right}
tbody td{padding:7px 12px;font-size:11.5px;border-bottom:1px solid #f3f4f6}
tbody td.r{text-align:right;font-family:'IBM Plex Mono',monospace}
tfoot td{padding:7px 12px;font-size:11px;font-weight:700;background:#f9fafb;border-top:1px solid #e5e7eb}
tfoot td.r{text-align:right;font-family:'IBM Plex Mono',monospace}

/* Totals */
.totals{margin-top:20px;display:flex;justify-content:flex-end}
.totals-table{min-width:280px;border:1px solid #e5e7eb;border-radius:6px;overflow:hidden}
.totals-table tr td{padding:8px 14px;font-size:12px;border-bottom:1px solid #e5e7eb}
.totals-table tr:last-child td{border-bottom:none;background:#1a1f2e;color:white;font-weight:700;font-size:14px}
.totals-table tr:last-child td.r{font-family:'IBM Plex Mono',monospace;color:#c9a84c}

/* Status / signature */
.inv-footer{margin-top:40px;padding-top:20px;border-top:1px solid #e5e7eb;display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px}
.sig-box{border:1px solid #e5e7eb;border-radius:4px;padding:10px;min-height:70px}
.sig-label{font-size:9px;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;font-weight:600;margin-bottom:4px}

.status-badge{display:inline-block;padding:3px 10px;border-radius:12px;font-size:10px;font-weight:700;letter-spacing:.04em}
.s-neplaceno{background:#fee2e2;color:#991b1b}
.s-placeno{background:#d1fae5;color:#065f46}
.s-delimicno{background:#fef9c3;color:#854d0e}

/* Print */
@media print {
    body{background:white}
    .page{padding:20mm 20mm 25mm}
    .no-print{display:none}
    @page{size:A4;margin:0}
}
</style>
</head>
<body>

<div class="page">

    <!-- Header -->
    <div class="inv-header">
        <div>
            <div class="company-name">DRNDA</div>
            <div class="company-sub">Pogrebno preduzece</div>
        </div>
        <div class="inv-title">
            <h1>RACUN</h1>
            <div class="inv-date">Broj: {{ $invoice->invoice_number }}</div>
            <div class="inv-date">Datum: {{ $invoice->issue_date->format('d.m.Y') }}</div>
            <div class="inv-date" style="margin-top:6px">
                <span class="status-badge
                    @switch($invoice->payment_status)
                        @case('neplaceno') s-neplaceno @break
                        @case('placeno') s-placeno @break
                        @default s-delimicno
                    @endswitch">
                    {{ strtoupper(['neplaceno'=>'NEPLACENO','delimicno_placeno'=>'DELIMICNO PLACENO','placeno'=>'PLACENO','stornirano'=>'STORNIRANO'][$invoice->payment_status] ?? $invoice->payment_status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Meta -->
    <div class="inv-meta">
        <div class="meta-block">
            <label>Kupac / Porodica</label>
            <div class="val">{{ $invoice->customer_name }}</div>
        </div>
        <div class="meta-block">
            <label>Slucaj</label>
            <div class="val" style="font-family:'IBM Plex Mono',monospace">{{ $invoice->funeralCase->case_number }}</div>
            <div class="val-sub">{{ $invoice->funeralCase->deceased_name }}</div>
        </div>
        @if($invoice->funeralCase->funeral_at)
        <div class="meta-block">
            <label>Datum sahrane</label>
            <div class="val">{{ $invoice->funeralCase->funeral_at->format('d.m.Y H:i') }}</div>
        </div>
        @endif
        @if($invoice->funeralCase->display_location)
        <div class="meta-block">
            <label>Mesto</label>
            <div class="val">{{ $invoice->funeralCase->display_location }}</div>
        </div>
        @endif
    </div>

    <!-- Items by department -->
    @foreach($byDept as $deptId => $items)
    @php $dept = $items->first()->department @endphp
    <div class="dept-section">
        <div class="dept-title">{{ $dept->name ?? 'Odeljenje' }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width:50%">Opis</th>
                    <th class="r" style="width:12%">Kol.</th>
                    <th class="r" style="width:19%">Jed. cena (RSD)</th>
                    <th class="r" style="width:19%">Ukupno (RSD)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="r">{{ $item->quantity }}</td>
                    <td class="r">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="r">{{ number_format($item->total, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right;color:#6b7280">Medjuzbir {{ $dept->name }}:</td>
                    <td class="r">{{ number_format($items->sum('total'), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endforeach

    <!-- Totals -->
    <div class="totals">
        <table class="totals-table">
            <tr>
                <td style="color:#6b7280">Ukupno bez PDV:</td>
                <td class="r" style="text-align:right;font-family:'IBM Plex Mono',monospace">{{ number_format($invoice->total, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="color:#6b7280">PDV (0%):</td>
                <td class="r" style="text-align:right;font-family:'IBM Plex Mono',monospace">0,00</td>
            </tr>
            <tr>
                <td>UKUPNO ZA UPLATU (RSD):</td>
                <td class="r">{{ number_format($invoice->total, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- Footer / Signature -->
    <div class="inv-footer">
        <div class="sig-box">
            <div class="sig-label">Nacin placanja</div>
            <div style="font-size:11px;color:#6b7280;margin-top:4px">Gotovina / Kartica / Prenos</div>
        </div>
        <div class="sig-box">
            <div class="sig-label">Potpis primaoca</div>
        </div>
        <div class="sig-box">
            <div class="sig-label">Pectat i potpis izdavaoca</div>
        </div>
    </div>

    <div style="margin-top:20px;font-size:9px;color:#9ca3af;text-align:center">
        Generisano: {{ $invoice->generated_at?->format('d.m.Y H:i') }} | DRNDA ERP 3.0
    </div>
</div>

<div class="no-print" style="position:fixed;bottom:24px;right:24px">
    <button onclick="window.print()"
            style="background:#c9a84c;color:white;border:none;padding:10px 20px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 4px 12px rgba(0,0,0,.2)">
        🖨️ Stampaj / Sacuvaj PDF
    </button>
</div>

</body>
</html>
