@extends('layouts.app')

@section('title', 'Approval')
@section('page-title', 'Approval Kunjungan')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Menunggu Approval</h1>
        <p class="page-sub">Setujui atau tolak permintaan kunjungan tamu (Level 1 — Host/PIC).</p>
    </div>
</div>

<div class="card anim-rise">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th><th>Tamu</th><th>Tujuan</th><th>Jadwal</th>
                    <th>Host</th><th>Department</th><th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($visitors as $v)
                <tr>
                    <td><a href="{{ route('visitors.show', $v) }}"><strong>{{ $v->code }}</strong></a></td>
                    <td>{{ $v->full_name }}<br><small style="color:var(--muted)">{{ $v->company ?: '-' }}</small></td>
                    <td>{{ $v->purpose ?: '-' }}</td>
                    <td>{{ $v->visit_date->format('d M Y') }} {{ $v->visit_time?->format('H:i') }}</td>
                    <td>{{ $v->host?->name ?: '-' }}</td>
                    <td>{{ $v->department?->name ?: '-' }}</td>
                    <td style="text-align:right;white-space:nowrap">
                        <form method="POST" action="{{ route('approvals.decide', $v) }}" style="display:flex;gap:8px;justify-content:flex-end;align-items:center">
                            @csrf
                            <input type="hidden" name="decision" value="approve">
                            <button class="btn btn-success btn-sm" data-confirm="Setujui kunjungan {{ $v->full_name }}?">✓ Setujui</button>
                        </form>
                        <form method="POST" action="{{ route('approvals.decide', $v) }}" style="display:flex;gap:8px;justify-content:flex-end;align-items:center;margin-top:6px" data-confirm="Tolak kunjungan {{ $v->full_name }}?">
                            @csrf
                            <input type="hidden" name="decision" value="reject">
                            <input type="text" name="note" placeholder="Alasan (ops.)" style="padding:6px 10px;border:1.5px solid var(--border);border-radius:9px;font-size:12px;width:130px">
                            <button class="btn btn-danger btn-sm">✕ Tolak</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:36px">
                    🎉 Tidak ada permintaan yang menunggu approval.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $visitors->links() }}
</div>
@endsection
