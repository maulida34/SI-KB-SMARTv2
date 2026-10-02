<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SI-KB SMART') · DPPKB</title>
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
    @stack('head')
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a href="{{ route('home') }}" class="brand" aria-label="SI-KB SMART, beranda">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 36 36" fill="none">
                        <path d="M18 4.5 29.5 11v14L18 31.5 6.5 25V11L18 4.5Z" stroke="currentColor" stroke-width="1.7"/>
                        <path d="M18 11v14M11.5 14.7l13 6.6M24.5 14.7l-13 6.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        <circle cx="18" cy="18" r="2.6" fill="currentColor"/>
                    </svg>
                </span>
                <span class="brand-copy">
                    <strong>SI-KB <span>SMART</span></strong>
                    <small>DPPKB · BANJARMASIN</small>
                </span>
            </a>

            <nav class="main-nav" aria-label="Navigasi utama">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home', 'public.village') ? 'is-active' : '' }}">Beranda</a>
                @auth
                    <a href="{{ route('upload.index') }}" class="{{ request()->routeIs('upload.*') ? 'is-active' : '' }}">Data petugas</a>
                @else
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'is-active' : '' }}">Login petugas</a>
                @endauth
            </nav>

            <div class="header-actions">
                <span class="header-divider" aria-hidden="true"></span>
                <span class="header-caption">Analitik Kampung KB</span>
                <button class="theme-toggle" type="button" data-theme-toggle aria-label="Aktifkan mode gelap">
                    <svg class="theme-icon-sun" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.7"/>
                        <path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>
                    <svg class="theme-icon-moon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M20.3 15.4A8.5 8.5 0 0 1 8.6 3.7 8.6 8.6 0 1 0 20.3 15.4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <main class="page-main">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <span>SI-KB SMART <span class="footer-dot">·</span> Sistem Informasi Analitik Kampung KB</span>
            <span>Dinas Pengendalian Penduduk dan Keluarga Berencana</span>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>