@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')
<div class="page-head">
    <div>
        <h1 class="page-title">Pengaturan Sistem</h1>
        <p class="page-sub">Kelola identitas aplikasi (logo & nama) serta konfigurasi SMTP email.</p>
    </div>
</div>

<div class="tabs">
    <button class="tab-btn active" data-group="settings" data-target="tab-general">🏪 Umum & Logo</button>
    <button class="tab-btn" data-group="settings" data-target="tab-smtp">📧 SMTP / Email</button>
</div>

{{-- ============ TAB GENERAL ============ --}}
<div class="tab-pane active" data-group="settings" id="tab-general">
    <div class="grid grid-2">
        <div class="card anim-rise">
            <div class="card-title">Identitas Aplikasi</div>

            <form method="POST" action="{{ route('settings.general') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nama Aplikasi <span class="req">*</span></label>
                    <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name'] ?? config('app.name') }}" required>
                    <div class="form-hint">Ditampilkan di sidebar, title browser, dan landing page.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Perusahaan</label>
                    <input type="text" name="company_name" class="form-control" value="{{ $settings['company_name'] ?? '' }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Logo Aplikasi</label>
                    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:10px">
                        <img src="{{ \App\Support\Vms::logoUrl() }}" alt="Logo saat ini"
                             style="width:64px;height:64px;border-radius:16px;border:1px solid var(--border);object-fit:cover;background:#fff;padding:6px">
                        <div>
                            <input type="file" name="app_logo" class="form-control" accept="image/png,image/jpeg,image/svg+xml,image/webp" style="max-width:280px">
                            <div class="form-hint">PNG / JPG / SVG / WEBP, maks 2MB. Disarankan rasio 1:1.</div>
                        </div>
                    </div>
                    @if (!empty($settings['app_logo']))
                        <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)">
                            <input type="checkbox" name="remove_logo" value="1" style="accent-color:var(--danger)">
                            Hapus logo custom (kembali ke default)
                        </label>
                    @endif
                </div>

                <button class="btn btn-primary">Simpan Pengaturan</button>
            </form>
        </div>

        <div class="card anim-rise d1">
            <div class="card-title">Preview Branding</div>
            <div style="display:flex;flex-direction:column;gap:18px;align-items:flex-start">
                <div style="display:flex;align-items:center;gap:12px;padding:14px 18px;background:linear-gradient(135deg,#0f1e3d,#172554);border-radius:14px;width:100%">
                    <img src="{{ \App\Support\Vms::logoUrl() }}" style="width:40px;height:40px;border-radius:10px" alt="">
                    <div>
                        <div style="color:#fff;font-weight:700">{{ $settings['app_name'] ?? config('app.name') }}</div>
                        <div style="color:#7d93b8;font-size:11px">by Trijaya Solution</div>
                    </div>
                </div>
                <p style="color:var(--muted);font-size:13px;margin:0">
                    Logo otomatis dipakai di: sidebar dashboard, halaman login, landing page, footer PWA,
                    dan icon aplikasi (favicon tetap pakai icon bawaan).
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ============ TAB SMTP ============ --}}
<div class="tab-pane" data-group="settings" id="tab-smtp">
    <div class="grid grid-2">
        <div class="card anim-rise">
            <div class="card-title">Konfigurasi SMTP</div>

            <form method="POST" action="{{ route('settings.smtp') }}">
                @csrf

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">SMTP Host <span class="req">*</span></label>
                        <input type="text" name="smtp_host" class="form-control" placeholder="smtp.gmail.com" value="{{ $settings['smtp_host'] ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Port <span class="req">*</span></label>
                        <input type="number" name="smtp_port" class="form-control" placeholder="587" value="{{ $settings['smtp_port'] ?? '587' }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Enkripsi</label>
                        <select name="smtp_encryption" class="form-select">
                            @foreach (['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'Tanpa Enkripsi'] as $k => $l)
                                <option value="{{ $k }}" {{ ($settings['smtp_encryption'] ?? 'tls') == $k ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" name="smtp_username" class="form-control" value="{{ $settings['smtp_username'] ?? '' }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password SMTP</label>
                    <div class="pass-wrap">
                        <input type="password" id="smtpPass" name="smtp_password" class="form-control" placeholder="••••••••" value="{{ $settings['smtp_password'] ?? '' }}" autocomplete="new-password">
                        <button type="button" class="pass-eye" data-target="smtpPass" tabindex="-1" title="Lihat password">
                            <svg class="eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-group">
                        <label class="form-label">From Address <span class="req">*</span></label>
                        <input type="email" name="smtp_from_address" class="form-control" placeholder="noreply@perusahaan.com" value="{{ $settings['smtp_from_address'] ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">From Name <span class="req">*</span></label>
                        <input type="text" name="smtp_from_name" class="form-control" value="{{ $settings['smtp_from_name'] ?? \App\Support\Vms::appName() }}" required>
                    </div>
                </div>

                <button class="btn btn-primary">Simpan SMTP</button>
            </form>
        </div>

        <div class="card anim-rise d1">
            <div class="card-title">Uji Coba Kirim Email</div>
            <p style="color:var(--muted);font-size:13px;margin:0 0 14px">
                Simpan konfigurasi terlebih dahulu, lalu kirim email uji coba untuk memastikan SMTP berfungsi.
            </p>

            <form method="POST" action="{{ route('settings.smtp.test') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email Tujuan <span class="req">*</span></label>
                    <input type="email" name="test_email" class="form-control" placeholder="email@tujuan.com" required>
                </div>
                <button class="btn btn-outline">📧 Kirim Email Uji</button>
            </form>

            <div class="alert alert-info" style="margin:18px 0 0">
                <b>Tips:</b> Untuk Gmail, gunakan <b>App Password</b> (bukan password biasa) dan aktifkan 2FA.
                Port 587 dengan TLS adalah konfigurasi paling umum.
            </div>
        </div>
    </div>
</div>
@endsection
