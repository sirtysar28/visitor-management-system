@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Semua Notifikasi')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Notifikasi</h1>
        <p class="page-sub">Seluruh riwayat notifikasi akun Anda.</p>
    </div>
    <form method="POST" action="{{ route('notifications.markRead') }}">
        @csrf
        <button class="btn btn-outline">Tandai semua dibaca</button>
    </form>
</div>

<div class="card anim-rise">
    @forelse ($notifications as $n)
        <a href="{{ $n->url ?: '#' }}" class="notif-item {{ $n->is_read ? '' : 'unread' }}" style="border-bottom:1px solid #f1f5f9">
            <span class="notif-ico" style="background:var(--primary-soft);color:var(--primary)">
                @switch($n->icon)
                    @case('check')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg>@break
                    @case('x')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>@break
                    @case('user')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>@break
                    @case('shield')<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>@break
                    @default<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                @endswitch
            </span>
            <span style="min-width:0">
                <span class="notif-title">{{ $n->title }}</span>
                @if ($n->body)<span class="notif-body">{{ $n->body }}</span>@endif
                <span class="notif-time">{{ $n->created_at?->locale('id')->translatedFormat('l, d F Y H:i') }}</span>
            </span>
        </a>
    @empty
        <div class="empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <div>Belum ada notifikasi.</div>
        </div>
    @endforelse

    {{ $notifications->links() }}
</div>
@endsection
