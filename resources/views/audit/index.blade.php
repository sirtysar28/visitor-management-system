@extends('layouts.app')

@section('title', 'Audit Log')
@section('page-title', 'Audit Log')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Audit Log</h1>
        <p class="page-sub">Jejak seluruh aktivitas sistem — login, perubahan data, approval, check-in/out.</p>
    </div>
    <a href="{{ route('audit.export') }}" class="btn btn-outline">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
        Export CSV
    </a>
</div>

<div class="card anim-rise">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Modul</th><th>Deskripsi</th><th>IP</th></tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                <tr>
                    <td style="white-space:nowrap">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                    <td>{{ $log->user?->name ?? 'System' }}</td>
                    <td>
                        <span class="badge {{ str_contains($log->action, 'delete') || str_contains($log->action, 'reject') || str_contains($log->action, 'failed') ? 'badge-rejected' : (str_contains($log->action, 'login') || str_contains($log->action, 'check_in') ? 'badge-checked_in' : 'badge-dot') }}">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td>{{ $log->module ?: '-' }}</td>
                    <td>{{ $log->description ?: '-' }}</td>
                    <td>{{ $log->ip_address ?: '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:36px">Belum ada aktivitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $logs->links() }}
</div>
@endsection
