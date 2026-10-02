<!DOCTYPE html>
<html lang="id" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Login Petugas') · SI-KB SMART</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
        <script>
            (() => {
                try {
                    document.documentElement.dataset.theme = localStorage.getItem('sikb-theme') || 'light';
                } catch (_) {}
            })();
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="login-page">
        <a href="{{ route('home') }}" class="login-back">
            <svg viewBox="0 0 18 18" fill="none" aria-hidden="true"><path d="M14.5 9h-11m0 0 4.3-4.3M3.5 9l4.3 4.3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Kembali ke beranda
        </a>
        <button class="theme-toggle login-theme-toggle" type="button" data-theme-toggle aria-label="Aktifkan mode gelap">
            <svg class="theme-icon-sun" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.7"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
            <svg class="theme-icon-moon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20.3 15.4A8.5 8.5 0 0 1 8.6 3.7 8.6 8.6 0 1 0 20.3 15.4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/></svg>
        </button>
        <div class="login-layout">
            <section class="login-story">
                <a href="{{ route('home') }}" class="brand login-brand">
                    <span class="brand-mark" aria-hidden="true"><svg viewBox="0 0 36 36" fill="none"><path d="M18 4.5 29.5 11v14L18 31.5 6.5 25V11L18 4.5Z" stroke="currentColor" stroke-width="1.7"/><path d="M18 11v14M11.5 14.7l13 6.6M24.5 14.7l-13 6.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="18" cy="18" r="2.6" fill="currentColor"/></svg></span>
                    <span class="brand-copy"><strong>SI-KB <span>SMART</span></strong><small>DPPKB · BANJARMASIN</small></span>
                </a>
                <div class="login-story-copy">
                    <span class="panel-kicker">AKSES PETUGAS DALDUK</span>
                    <h1>Data terarah.<br><span>Dampak terukur.</span></h1>
                    <p>Masuk untuk mengunggah data kegiatan dan melihat riwayat impor Kampung KB.</p>
                </div>
                <div class="login-story-footer">Sistem Informasi Analitik Kampung KB <span>·</span> DPPKB</div>
            </section>
            <section class="login-form-side">
                <div class="login-form-card">
                    <span class="panel-kicker">SELAMAT DATANG KEMBALI</span>
                    <h2>Login petugas</h2>
                    <p class="login-form-intro">Gunakan akun petugas DALDUK untuk melanjutkan.</p>
                    {{ $slot }}
                    <div class="login-private-note"><span class="lock-icon" aria-hidden="true">⌑</span>Akses ini khusus untuk petugas terdaftar.</div>
                </div>
            </section>
        </div>
    </body>
</html>
