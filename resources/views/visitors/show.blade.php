@extends('layouts.app')

@section('title', 'Detail Tamu')
@section('page-title', 'Detail Tamu')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">{{ $visitor->full_name }}</h1>
        <p class="page-sub">{{ $visitor->code }} · {{ $visitor->visitTypeLabel() }} · <span class="badge badge-{{ $visitor->status }}">{{ $visitor->statusLabel() }}</span></p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        @if ($visitor->isInside())
            <form method="POST" action="{{ route('visitors.checkout', $visitor) }}">
                @csrf
                <button class="btn btn-warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                    Check-Out
                </button>
            </form>
        @elseif ($visitor->status === \App\Models\Visitor::STATUS_APPROVED)
            <form method="POST" action="{{ route('visitors.checkin', $visitor) }}">
                @csrf
                <button class="btn btn-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
                    Check-In
                </button>
            </form>
        @endif
        <a href="{{ route('visitors.edit', $visitor) }}" class="btn btn-outline">Edit</a>
        <form method="POST" action="{{ route('visitors.destroy', $visitor) }}" data-confirm="Hapus registrasi {{ $visitor->code }}?">
            @csrf @method('DELETE')
            <button class="btn btn-danger">Hapus</button>
        </form>
    </div>
</div>

<div class="grid" style="grid-template-columns: 1fr 1.7fr" id="detailRow">
    {{-- QR & badge --}}
    <div class="card anim-rise" style="text-align:center">
        <div class="card-title" style="justify-content:center">QR Badge Tamu</div>

        <div class="qr-box">
            {!! QrCode::size(190)->margin(1)->generate($visitor->badge_code ?? $visitor->code) !!}
        </div>

        <p style="font-size:12.5px;color:var(--muted);margin:14px 0 4px">Invitation ID</p>
        <p style="font-weight:800;font-size:16px;margin:0">{{ $visitor->code }}</p>
        <p style="font-size:12.5px;color:var(--muted);margin:6px 0 0">Badge: {{ $visitor->badge_code }}</p>

        @if ($visitor->photo)
            <div style="margin-top:18px">
                <img src="{{ asset('storage/' . $visitor->photo) }}" alt="Foto" style="max-width:180px;border-radius:14px;border:1px solid var(--border)">
            </div>
        @endif
    </div>

    <div>
        <div class="card anim-rise d1">
            <div class="card-title">Identitas Tamu</div>
            <div class="detail-grid">
                <div class="detail-item"><div class="d-label">Nama Lengkap</div><div class="d-value">{{ $visitor->full_name }}</div></div>
                <div class="detail-item"><div class="d-label">Perusahaan</div><div class="d-value">{{ $visitor->company ?: '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Nomor HP</div><div class="d-value">{{ $visitor->phone ?: '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Email</div><div class="d-value">{{ $visitor->email ?: '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Jabatan</div><div class="d-value">{{ $visitor->position ?: '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Identitas</div><div class="d-value">{{ $visitor->id_type }} {{ $visitor->id_number ? '· ' . $visitor->id_number : '' }}</div></div>
                <div class="detail-item"><div class="d-label">Jumlah Orang</div><div class="d-value">{{ $visitor->person_count }} orang</div></div>
                <div class="detail-item"><div class="d-label">Kendaraan</div><div class="d-value">{{ $visitor->vehicle_number ?: '-' }}</div></div>
            </div>
        </div>

        <div class="card anim-rise d2">
            <div class="card-title">Kunjungan</div>
            <div class="detail-grid">
                <div class="detail-item"><div class="d-label">Tujuan</div><div class="d-value">{{ $visitor->purpose ?: '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Tanggal & Jam</div><div class="d-value">{{ $visitor->visit_date->format('d M Y') }} · {{ $visitor->visit_time?->format('H:i') }}</div></div>
                <div class="detail-item"><div class="d-label">Durasi</div><div class="d-value">{{ $visitor->duration_minutes ? $visitor->duration_minutes . ' menit' : '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Host / PIC</div><div class="d-value">{{ $visitor->host?->name ?: '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Department</div><div class="d-value">{{ $visitor->department?->name ?: '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Meeting Room</div><div class="d-value">{{ $visitor->meeting_room ?: '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Area Akses</div><div class="d-value">{{ $visitor->access_area ?: '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Catatan</div><div class="d-value">{{ $visitor->notes ?: '-' }}</div></div>
            </div>
        </div>

        <div class="card anim-rise d3">
            <div class="card-title">Riwayat Status & Approval</div>

            <div class="detail-grid" style="margin-bottom:14px">
                <div class="detail-item"><div class="d-label">Disetujui Oleh</div><div class="d-value">{{ $visitor->approved_by ? \App\Models\User::find($visitor->approved_by)?->name : '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Waktu Approval</div><div class="d-value">{{ $visitor->approved_at?->format('d M Y H:i') ?? '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Check-In</div><div class="d-value">{{ $visitor->checked_in_at?->format('d M Y H:i') ?? '-' }}</div></div>
                <div class="detail-item"><div class="d-label">Check-Out</div><div class="d-value">{{ $visitor->checked_out_at?->format('d M Y H:i') ?? '-' }}</div></div>
            </div>

            @if ($visitor->reject_reason)
                <div class="alert alert-danger" style="margin:0">Alasan ditolak: {{ $visitor->reject_reason }}</div>
            @endif

            @if ($visitor->approvals->isNotEmpty())
                <div class="table-wrap">
                    <table class="table">
                        <thead><tr><th>Level</th><th>Approver</th><th>Status</th><th>Catatan</th><th>Waktu</th></tr></thead>
                        <tbody>
                            @foreach ($visitor->approvals as $a)
                            <tr>
                                <td>{{ ucfirst($a->level) }}</td>
                                <td>{{ $a->approver?->name }}</td>
                                <td><span class="badge badge-{{ $a->status }}">{{ ucfirst($a->status) }}</span></td>
                                <td>{{ $a->note ?: '-' }}</td>
                                <td>{{ $a->acted_at?->format('d M H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
@media (max-width: 991px) { #detailRow { grid-template-columns: 1fr !important; } }
</style>
@endsection
