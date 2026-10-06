@extends('layouts.app')

@section('title', 'Blacklist & Watchlist')
@section('page-title', 'Blacklist & Watchlist')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Blacklist & Watchlist</h1>
        <p class="page-sub">Daftar orang yang dilarang / diawasi. Registrasi akan otomatis diblokir jika cocok.</p>
    </div>
    <button class="btn btn-primary" id="toggleBlacklistForm" type="button">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Data
    </button>
</div>

{{-- ====== FORM (kartu atas) ====== --}}
<div class="card anim-rise" id="blacklistFormCard" style="margin-bottom:20px">
    <div class="card-title">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l6 6M15 12l-6 6"/></svg>
        Tambah Blacklist / Watchlist
    </div>

    <form method="POST" action="{{ route('blacklist.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                <input type="text" name="full_name" class="form-control {{ $errors->has('full_name') ? 'is-invalid' : '' }}" placeholder="Nama sesuai identitas" required>
                @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nomor Identitas (NIK)</label>
                <input type="text" name="id_number" class="form-control" placeholder="16 digit NIK">
            </div>

            <div class="form-group">
                <label class="form-label">Perusahaan</label>
                <input type="text" name="company" class="form-control" placeholder="Nama perusahaan / instansi">
            </div>

            <div class="form-group">
                <label class="form-label">Nomor HP</label>
                <input type="tel" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
            </div>

            <div class="form-group">
                <label class="form-label">Tipe <span class="req">*</span></label>
                <select name="type" class="form-select">
                    <option value="blacklist">Blacklist — dilarang masuk</option>
                    <option value="watchlist">Watchlist — diawasi</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Alasan <span class="req">*</span></label>
                <input type="text" name="reason" class="form-control {{ $errors->has('reason') ? 'is-invalid' : '' }}" placeholder="Contoh: insiden keamanan 2025" required>
                @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div style="display:flex;gap:10px;align-items:center;justify-content:flex-end;flex-wrap:wrap">
            <span class="form-hint" style="margin:0">Data akan langsung aktif memblokir registrasi yang cocok.</span>
            <button class="btn btn-primary">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Tambahkan ke Daftar
            </button>
        </div>
    </form>
</div>

{{-- ====== TABEL (kartu bawah) ====== --}}
<div class="card anim-rise d1">
    <div class="card-title" style="justify-content:space-between">
        <span style="display:flex;align-items:center;gap:8px">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 6h13M8 12h13M8 18h13"/><path d="M3 6h.01M3 12h.01M3 18h.01"/></svg>
            Daftar Blacklist & Watchlist
        </span>
        <span class="badge badge-dot">{{ $blacklists->total() }} entri</span>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama</th><th>Identitas</th><th>Perusahaan</th>
                    <th>Tipe</th><th>Alasan</th><th>Ditambahkan</th><th>Status</th><th style="text-align:right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($blacklists as $b)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <span class="avatar" style="width:34px;height:34px;font-size:13px;background:linear-gradient(135deg,#dc2626,#b91c1c)">{{ strtoupper(substr($b->full_name, 0, 1)) }}</span>
                            <div>
                                <strong>{{ $b->full_name }}</strong>
                                @if ($b->phone)<br><small style="color:var(--muted)">{{ $b->phone }}</small>@endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $b->id_number ?: '-' }}</td>
                    <td>{{ $b->company ?: '-' }}</td>
                    <td><span class="badge {{ $b->type === 'blacklist' ? 'badge-rejected' : 'badge-dot' }}">{{ ucfirst($b->type) }}</span></td>
                    <td style="max-width:260px">{{ $b->reason }}</td>
                    <td style="white-space:nowrap">
                        {{ $b->created_at->format('d M Y') }}<br>
                        <small style="color:var(--muted)">oleh {{ $b->creator?->name ?? '-' }}</small>
                    </td>
                    <td>
                        <span class="badge {{ $b->is_active ? 'badge-checked_in' : 'badge-cancelled' }}">{{ $b->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </td>
                    <td style="text-align:right;white-space:nowrap">
                        <form method="POST" action="{{ route('blacklist.toggle', $b) }}" style="display:inline">
                            @csrf
                            <button class="btn btn-outline btn-sm">{{ $b->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>
                        <form method="POST" action="{{ route('blacklist.destroy', $b) }}" style="display:inline" data-confirm="Hapus {{ $b->full_name }} dari daftar?">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:36px">
                    🛡️ Daftar masih kosong. Tambahkan entri pertama lewat form di atas.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $blacklists->links() }}
</div>
@endsection

@push('scripts')
<script>
    // Toggle tampil/sembunyi form (default terbuka jika ada error validasi)
    (function () {
        var card = document.getElementById('blacklistFormCard');
        var btn = document.getElementById('toggleBlacklistForm');
        var hasError = {{ $errors->has('full_name') || $errors->has('reason') ? 'true' : 'false' }};

        function setCollapsed(collapsed) {
            // sembunyikan isi form saja, judul kartu tetap
            var body = card.querySelector('form');
            body.style.display = collapsed ? 'none' : 'block';
            btn.innerHTML = collapsed
                ? '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg> Tambah Data'
                : '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5 12h14"/></svg> Tutup Form';
            localStorage.setItem('vms.blacklistForm', collapsed ? '1' : '0');
        }

        btn.addEventListener('click', function () {
            var body = card.querySelector('form');
            setCollapsed(body.style.display !== 'none');
        });

        if (!hasError && localStorage.getItem('vms.blacklistForm') === '1') setCollapsed(true);
    })();
</script>
@endpush
