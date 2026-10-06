<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline — {{ \App\Support\Vms::appName() }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: 'Inter', system-ui, sans-serif; background: linear-gradient(150deg, #101d42, #251a66);
            color: #e2e8f0; text-align: center; padding: 24px;
        }
        .box { max-width: 420px; animation: pop .6s both; }
        @keyframes pop { from { opacity: 0; transform: translateY(24px) scale(.96); } to { opacity: 1; transform: none; } }
        .wifi { width: 90px; height: 90px; margin: 0 auto 24px; border-radius: 26px;
            background: rgba(255,255,255,.08); display: flex; align-items: center; justify-content: center; }
        .wifi svg { width: 44px; height: 44px; color: #7fb2ff; }
        h1 { font-size: 26px; margin: 0 0 10px; color: #fff; }
        p { color: #93a7c8; line-height: 1.7; margin: 0 0 28px; font-size: 14.5px; }
        a {
            display: inline-flex; align-items: center; gap: 8px; padding: 12px 26px; border-radius: 13px;
            background: linear-gradient(135deg, #2563eb, #7c3aed); color: #fff; font-weight: 700;
            text-decoration: none; transition: transform .2s;
        }
        a:hover { transform: translateY(-2px); }
        .foot { margin-top: 40px; font-size: 12.5px; color: #64748b; }
        .foot a { background: none; padding: 0; color: #7fb2ff; }
    </style>
</head>
<body>
    <div class="box">
        <div class="wifi">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 15.11a6 6 0 0 1 6.95 0"/><path d="M12 20h.01"/></svg>
        </div>
        <h1>Anda Sedang Offline</h1>
        <p>Tidak ada koneksi internet. Halaman yang pernah dikunjungi tetap dapat diakses dari cache. Periksa koneksi Anda lalu coba lagi.</p>
        <a href="/">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/></svg>
            Coba Lagi
        </a>
        <div class="foot">&copy; {{ date('Y') }} — Developed by <a href="https://trijayasolution.com" target="_blank">Trijaya Solution</a></div>
    </div>
</body>
</html>
