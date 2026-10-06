@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Laporan Kunjungan')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Laporan Kunjungan</h1>
        <p class="page-sub">Rekap registrasi tamu berdasarkan rentang tanggal & status — siap diunduh PDF / Excel.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="{{ route('reports.export', ['format' => 'pdf']) . '?' . http_build_query(request()->only(['from','to','status'])) }}"
           class="btn btn-danger" target="_blank">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M12 18v-6"/><path d="M9 15l3 3 3-3"/></svg>
            Export PDF
        </a>
        <a href="{{ route('reports.export', ['format' => 'excel']) . '?' . http_build_query(request()->only(['from','to','status'])) }}"
           class="btn btn-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
            Export Excel
        </a>
    </div>
</div>

{{-- Ringkasan --}}
<div class="grid grid-4" style="margin-bottom:20px">
    <div class="stat-card blue anim-rise">
        <span class="stat-bg"></span>
        <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/></svg></div>
        <div class="stat-value" data-count="{{ $summary['total'] }}">0</div>
        <div class="stat-label">Total Kunjungan</div>
    </div>
    <div class="stat-card violet anim-rise d1">
        <span class="stat-bg"></span>
        <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg></div>
        <div class="stat-value" data-count="{{ $summary['pending'] }}">0</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card green anim-rise d2">
        <span class="stat-bg"></span>
        <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9"/><path d="M12 7v5l3 3"/></svg></div>
        <div class="stat-value" data-count="{{ $summary['inside'] }}">0</div>
        <div class="stat-label">Di Dalam Gedung</div>
    </div>
    <div class="stat-card amber anim-rise d3">
        <span class="stat-bg"></span>
        <div class="stat-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></div>
        <div class="stat-value" data-count="{{ $summary['done'] }}">0</div>
        <div class="stat-label">Selesai (Check-Out)</div>
    </div>
</div>

<form method="GET" class="filter-bar">
    <label style="display:flex;align-items:center;font-weight:600;font-size:13px">Dari</label>
    <input type="date" name="from" class="form-control" value="{{ $from }}">
    <label style="display:flex;align-items:center;font-weight:600;font-size:13px">Sampai</label>
    <input type="date" name="to" class="form-control" value="{{ $to }}">
    <select name="status" class="form-select">
        <option value="">Semua Status</option>
        @foreach ($statuses as $key => $label)
            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <button class="btn btn-primary">Tampilkan</button>
</form>

<div class="card anim-rise">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Kode</th><th>Tamu</th><th>Tipe</th><th>Tanggal</th><th>Host</th><th>Check-In</th><th>Check-Out</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($visitors as $v)
                <tr>
                    <td><a href="{{ route('visitors.show', $v) }}"><strong>{{ $v->code }}</strong></a></td>
                    <td>{{ $v->full_name }}<br><small style="color:var(--muted)">{{ $v->company ?: '-' }}</small></td>
                    <td>{{ $v->visitTypeLabel() }}</td>
                    <td>{{ $v->visit_date->format('d M Y') }}</td>
                    <td>{{ $v->host?->name ?: '-' }}</td>
                    <td>{{ $v->checked_in_at?->format('d/m H:i') ?? '-' }}</td>
                    <td>{{ $v->checked_out_at?->format('d/m H:i') ?? '-' }}</td>
                    <td><span class="badge badge-{{ $v->status }}">{{ $v->statusLabel() }}</span></td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:36px">Tidak ada data pada rentang tersebut.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $visitors->links() }}
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/dashboard.js?v=2') }}"></script>
@endpush
