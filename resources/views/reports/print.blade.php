<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Kunjungan — {{ \App\Support\Vms::appName() }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: #0f172a; font-size: 13px; margin: 0; background: #f8fafc; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 24px 20px 60px; }

        .no-print { position: sticky; top: 0; z-index: 9; }
        .banner { display: flex; gap: 12px; align-items: center; justify-content: space-between;
                  background: #fffbeb; border: 1px solid #fcd34d; border-radius: 12px; padding: 12px 18px; margin-bottom: 18px; flex-wrap: wrap; }
        .banner b { color: #92400e; }
        .btn { border: none; border-radius: 10px; padding: 10px 18px; font-weight: 600; cursor: pointer; font-size: 13px; }
        .btn-primary { background: #2563eb; color: #fff; }

        .sheet { background: #fff; border-radius: 14px; padding: 28px; box-shadow: 0 4px 24px rgba(15,23,42,.06); }
        .head { display: flex; align-items: center; gap: 14px; border-bottom: 3px solid #2563eb; padding-bottom: 12px; margin-bottom: 14px; }
        .logo { width: 46px; height: 46px; }
        h1 { font-size: 17px; margin: 0; }
        .sub { color: #64748b; font-size: 11px; margin-top: 3px; line-height: 1.6; }

        .sumrow { display: flex; gap: 10px; margin-bottom: 14px; }
        .sumcard { flex: 1; border: 1px solid #e2e8f0; border-radius: 10px; padding: 10px 14px; }
        .sumcard .n { font-size: 18px; font-weight: 700; }
        .sumcard .l { font-size: 9px; color: #64748b; text-transform: uppercase; letter-spacing: .05em; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #2563eb; color: #fff; padding: 8px 10px; text-align: left; font-size: 10.5px; text-transform: uppercase; letter-spacing: .04em; }
        td { padding: 7px 10px; border-bottom: 1px solid #e2e8f0; }
        tr:nth-child(even) td { background: #f8fafc; }

        .status { padding: 2px 9px; border-radius: 99px; font-size: 9.5px; font-weight: 700; white-space: nowrap; }
        .st-pending { background: #fef3c7; color: #92400e; }
        .st-approved { background: #dcfce7; color: #166534; }
        .st-rejected { background: #fee2e2; color: #991b1b; }
        .st-checked_in { background: #cffafe; color: #155e75; }
        .st-checked_out { background: #e2e8f0; color: #334155; }
        .st-cancelled { background: #f1f5f9; color: #64748b; }

        .footer { margin-top: 18px; padding-top: 10px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; color: #94a3b8; font-size: 10px; }
        .footer a { color: #2563eb; text-decoration: none; }

        @media print {
            body { background: #fff; font-size: 11px; }
            .no-print { display: none !important; }
            .sheet { box-shadow: none; padding: 0; border-radius: 0; }
            @page { size: A4 landscape; margin: 12mm; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="no-print">
        <div class="banner">
            <span><b>Mode Cetak.</b> Untuk menyimpan sebagai PDF: tekan tombol <b>Cetak / Simpan PDF</b> lalu pilih <i>"Save as PDF"</i> pada dialog printer.</span>
            <button class="btn btn-primary" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        </div>
    </div>

    <div class="sheet" id="sheet">
        <div class="head">
            <img class="logo" src="{{ asset('assets/img/icons/icon-512.png') }}" alt="">
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
                <th style="width:28px">No</th><th>Kode</th><th>Nama Tamu</th><th>Perusahaan</th>
                <th>Tipe</th><th>Tanggal</th><th>Host</th><th>Check-In</th><th>Check-Out</th><th>Status</th>
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
    </div>
</div>

<script>
    // buka dialog cetak otomatis saat halaman dibuka di tab baru
    window.addEventListener('load', function () {
        setTimeout(function () { window.print(); }, 400);
    });
</script>
</body>
</html>
