<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/icons/icon-96.png') }}">
    <title>@yield('title', 'Login') — {{ \App\Support\Vms::appName() }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css?v=2') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css?v=2') }}">
</head>
<body>
<div class="auth-body">

    {{-- ====== KIRI: form login ====== --}}
    <div class="auth-left">
        <div class="auth-left-inner" data-error="{{ $errors->any() ? '1' : '0' }}">
            <a href="{{ route('landing') }}" class="auth-brand">
                <img src="{{ \App\Support\Vms::logoUrl() }}" alt="Logo">
                <span>
                    <span class="b-name">{{ \App\Support\Vms::appName() }}</span><br>
                    <span class="b-sub">Visitor Management System</span>
                </span>
            </a>

            <h1 class="welcome-title">Selamat Datang 👋</h1>
            <p class="welcome-sub">Silakan login dengan akun Anda untuk mengakses sistem.</p>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" autocomplete="off" id="loginForm">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="email">Email <span class="req">*</span></label>
                    <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           placeholder="nama@perusahaan.com" value="{{ old('email') }}" required autofocus>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Password dengan tombol intip --}}
                <div class="form-group">
                    <label class="form-label" for="password">Password <span class="req">*</span></label>
                    <div class="pass-wrap">
                        <input type="password" id="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               placeholder="••••••••" required>
                        <button type="button" class="pass-eye" data-target="password" tabindex="-1" title="Lihat password">
                            <svg class="eye-on" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/></svg>
                        </button>
                    </div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- CAPTCHA: langsung di bawah password --}}
                <div class="form-group">
                    <label class="form-label" for="captchaInput">Kode Keamanan <span class="req">*</span></label>

                    <div class="captcha-row">
                        <div class="captcha-box">
                            <img src="{{ route('captcha') }}?{{ time() }}" alt="Captcha" class="captcha-img" id="captchaImg" title="Klik gambar untuk ganti kode">
                            <button type="button" class="captcha-refresh" id="captchaRefresh" title="Ganti captcha">
                                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.64-6.36"/><path d="M21 3v6h-6"/></svg>
                            </button>
                        </div>
                        <input type="text" id="captchaInput" name="captcha"
                               class="form-control captcha-input {{ $errors->has('captcha') ? 'is-invalid' : '' }}"
                               placeholder="•••••" maxlength="5" autocomplete="off" required>
                    </div>

                    @error('captcha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-hint">Ketik 5 karakter pada gambar — tidak peka huruf besar/kecil.</div>
                </div>

                <div class="remember-row">
                    <label>
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Ingat saya
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;padding:12px">
                    Masuk
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                </button>
            </form>

            <div class="demo-box">
                <strong>Akun demo</strong><br>
                Admin: <code>admin@vms.test</code> / <code>password</code><br>
                Receptionist: <code>receptionist@vms.test</code> / <code>password</code>
            </div>
        </div>
    </div>

    {{-- ====== KANAN: info & branding ====== --}}
    <div class="auth-right">
        <span class="blob blob-1"></span>
        <span class="blob blob-2"></span>
        <span class="blob blob-3"></span>

        <div class="auth-right-inner">
            <img src="{{ \App\Support\Vms::logoUrl() }}" alt="Logo" style="width:64px;height:64px;border-radius:16px">
            <h2>Visitor Management<br>System</h2>
            <p>Kelola tamu, approval, check-in/out, badge QR, dan laporan keamanan dalam satu platform terpadu.</p>

            <ul class="auth-feats">
                <li>
                    <span class="f-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg></span>
                    Pre-Registration & Walk-In visitor
                </li>
                <li>
                    <span class="f-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM21 14v.01M14 21v.01M17.5 17.5L21 21"/></svg></span>
                    QR Code invitation & badge
                </li>
                <li>
                    <span class="f-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></span>
                    Notifikasi real-time
                </li>
                <li>
                    <span class="f-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg></span>
                    Blacklist & audit trail
                </li>
            </ul>

            <div class="security-note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <div>
                    <b>Koneksi Aman</b>
                    <span>Seluruh aktivitas login dipantau & tercatat di audit trail sistem.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/pass-eye.js?v=2') }}"></script>
<script src="{{ asset('assets/js/login.js?v=2') }}"></script>
</body>
</html>
