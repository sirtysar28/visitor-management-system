@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Profil Saya</h1>
        <p class="page-sub">Informasi akun & pengaturan keamanan.</p>
    </div>
</div>

<div class="grid grid-2">
    <div class="card anim-rise">
        <div class="card-title">Informasi Akun</div>
        <div class="detail-grid">
            <div class="detail-item"><div class="d-label">Nama</div><div class="d-value">{{ $user->name }}</div></div>
            <div class="detail-item"><div class="d-label">Email</div><div class="d-value">{{ $user->email }}</div></div>
            <div class="detail-item"><div class="d-label">Role</div><div class="d-value"><span class="badge badge-dot">{{ $user->roleLabel() }}</span></div></div>
            <div class="detail-item"><div class="d-label">Department</div><div class="d-value">{{ $user->department?->name ?: '-' }}</div></div>
            <div class="detail-item"><div class="d-label">Jabatan</div><div class="d-value">{{ $user->position ?: '-' }}</div></div>
            <div class="detail-item"><div class="d-label">Nomor HP</div><div class="d-value">{{ $user->phone ?: '-' }}</div></div>
        </div>
        <div class="form-hint" style="margin-top:14px">
            Perubahan data profil dapat dilakukan oleh administrator melalui menu Pengguna.
        </div>
    </div>

    <div class="card anim-rise d1">
        <div class="card-title">Ganti Password</div>

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Password Saat Ini <span class="req">*</span></label>
                <div class="pass-wrap">
                    <input type="password" id="curPass" name="current_password" class="form-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}" required autocomplete="current-password">
                    <button type="button" class="pass-eye" data-target="curPass" tabindex="-1" title="Lihat password">
                        <svg class="eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                    </button>
                </div>
                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password Baru <span class="req">*</span></label>
                <div class="pass-wrap">
                    <input type="password" id="newPass" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" required autocomplete="new-password">
                    <button type="button" class="pass-eye" data-target="newPass" tabindex="-1" title="Lihat password">
                        <svg class="eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                    </button>
                </div>
                <div class="form-hint">Min. 6 karakter, kombinasi huruf besar, kecil & angka.</div>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Konfirmasi Password Baru <span class="req">*</span></label>
                <div class="pass-wrap">
                    <input type="password" id="confirmPass" name="password_confirmation" class="form-control" required autocomplete="new-password">
                    <button type="button" class="pass-eye" data-target="confirmPass" tabindex="-1" title="Lihat password">
                        <svg class="eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                    </button>
                </div>
            </div>

            <button class="btn btn-primary">Simpan Password Baru</button>
        </form>
    </div>
</div>
@endsection
