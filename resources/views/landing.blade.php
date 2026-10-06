<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Visitor Management System enterprise — registrasi tamu digital, approval workflow, QR badge, check-in/out, blacklist, notifikasi & laporan keamanan.">
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/img/icons/icon-96.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/icons/icon-192.png') }}">
    <title>{{ \App\Support\Vms::appName() }} — Visitor Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/landing.css?v=3') }}">
</head>
<body class="lp-body">

<!-- ================= NAVBAR ================= -->
<nav class="lp-nav" id="lpNav">
    <div class="lp-container lp-nav-inner">
        <a href="#home" class="lp-brand">
            <img src="{{ \App\Support\Vms::logoUrl() }}" alt="Logo">
            {{ \App\Support\Vms::appName() }}
        </a>

        <div class="lp-nav-links" id="lpNavLinks">
            <a href="#fitur">Fitur</a>
            <a href="#alur">Alur Kerja</a>
            <a href="#role">Role</a>
            <a href="#pwa">PWA</a>
            <a href="{{ route('login') }}" class="btn-login">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
                Login
            </a>
        </div>

        <button class="lp-burger" id="lpBurger" aria-label="Menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        </button>
    </div>
</nav>

<!-- ================= HERO ================= -->
<header class="lp-hero" id="home">
    <div class="lp-container lp-hero-grid">
        <div>
            <span class="lp-chip"><span class="dot"></span> Enterprise Visitor Management</span>
            <h1>Kelola Tamu<br>Secara <span class="grad-text">Digital & Aman</span></h1>
            <p class="lead">
                Sistem manajemen tamu terpadu: pre-registration, walk-in kiosk, approval berjenjang,
                QR badge, check-in/out otomatis, blacklist, hingga laporan keamanan — semuanya dalam satu platform.
            </p>
            <div class="lp-hero-actions">
                <a href="{{ route('login') }}" class="lp-btn lp-btn-primary">
                    Mulai Sekarang
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                </a>
                <a href="#alur" class="lp-btn lp-btn-ghost">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    Lihat Alur Kerja
                </a>
            </div>

            <div class="lp-hero-stats">
                <div class="lp-stat"><div class="num grad-text"><span data-count="100">0</span>%</div><div class="lbl">Digital & Paperless</div></div>
                <div class="lp-stat"><div class="num grad-text"><span data-count="8">0</span>+</div><div class="lbl">Role Pengguna</div></div>
                <div class="lp-stat"><div class="num grad-text"><span data-count="24">0</span>/7</div><div class="lbl">Monitoring</div></div>
            </div>
        </div>

        <!-- Mockup dashboard -->
        <div class="hero-mockup">
            <div class="mockup-card">
                <div class="mockup-topbar">
                    <span class="dot" style="background:#f87171"></span>
                    <span class="dot" style="background:#fbbf24"></span>
                    <span class="dot" style="background:#34d399"></span>
                    <span class="url">vms.trijayasolution.com/dashboard</span>
                </div>
                <div class="mockup-body">
                    <div class="mockup-side">
                        <span class="sdot on"></span><span class="sdot"></span><span class="sdot"></span>
                        <span class="sdot"></span><span class="sdot"></span>
                    </div>
                    <div class="mockup-main">
                        <div class="mockup-stats">
                            <div class="ms-card ms-1"><div class="ms-num">128</div><div class="ms-lbl">Total Tamu</div></div>
                            <div class="ms-card ms-2"><div class="ms-num">24</div><div class="ms-lbl">Di Dalam</div></div>
                            <div class="ms-card ms-3"><div class="ms-num">6</div><div class="ms-lbl">Approval</div></div>
                        </div>
                        <div class="mock-chart">
                            <div class="bar" style="height:42%;animation-delay:.15s"></div>
                            <div class="bar" style="height:66%;animation-delay:.3s"></div>
                            <div class="bar" style="height:38%;animation-delay:.45s"></div>
                            <div class="bar" style="height:80%;animation-delay:.6s"></div>
                            <div class="bar" style="height:55%;animation-delay:.75s"></div>
                            <div class="bar" style="height:92%;animation-delay:.9s"></div>
                        </div>
                        <div class="mock-rows">
                            <div class="mock-row"><span class="mav"></span><span class="mline m1"></span><span class="mline m2"></span></div>
                            <div class="mock-row"><span class="mav"></span><span class="mline m1"></span><span class="mline m2"></span></div>
                            <div class="mock-row"><span class="mav"></span><span class="mline m1"></span><span class="mline m2"></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="float-badge fb-1">
                <span class="fb-ico" style="background:#dcfce7;color:#16a34a"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg></span>
                Tamu Check-In
            </div>
            <div class="float-badge fb-2">
                <span class="fb-ico" style="background:#eef4ff;color:#2563eb"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM21 14v.01M14 21v.01M17.5 17.5L21 21"/></svg></span>
                QR Badge
            </div>
            <div class="float-badge fb-3">
                <span class="fb-ico" style="background:#fef3c7;color:#d97706"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg></span>
                Blacklist Terdeteksi
            </div>
        </div>
    </div>

    <div class="scroll-hint"><span class="mouse"></span>Scroll</div>
