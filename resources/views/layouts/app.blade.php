<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/icons/icon-96.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/icons/icon-192.png') }}">
    <title>@yield('title', 'Dashboard') — {{ \App\Support\Vms::appName() }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css?v=5') }}">
    @stack('styles')
</head>
<body>
<div class="app" id="app">

    {{-- ============ SIDEBAR (bisa hide/unhide) ============ --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="{{ \App\Support\Vms::logoUrl() }}" alt="Logo">
            <div style="min-width:0">
                <div class="brand-name">{{ \App\Support\Vms::appName() }}</div>
                <div class="brand-sub">by Trijaya Solution</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">Menu Utama</div>

            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
                <span class="label">Dashboard</span>
            </a>

            @can('admin-only')
            <div class="nav-section">Manajemen</div>
            @endcan

            <a href="{{ route('visitors.index') }}" class="nav-link {{ request()->routeIs('visitors.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span class="label">Data Tamu</span>
                <span class="nav-badge" data-pending-count style="display:none">0</span>
            </a>

            <a href="{{ route('approvals.index') }}" class="nav-link {{ request()->routeIs('approvals.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                <span class="label">Approval</span>
                <span class="nav-badge" data-approval-count style="display:none">0</span>
            </a>

            <a href="{{ route('blacklist.index') }}" class="nav-link {{ request()->routeIs('blacklist.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l6 6M15 12l-6 6"/></svg>
                <span class="label">Blacklist</span>
            </a>

            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg>
                <span class="label">Laporan</span>
            </a>

            @can('admin-only')
            <div class="nav-section">Sistem</div>

            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span class="label">Pengguna</span>
            </a>

            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.01a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <span class="label">Pengaturan</span>
            </a>

            <a href="{{ route('audit.index') }}" class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8M16 17H8M10 9H8"/></svg>
                <span class="label">Audit Log</span>
            </a>
            @endcan
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('landing') }}" class="nav-link" target="_blank" title="Landing Page">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/></svg>
                <span class="label">Landing Page</span>
            </a>
        </div>
    </aside>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    {{-- ============ MAIN ============ --}}
    <div class="main">

        {{-- Topbar --}}
        <header class="topbar">
            <button class="btn-icon" id="sidebarToggle" title="Hide/Unhide Sidebar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 12h18"/><path d="M3 6h18"/><path d="M3 18h18"/></svg>
            </button>

            <div>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            </div>

            <div class="topbar-spacer"></div>

            {{-- NOTIFICATION BELL (pojok kanan atas) --}}
            <div class="notif-wrap">
                <button class="btn-icon" id="notifBtn" title="Notifikasi">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    <span class="notif-badge" id="notifBadge" style="display:none"><span id="notifBadgeCount">0</span></span>
                </button>
                <div class="notif-panel" id="notifPanel">
                    <div class="notif-head">
                        Notifikasi
                        <form method="POST" action="{{ route('notifications.markRead') }}">
                            @csrf
                            <button class="btn btn-outline btn-sm">Tandai dibaca</button>
                        </form>
                    </div>
                    <div class="notif-list" id="notifList"></div>
                    <div class="notif-empty" id="notifEmpty">Belum ada notifikasi 🔔</div>
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline btn-sm" style="margin:10px;display:flex">Lihat semua</a>
                </div>
            </div>

            {{-- PROFILE --}}
            <div class="profile-wrap">
                <button class="profile-btn" id="profileBtn">
                    <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="profile-text" style="text-align:left">
                        <span class="profile-name">{{ auth()->user()->name }}</span><br>
                        <span class="profile-role">{{ auth()->user()->roleLabel() }}</span>
                    </span>
                </button>
                <div class="profile-menu" id="profileMenu">
                    <a href="{{ route('profile.index') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Profil Saya
                    </a>
                    <hr>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="profile-logout">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        <main class="content">
            @if (session('success'))
                <div class="alert alert-success alert-auto">✅ {{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-auto">⚠️ {{ session('error') }}</div>
            @endif

            @yield('content')
        </main>

        {{-- ============ FOOTER (tahun berjalan + Trijaya Solution) ============ --}}
        <footer class="app-footer">
            &copy; {{ date('Y') }} {{ \App\Support\Vms::appName() }} — All rights reserved.
            Developed by <a href="https://trijayasolution.com" target="_blank" rel="noopener">Trijaya Solution</a>
        </footer>
    </div>

    {{-- ============ BOTTOM NAV (mobile — tab bar 5 icon ala aplikasi native) ============ --}}
    <nav class="bottom-nav" aria-label="Navigasi mobile">
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="bn-ico">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
            </span>
            <span class="bn-lbl">Beranda</span>
        </a>

        <a href="{{ route('visitors.index') }}" class="{{ request()->routeIs('visitors.*') ? 'active' : '' }}">
            <span class="bn-ico">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </span>
            <span class="bn-lbl">Tamu</span>
        </a>

        <a href="{{ route('approvals.index') }}" class="{{ request()->routeIs('approvals.*') ? 'active' : '' }}">
            <span class="bn-ico">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                <span class="bn-badge" data-approval-count style="display:none">0</span>
            </span>
            <span class="bn-lbl">Approval</span>
        </a>

        <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="bn-ico">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg>
            </span>
            <span class="bn-lbl">Laporan</span>
        </a>

        <a href="{{ route('profile.index') }}" class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span class="bn-ico">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <span class="bn-lbl">Profil</span>
        </a>
    </nav>
</div>

<script src="{{ asset('assets/js/pass-eye.js?v=2') }}"></script>
<script src="{{ asset('assets/js/app.js?v=2') }}"></script>
@stack('scripts')
</body>
</html>
