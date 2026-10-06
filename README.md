# TriVMS — Visitor Management System

Aplikasi **Visitor Management System (VMS)** enterprise berbasis **Laravel 12 + PHP 8.2**, dibangun mengikuti dokumen *Work Flow Aplikasi Visitor Management System*.

Dikembangkan oleh **[Trijaya Solution](https://trijayasolution.com)**.

---

## ✨ Fitur

### Publik
- **Landing page** full responsive, smooth, penuh animasi (hero parallax, reveal on scroll, counter, marquee, mockup dashboard, floating badges).
- **PWA (Progressive Web App)** — `manifest.json`, service worker (`sw.js`), halaman offline, tombol install app, cache-first untuk aset statis.

### Autentikasi
- Halaman login **split-layout** — form di kiri, **panel captcha di sisi kanan**.
- **Captcha jelas & mudah dibaca**: 5 karakter besar tebal, kontras tinggi, tanpa karakter membingungkan (0/O, 1/I, dst), bisa di-refresh, case-insensitive.
- **Toggle intip password (eye)** di **semua** input password: login, ganti password profil, form user (password + konfirmasi), password SMTP.

### Dashboard
- Kartu statistik dengan **animasi counter** (total, hari ini, sedang di dalam, pending approval).
- **Bar chart animasi** (Chart.js) kunjungan 7 hari terakhir.
- **Venn diagram animasi** (SVG custom): segmen Pre-Registered / Walk-In / Vendor.
- Donut chart distribusi status + daftar tamu di dalam gedung + registrasi terbaru.

### Modul Operasional
| Modul | Keterangan |
|---|---|
| **Data Tamu** | CRUD registrasi (pre-registered, walk-in, vendor, delivery), filter & pencarian, check-in / check-out, QR badge (SVG), foto tamu |
| **Approval** | Level 1 (Host/PIC) — setujui/tolak + catatan, riwayat approval |
| **Blacklist/Watchlist** | Guard otomatis saat registrasi (nama/NIK match) |
| **Laporan** | Filter rentang tanggal & status |
| **Notifikasi** | Bell di **pojok kanan atas** (dropdown, polling 30 detik, badge merah, tandai dibaca) |
| **Pengguna** | Manajemen user + 6 role (super_admin, admin, receptionist, security, host, viewer) |
| **Pengaturan** | **Logo custom** (upload, tampil di semua halaman), nama aplikasi, **SMTP lengkap + test kirim email** |
| **Audit Log** | Jejak semua aktivitas + export CSV |

### UI/UX
- Sidebar **hide/unhide** (collapse ke icon-only, state tersimpan di localStorage; drawer + overlay di mobile).
- Footer: `© <tahun berjalan> <NamaApp> — Developed by Trijaya Solution` (link ke trijayasolution.com) di semua halaman.

---

## 🚀 Cara Menjalankan

```bash
# 1) Install dependency
composer install

# 2) Konfigurasi environment
cp .env.example .env
php artisan key:generate

# 3) Database (default: SQLite — tanpa setup apa pun)
touch database/database.sqlite
php artisan migrate --seed

# 4) Storage link (untuk upload logo & foto)
php artisan storage:link

# 5) Jalankan
php artisan serve
# buka http://127.0.0.1:8000
```

### Ganti ke MySQL
Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vms
DB_USERNAME=root
DB_PASSWORD=
```
lalu `php artisan migrate --seed`.

---



---

## 🧪 Testing

```bash
php artisan test
# 22 tests, meliputi: login+captcha, flow registrasi→approval→check-in/out,
# blacklist guard, upload logo + SMTP, role restriction, render halaman, PWA assets
```

---

## 📁 Struktur Penting

```
app/
├── Http/Controllers/       # Dashboard, Visitor, Approval, Blacklist, Setting, dll
├── Http/Middleware/        # CheckRole
├── Models/                 # User, Visitor, Approval, Blacklist, Setting, ...
└── Support/Vms.php         # Helper global (appName, logoUrl, audit)
database/migrations/        # users+role, departments, visitors, approvals, dst
public/
├── manifest.json           # PWA manifest
├── sw.js                   # Service worker
└── assets/                 # CSS/JS custom (tanpa build step), Chart.js, icons
resources/views/
├── landing.blade.php       # Landing page animasi
├── auth/login.blade.php    # Login + captcha kanan
├── dashboard.blade.php     # Statistik + bar + venn
└── ...                     # visitors, approvals, blacklist, settings, users, dst
```

## 🔌 Integrasi Selanjutnya ( roadmap dari dokumen workflow )
WhatsApp gateway, OCR KTP, CCTV, access door/turnstile, meeting room booking, emergency evacuation list, vendor work permit — struktur modul & approval trail sudah disiapkan untuk ekspansi ini.
# visitor-management-system