</header>

<!-- ================= MARQUEE ================= -->
<section style="padding:34px 0;background:#fff">
    <div class="marquee">
        <div class="marquee-track">
            @foreach(array_merge($items = ['Pre-Registration','Walk-In Kiosk','QR Badge','Approval Workflow','Blacklist Guard','Emergency Mode','Audit Trail','Vendor Management','Delivery Module','WhatsApp Notif'], $items) as $i => $item)
            <span class="marquee-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg>
                {{ $item }}
            </span>
            @endforeach
        </div>
    </div>
</section>

<!-- ================= ALUR KERJA ================= -->
<section class="lp-section alt" id="alur">
    <div class="lp-container">
        <div class="lp-head reveal">
            <span class="lp-kicker">Alur Kerja</span>
            <h2>Dari Registrasi Sampai Check-Out</h2>
            <p>Alur end-to-end yang menggantikan buku tamu manual — lebih cepat, aman, dan teraudit.</p>
        </div>

        <div class="flow-grid">
            <div class="flow-card reveal" style="--fc:#2563eb;--fc-soft:#eff6ff">
                <span class="flow-num">01</span>
                <div class="flow-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 15l6-6M15 15l-6-6"/></svg></div>
                <h3>Registrasi</h3>
                <p>Host membuat undangan (pre-registration) atau tamu datang langsung via walk-in kiosk dengan input data & identitas.</p>
            </div>

            <div class="flow-card reveal d1" style="--fc:#7c3aed;--fc-soft:#f5f3ff">
                <span class="flow-num">02</span>
                <div class="flow-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg></div>
                <h3>Verifikasi</h3>
                <p>Security memverifikasi identitas, cek blacklist/watchlist otomatis sebelum tamu diizinkan masuk.</p>
            </div>

            <div class="flow-card reveal d2" style="--fc:#0891b2;--fc-soft:#ecfeff">
                <span class="flow-num">03</span>
                <div class="flow-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
                <h3>Approval</h3>
                <p>Host/PIC menyetujui atau menolak kunjungan. Level approval bisa berjenjang untuk area sensitif.</p>
            </div>

            <div class="flow-card reveal d3" style="--fc:#16a34a;--fc-soft:#f0fdf4">
                <span class="flow-num">04</span>
                <div class="flow-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM21 14v.01M14 21v.01M17.5 17.5L21 21"/></svg></div>
                <h3>Check-In & Badge</h3>
                <p>Tamu check-in via QR code, mendapat visitor badge, dan host menerima notifikasi kedatangan real-time.</p>
            </div>
        </div>
    </div>
</section>

