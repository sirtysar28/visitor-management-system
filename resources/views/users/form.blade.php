@extends('layouts.app')

@section('title', $user->exists ? 'Edit User' : 'Tambah User')
@section('page-title', $user->exists ? 'Edit User' : 'Tambah User')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">{{ $user->exists ? 'Edit: ' . $user->name : 'Tambah User Baru' }}</h1>
        <p class="page-sub">Lengkapi informasi akun dan tentukan role aksesnya.</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-outline">← Kembali</a>
</div>

<div class="card anim-rise" style="max-width:820px">
    <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
        @csrf
        @if ($user->exists) @method('PUT') @endif

        <div class="grid grid-2">
            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="req">*</span></label>
                <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name', $user->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Email <span class="req">*</span></label>
                <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email', $user->email) }}" required>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password {{ $user->exists ? '' : '<span class="req">*</span>' }}</label>
                <div class="pass-wrap">
                    <input type="password" id="userPass" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           placeholder="{{ $user->exists ? 'Kosongkan jika tidak diubah' : 'Minimal 6 karakter' }}" {{ $user->exists ? '' : 'required' }} autocomplete="new-password">
                    <button type="button" class="pass-eye" data-target="userPass" tabindex="-1" title="Lihat password">
                        <svg class="eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                    </button>
                </div>
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <div class="pass-wrap">
                    <input type="password" id="userPassConfirm" name="password_confirmation" class="form-control" placeholder="Ulangi password" autocomplete="new-password">
                    <button type="button" class="pass-eye" data-target="userPassConfirm" tabindex="-1" title="Lihat password">
                        <svg class="eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Role <span class="req">*</span></label>
                <select name="role" class="form-select" required>
                    @foreach ($roles as $key => $label)
                        <option value="{{ $key }}" {{ old('role', $user->role) == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="form-hint">Super Admin memiliki akses penuh ke seluruh sistem.</div>
            </div>

            <div class="form-group">
                <label class="form-label">Department</label>
                <select name="department_id" class="form-select">
                    <option value="">— Tanpa Department —</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Nomor HP</label>
                <input type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Jabatan</label>
                <input type="text" name="position" class="form-control" value="{{ old('position', $user->position) }}">
            </div>

            <div class="form-group">
                <label class="form-label">Status Akun</label>
                <label style="display:flex;align-items:center;gap:9px;padding:10px 0">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }} style="width:17px;height:17px;accent-color:var(--success)">
                    <span style="font-weight:600">Aktif (boleh login)</span>
                </label>
            </div>
        </div>

        <button class="btn btn-primary">{{ $user->exists ? 'Simpan Perubahan' : 'Tambah User' }}</button>
    </form>
</div>
@endsection
