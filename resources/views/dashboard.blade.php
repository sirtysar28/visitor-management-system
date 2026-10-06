@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- ====== Stat cards (animasi counter) ====== --}}
<div class="grid grid-4">
    <div class="stat-card blue anim-rise">
        <span class="stat-bg"></span>
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div class="stat-value" data-count="{{ $stats['total'] }}">0</div>
        <div class="stat-label">Total Kunjungan</div>
    </div>

    <div class="stat-card violet anim-rise d1">
        <span class="stat-bg"></span>
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/><path d="M9 16l2 2 4-4"/></svg>
        </div>
        <div class="stat-value" data-count="{{ $stats['today'] }}">0</div>
        <div class="stat-label">Jadwal Hari Ini</div>
    </div>

    <div class="stat-card green anim-rise d2">
        <span class="stat-bg"></span>
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9"/><path d="M12 7v5l3 3"/><path d="M3 3v5h5"/></svg>
        </div>
        <div class="stat-value" data-count="{{ $stats['inside'] }}">0</div>
        <div class="stat-label">Sedang Di Dalam</div>
    </div>

    <div class="stat-card amber anim-rise d3">
        <span class="stat-bg"></span>
        <div class="stat-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        </div>
        <div class="stat-value" data-count="{{ $stats['pending'] }}">0</div>
        <div class="stat-label">Menunggu Approval</div>
    </div>
</div>

{{-- ====== Charts ====== --}}
<div class="grid row-charts" id="chartsRow" style="margin-top:20px">
    <div class="card anim-rise d2">
        <div class="card-title">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg>
            Kunjungan 7 Hari Terakhir
        </div>
        <div class="chart-box">
            <canvas id="barChart"
                    data-labels='{{ json_encode($barLabels) }}'
                    data-data='{{ json_encode($barData) }}'></canvas>
        </div>
    </div>

    <div class="card anim-rise d3">
        <div class="card-title">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#7c3aed" stroke-width="2"><circle cx="8" cy="8" r="6"/><circle cx="16" cy="8" r="6"/><circle cx="12" cy="14" r="6"/></svg>
            Segmen Kunjungan (Venn)
        </div>
        <div class="venn-wrap">
            <svg width="18" height="18" class="venn-svg" id="vennDiagram" viewBox="0 0 320 300"
                 data-pre="{{ $venn['pre'] }}" data-walk="{{ $venn['walk'] }}" data-vendor="{{ $venn['vendor'] }}"></svg>
            <div class="venn-legend">
                <div class="lg-item"><span class="lg-dot" style="background:#2563eb"></span> Pre-Registered <span class="lg-val" data-venn-count="pre">0</span></div>
                <div class="lg-item"><span class="lg-dot" style="background:#7c3aed"></span> Walk-In <span class="lg-val" data-venn-count="walk">0</span></div>
                <div class="lg-item"><span class="lg-dot" style="background:#06b6d4"></span> Vendor <span class="lg-val" data-venn-count="vendor">0</span></div>
            </div>
        </div>
    </div>
</div>

{{-- ====== Status donut + di dalam gedung ====== --}}
<div class="grid row-status" id="row2" style="margin-top:20px">
    <div class="card anim-rise d4">
        <div class="card-title">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#0891b2" stroke-width="2"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
            Distribusi Status
        </div>
        <div class="chart-box chart-box-sm">
            <canvas id="statusChart"
                    data-labels='{{ json_encode(collect(\App\Models\Visitor::STATUSES)->map(fn($l, $k) => $l . ' (' . ($statusDist[$k] ?? 0) . ')')->values()->all()) }}'
                    data-data='{{ json_encode(collect(\App\Models\Visitor::STATUSES)->keys()->map(fn($k) => $statusDist[$k] ?? 0)->all()) }}'></canvas>
        </div>
    </div>

    <div class="card anim-rise d5">
        <div class="card-title" style="justify-content:space-between">
            <span style="display:flex;align-items:center;gap:8px">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg>
                Tamu Sedang Di Dalam Gedung
            </span>
            <span class="badge badge-checked_in">{{ $inside->count() }} orang</span>
        </div>

        @if ($inside->isEmpty())
            <div class="empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <div>Tidak ada tamu yang sedang berada di dalam gedung.</div>
            </div>
        @else
            <div class="table-wrap table-inside-wrap">
                <table class="table table-inside">
                    <thead>
                        <tr><th>Tamu</th><th>Perusahaan</th><th>Host</th><th>Check-In</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($inside as $v)
                        <tr>
                            <td data-label="Tamu"><a href="{{ route('visitors.show', $v) }}"><strong>{{ $v->full_name }}</strong></a><br><small style="color:var(--muted)">{{ $v->code }}</small></td>
                            <td data-label="Perusahaan">{{ $v->company ?: '-' }}</td>
                            <td data-label="Host">{{ $v->host?->name ?: '-' }}</td>
                            <td data-label="Check-In">{{ $v->checked_in_at?->format('H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ====== Recent visitors ====== --}}
<div class="card anim-rise d6" style="margin-top:20px">
    <div class="card-title" style="justify-content:space-between">
        <span style="display:flex;align-items:center;gap:8px">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
            Registrasi Terbaru
        </span>
        <a href="{{ route('visitors.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Kode</th><th>Tamu</th><th>Tipe</th><th>Tanggal</th><th>Host</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($recent as $v)
                <tr>
                    <td><a href="{{ route('visitors.show', $v) }}"><strong>{{ $v->code }}</strong></a></td>
                    <td>{{ $v->full_name }}<br><small style="color:var(--muted)">{{ $v->company ?: '-' }}</small></td>
                    <td>{{ $v->visitTypeLabel() }}</td>
                    <td>{{ $v->visit_date->format('d M Y') }}</td>
                    <td>{{ $v->host?->name ?: '-' }}</td>
                    <td><span class="badge badge-{{ $v->status }}">{{ $v->statusLabel() }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:30px">Belum ada data registrasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/vendor/chart.umd.min.js?v=2') }}"></script>
<script src="{{ asset('assets/js/dashboard.js?v=2') }}"></script>
@endpush
