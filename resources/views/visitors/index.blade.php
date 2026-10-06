@extends('layouts.app')

@section('title', 'Data Tamu')
@section('page-title', 'Data Tamu')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Data Tamu</h1>
        <p class="page-sub">Kelola seluruh registrasi tamu — pre-registered, walk-in, vendor, dan delivery.</p>
    </div>
    <a href="{{ route('visitors.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
        Registrasi Tamu
    </a>
</div>

<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Cari nama / kode / perusahaan..." value="{{ $filters['search'] ?? '' }}">
    <select name="status" class="form-select">
        <option value="">Semua Status</option>
        @foreach ($statuses as $key => $label)
            <option value="{{ $key }}" {{ ($filters['status'] ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <select name="visit_type" class="form-select">
        <option value="">Semua Tipe</option>
        @foreach ($visitTypes as $key => $label)
            <option value="{{ $key }}" {{ ($filters['visit_type'] ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    <input type="date" name="date" class="form-control" value="{{ $filters['date'] ?? '' }}">
    <button class="btn btn-primary" type="submit">Filter</button>
    <a href="{{ route('visitors.index') }}" class="btn btn-outline">Reset</a>
</form>

<div class="card anim-rise">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Kode</th><th>Tamu</th><th>Tipe</th><th>Kunjungan</th>
                    <th>Host</th><th>Status</th><th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($visitors as $v)
                <tr>
                    <td><a href="{{ route('visitors.show', $v) }}"><strong>{{ $v->code }}</strong></a></td>
                    <td>
                        {{ $v->full_name }}<br>
                        <small style="color:var(--muted)">{{ $v->company ?: '-' }} · {{ $v->person_count }} org</small>
                    </td>
                    <td>{{ $v->visitTypeLabel() }}</td>
                    <td>{{ $v->visit_date->format('d M Y') }} {{ $v->visit_time?->format('H:i') }}</td>
                    <td>{{ $v->host?->name ?: '-' }}</td>
                    <td><span class="badge badge-{{ $v->status }}">{{ $v->statusLabel() }}</span></td>
                    <td style="text-align:right;white-space:nowrap">
                        @if ($v->isInside())
                            <form method="POST" action="{{ route('visitors.checkout', $v) }}" style="display:inline">
                                @csrf
                                <button class="btn btn-warning btn-sm">Check-Out</button>
                            </form>
                        @elseif ($v->status === \App\Models\Visitor::STATUS_APPROVED)
                            <form method="POST" action="{{ route('visitors.checkin', $v) }}" style="display:inline">
                                @csrf
                                <button class="btn btn-success btn-sm">Check-In</button>
                            </form>
                        @endif
                        <a href="{{ route('visitors.show', $v) }}" class="btn btn-outline btn-sm">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:36px">Tidak ada data tamu ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $visitors->links() }}
</div>
@endsection