<!-- ================= FITUR ================= -->
<section class="lp-section" id="fitur">
    <div class="lp-container">
        <div class="lp-head reveal">
            <span class="lp-kicker">Fitur Unggulan</span>
            <h2>Semua yang Dibutuhkan Perusahaan</h2>
            <p>Bukan sekadar buku tamu digital — sistem keamanan dan analitik lengkap untuk perusahaan modern.</p>
        </div>

        <div class="feat-grid">
            <div class="feat-card reveal" style="--fi1:#2563eb;--fi2:#3b82f6">
                <div class="feat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                <div><h3>Registrasi Tamu Lengkap</h3><p>Pre-registered, walk-in, vendor/contractor, hingga modul delivery kurir dengan data identitas terstruktur.</p></div>
            </div>

            <div class="feat-card reveal d1" style="--fi1:#7c3aed;--fi2:#a78bfa">
                <div class="feat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM21 14v.01M14 21v.01M17.5 17.5L21 21"/></svg></div>
                <div><h3>QR Invitation & Badge</h3><p>Setiap registrasi menghasilkan QR code unik untuk invitation dan visitor badge yang siap dicetak.</p></div>
            </div>

            <div class="feat-card reveal d2" style="--fi1:#0891b2;--fi2:#22d3ee">
                <div class="feat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
                <div><h3>Approval Berjenjang</h3><p>Level 1 host, level 2 manager, level 3 security untuk area sensitif seperti server room & finance.</p></div>
            </div>

            <div class="feat-card reveal d3" style="--fi1:#d97706;--fi2:#fbbf24">
                <div class="feat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></div>
                <div><h3>Notifikasi Real-Time</h3><p>Bell notifikasi in-app plus integrasi email/WhatsApp: invitation, kedatangan tamu, approval, dan reminder.</p></div>
            </div>

            <div class="feat-card reveal d4" style="--fi1:#dc2626;--fi2:#f87171">
                <div class="feat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l6 6M15 12l-6 6"/></svg></div>
                <div><h3>Blacklist & Watchlist</h3><p>Registrasi otomatis diblokir jika nama/NIK cocok dengan daftar hitam. Riwayat percobaan tercatat.</p></div>
            </div>

            <div class="feat-card reveal d5" style="--fi1:#16a34a;--fi2:#4ade80">
                <div class="feat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg></div>
                <div><h3>Dashboard & Laporan</h3><p>Statistik animasi, grafik kunjungan, analitik per department/company, export audit trail, dan masih banyak lagi.</p></div>
            </div>
        </div>
    </div>
</section>

<!-- ================= ROLE ================= -->
<section class="lp-section alt" id="role">
    <div class="lp-container">
        <div class="lp-head reveal">
            <span class="lp-kicker">Role Pengguna</span>
            <h2>Satu Sistem, Banyak Peran</h2>
            <p>Setiap role punya hak akses dan tampilan yang sesuai dengan tanggung jawabnya.</p>
        </div>

        <div class="roles-strip reveal">
            <span class="role-pill"><span class="rp-dot" style="background:#dc2626"></span>Super Admin</span>
            <span class="role-pill"><span class="rp-dot" style="background:#2563eb"></span>Admin</span>
            <span class="role-pill"><span class="rp-dot" style="background:#0891b2"></span>Receptionist</span>
            <span class="role-pill"><span class="rp-dot" style="background:#7c3aed"></span>Security</span>
            <span class="role-pill"><span class="rp-dot" style="background:#d97706"></span>Host / PIC</span>
            <span class="role-pill"><span class="rp-dot" style="background:#16a34a"></span>Manager</span>
            <span class="role-pill"><span class="rp-dot" style="background:#64748b"></span>HR & Auditor</span>
            <span class="role-pill"><span class="rp-dot" style="background:#ca8a04"></span>Viewer</span>
        </div>
    </div>
</section>

