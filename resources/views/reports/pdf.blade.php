<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kunjungan</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #0f172a; font-size: 11px; margin: 0; }

        .head { display: flex; align-items: center; gap: 14px; border-bottom: 3px solid #2563eb; padding-bottom: 12px; margin-bottom: 14px; }
        .logo { width: 46px; height: 46px; }
        h1 { font-size: 17px; margin: 0; color: #0f172a; }
        .sub { color: #64748b; font-size: 11px; margin-top: 3px; }

        .meta { background: #f1f5f9; border-radius: 8px; padding: 10px 14px; margin-bottom: 12px; line-height: 1.7; }
        .meta b { color: #0f172a; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #2563eb; color: #fff; padding: 7px 8px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: .04em; }
        td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) td { background: #f8fafc; }

        .status { padding: 2px 8px; border-radius: 99px; font-size: 9px; font-weight: bold; white-space: nowrap; }
        .st-pending { background: #fef3c7; color: #92400e; }
        .st-approved { background: #dcfce7; color: #166534; }
        .st-rejected { background: #fee2e2; color: #991b1b; }
        .st-checked_in { background: #cffafe; color: #155e75; }
        .st-checked_out { background: #e2e8f0; color: #334155; }
        .st-cancelled { background: #f1f5f9; color: #64748b; }

        .sumrow { display: flex; gap: 10px; margin-bottom: 12px; }
        .sumcard { flex: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 12px; background: #fff; }
        .sumcard .n { font-size: 16px; font-weight: bold; }
        .sumcard .l { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }

        .footer { margin-top: 16px; padding-top: 8px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; color: #94a3b8; font-size: 9px; }
        .footer a { color: #2563eb; text-decoration: none; }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <div class="head">
        <img class="logo" src="{{ public_path('assets/img/icons/icon-512.png') }}" alt="">
        <div>
            <h1>Laporan Kunjungan Tamu — {{ \App\Support\Vms::appName() }}</h1>
            <div class="sub">
                Periode: {{ $from->translatedFormat('d F Y') }} s/d {{ $to->translatedFormat('d F Y') }}<br>
                Dicetak: {{ now()->translatedFormat('d F Y H:i') }} oleh {{ auth()->user()->name }}
            </div>
        </div>
    </div>

    <div class="sumrow">
        <div class="sumcard"><div class="n">{{ $summary['total'] }}</div><div class="l">Total</div></div>
        <div class="sumcard"><div class="n">{{ $summary['pending'] }}</div><div class="l">Pending</div></div>
        <div class="sumcard"><div class="n">{{ $summary['inside'] }}</div><div class="l">Di Dalam</div></div>
        <div class="sumcard"><div class="n">{{ $summary['done'] }}</div><div class="l">Selesai</div></div>
        <div class="sumcard"><div class="n">{{ $summary['rejected'] }}</div><div class="l">Ditolak</div></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:24px">No</th>
                <th>Kode</th>
                <th>Nama Tamu</th>
                <th>Perusahaan</th>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th>Host</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($visitors as $i => $v)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $v->code }}</td>
                <td>{{ $v->full_name }}</td>
                <td>{{ $v->company ?: '-' }}</td>
                <td>{{ $v->visitTypeLabel() }}</td>
                <td>{{ $v->visit_date->format('d/m/Y') }}</td>
                <td>{{ $v->host?->name ?? '-' }}</td>
                <td>{{ $v->checked_in_at?->format('d/m H:i') ?? '-' }}</td>
                <td>{{ $v->checked_out_at?->format('d/m H:i') ?? '-' }}</td>
                <td><span class="status st-{{ $v->status }}">{{ $v->statusLabel() }}</span></td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center;padding:20px;color:#64748b">Tidak ada data pada rentang tanggal ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span>&copy; {{ date('Y') }} {{ \App\Support\Vms::appName() }} — All rights reserved.</span>
        <span>Developed by <a href="https://trijayasolution.com">Trijaya Solution</a></span>
    </div>
</body>
</html>
