@extends('layouts.app')

@section('title', 'Pengguna')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Pengguna</h1>
        <p class="page-sub">Kelola akun & role: admin, receptionist, security, host, dll.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
        Tambah User
    </a>
</div>

<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Cari nama / email..." value="{{ request('search') }}">
    <button class="btn btn-primary">Cari</button>
    <a href="{{ route('users.index') }}" class="btn btn-outline">Reset</a>
</form>

<div class="card anim-rise">
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Nama</th><th>Email</th><th>Role</th><th>Department</th><th>Kontak</th><th>Status</th><th style="text-align:right">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <span class="avatar" style="width:32px;height:32px;font-size:12px">{{ strtoupper(substr($u->name, 0, 1)) }}</span>
                            <div>
                                <strong>{{ $u->name }}</strong>
                                @if ($u->id === auth()->id())<span class="badge badge-dot" style="margin-left:6px">Anda</span>@endif
                                @if ($u->position)<br><small style="color:var(--muted)">{{ $u->position }}</small>@endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge badge-dot">{{ $u->roleLabel() }}</span></td>
                    <td>{{ $u->department?->name ?: '-' }}</td>
                    <td>{{ $u->phone ?: '-' }}</td>
                    <td><span class="badge {{ $u->is_active ? 'badge-approved' : 'badge-cancelled' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td style="text-align:right;white-space:nowrap">
                        <a href="{{ route('users.edit', $u) }}" class="btn btn-outline btn-sm">Edit</a>
                        @if ($u->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $u) }}" style="display:inline" data-confirm="Hapus user {{ $u->name }}?">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:36px">Tidak ada user ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</div>
@endsection