<!-- ================= PWA ================= -->
<section class="lp-section" id="pwa">
    <div class="lp-container">
        <div class="pwa-card reveal">
            <div>
                <span class="lp-kicker">Progressive Web App</span>
                <h2 style="font-size:clamp(24px,3vw,34px);font-weight:800;letter-spacing:-.02em;margin:0 0 12px">Bisa Dipasang Seperti Aplikasi Native</h2>
                <p style="color:var(--lp-muted);line-height:1.7;margin:0 0 20px">
                    VMS dibangun dengan teknologi PWA — install di desktop maupun HP tanpa app store,
                    tampil fullscreen, dan tetap responsif di semua ukuran layar.
                </p>

                <div class="pwa-points">
                    <div class="pwa-point">
                        <span class="pp-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg></span>
                        <div><b>Install Sekali, Akses Kapan Saja</b><span>Tampil dari homescreen/dock seperti aplikasi biasa.</span></div>
                    </div>
                    <div class="pwa-point">
                        <span class="pp-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></span>
                        <div><b>Cepat & Ringan</b><span>Service worker caching membuat halaman termuat seketika.</span></div>
                    </div>
                    <div class="pwa-point">
                        <span class="pp-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 17v4"/></svg></span>
                        <div><b>Fully Responsive</b><span>Optimal di kiosk, tablet, HP, hingga monitor lebar.</span></div>
                    </div>
                </div>

                <button class="pwa-install pwa-install-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
                    Pasang Aplikasi
                </button>
            </div>

            <div class="pwa-phone reveal-right">
                <div class="phone">
                    <div class="phone-screen">
                        <div class="phone-notch"></div>
                        <div class="phone-body">
                            <div class="phone-head">
                                <span class="ph-title">VMS</span>
                                <span class="phone-bell"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg></span>
                            </div>
                            <div class="phone-stats">
                                <div class="ps-card" style="background:#eff6ff"><div class="n">36</div><div class="l">Tamu Hari Ini</div></div>
                                <div class="ps-card" style="background:#f0fdf4"><div class="n">11</div><div class="l">Di Dalam</div></div>
                            </div>
                            <div class="phone-bars">
                                <div class="b" style="height:40%"></div>
                                <div class="b" style="height:70%"></div>
                                <div class="b" style="height:52%"></div>
                                <div class="b" style="height:88%"></div>
                                <div class="b" style="height:60%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= CTA ================= -->
<section class="lp-section">
    <div class="lp-container">
        <div class="lp-cta reveal">
            <h2>Siap Mengubah Cara Perusahaan<br>Mengelola Tamu?</h2>
            <p>Login sekarang dan rasakan pengalaman manajemen tamu yang modern, aman, dan menyenangkan.</p>
            <a href="{{ route('login') }}" class="lp-btn cta-glow">
                Login ke Dashboard
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
            </a>
        </div>
    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer class="lp-footer">
    <div class="lp-container">
        <div class="lp-footer-grid">
            <div>
                <div class="f-brand">
                    <img src="{{ \App\Support\Vms::logoUrl() }}" alt="Logo">
                    {{ \App\Support\Vms::appName() }}
                </div>
                <p>Visitor Management System enterprise untuk keamanan, kenyamanan, dan produktivitas perusahaan Anda. Dikembangkan dengan teknologi web modern termasuk PWA.</p>
            </div>
            <div>
                <h4>Navigasi</h4>
                <ul>
                    <li><a href="#fitur">Fitur</a></li>
                    <li><a href="#alur">Alur Kerja</a></li>
                    <li><a href="#pwa">PWA</a></li>
                    <li><a href="{{ route('login') }}">Login</a></li>
                </ul>
            </div>
            <div>
                <h4>Keamanan</h4>
                <ul>
                    <li><a href="#fitur">Blacklist Guard</a></li>
                    <li><a href="#fitur">Audit Trail</a></li>
                    <li><a href="#fitur">Approval Berjenjang</a></li>
                </ul>
            </div>
        </div>
        <div class="lp-footer-bottom">
            <span>&copy; {{ date('Y') }} {{ \App\Support\Vms::appName() }}. All rights reserved.</span>
            <span>Developed by <a href="https://trijayasolution.com" target="_blank" rel="noopener">Trijaya Solution</a></span>
        </div>
    </div>
</footer>

<!-- PWA install toast -->
<div class="install-toast" id="installToast">
    <img src="{{ asset('assets/img/icons/icon-192.png') }}" alt="VMS">
    <div style="min-width:0">
        <b>Pasang VMS?</b>
        <span>Akses lebih cepat dari homescreen</span>
    </div>
    <div class="it-actions">
        <button class="it-btn it-yes" id="installYes">Pasang</button>
        <button class="it-btn it-no" id="installNo">Nanti</button>
    </div>
</div>

<button class="to-top" id="toTop" aria-label="Kembali ke atas">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
</button>

<script src="{{ asset('assets/js/landing.js?v=3') }}"></script>
</body>
</html>
