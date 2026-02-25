<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','Sistem Gudang Labkesda')</title>

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet">

    {{-- Font Awesome (untuk icon fas/fa-* seperti di menu Stok Barang) --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkFhA0Q5pSgH5QdQfQ2R3pD7G8Wq9j0P5h5c5uNQ7wX0P5h5uP5g50w=="
          crossorigin="anonymous"
          referrerpolicy="no-referrer" />

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- GLOBAL STYLE: layout Spica-style + util btn-aksi --}}
    <style>
        :root {
            --primary: #4f46e5;
            --primary-soft: rgba(79, 70, 229, 0.08);
            --accent: #06b6d4;
            --danger-soft: rgba(239, 68, 68, 0.08);
            --warning-soft: rgba(245, 158, 11, 0.08);
            --success-soft: rgba(34, 197, 94, 0.08);
            --text-muted: #6b7280;
            --card-radius: 18px;

            --sidebar-width: 260px;
            --sidebar-width-mini: 80px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Poppins", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        .app-layout {
            min-height: 100vh;
            display: flex;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #111827 0%, #020617 100%);
            color: #e5e7eb;
            display: flex;
            flex-direction: column;
            position: fixed;
            inset-block: 0;
            inset-inline-start: 0;
            z-index: 1050;
            transition: width 0.25s ease, transform 0.25s ease, background 0.25s ease, color 0.25s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px 20px;
            border-bottom: 1px solid rgba(31, 41, 55, 0.8);
        }

        .sidebar-logo {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            background: radial-gradient(circle at 10% 20%, #22c55e 0%, #0ea5e9 40%, #6366f1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand-text span:first-child {
            font-size: 14px;
            font-weight: 600;
        }

        .sidebar-brand-text span:last-child {
            font-size: 11px;
            color: #9ca3af;
        }

        .sidebar-nav {
            padding: 14px 10px 10px;
            overflow-y: auto;
        }

        .sidebar-section-title {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
            margin: 14px 12px 6px;
        }

        .nav-item {
            margin-inline: 4px;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 9px 12px;
            border-radius: 999px;
            color: #d1d5db;
            font-size: 13px;
            text-decoration: none;
            transition: background 0.18s ease, color 0.18s ease, transform 0.1s ease;
        }

        .nav-link-custom .nav-icon {
            font-size: 18px;
            width: 22px;
            text-align: center;
        }

        .nav-link-custom .nav-text {
            white-space: nowrap;
        }

        .nav-link-custom .badge {
            margin-left: auto;
            font-size: 10px;
        }

        .nav-link-custom:hover {
            background: rgba(55, 65, 81, 0.9);
            color: #f9fafb;
            transform: translateY(-1px);
        }

        .nav-link-active {
            background: linear-gradient(90deg, #4f46e5 0%, #06b6d4 100%);
            color: #f9fafb;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.55);
        }

        .sidebar-footer {
            padding: 14px 16px 18px;
            border-top: 1px solid rgba(31, 41, 55, 0.8);
            font-size: 11px;
            color: #6b7280;
        }

        .sidebar-footer span {
            display: block;
        }

        /* Tombol close sidebar (mobile) */
        .btn-sidebar-close{
            position: absolute;
            top: 14px;
            right: 14px;
            width: 32px;
            height: 32px;
            border-radius: 999px;
            border: none;
            background: rgba(15, 23, 42, 0.85);
            color: #e5e7eb;
            display: none;
            cursor: pointer;
            z-index: 2000;
        }

        .btn-sidebar-close:hover{
            background: rgba(15, 23, 42, 1);
        }

        body.sidebar-skin-light .btn-sidebar-close{
            background: #111827;
            color: #f9fafb;
        }

        /* Sidebar LIGHT skin */
        body.sidebar-skin-light .sidebar {
            background: #ffffff;
            color: #111827;
            box-shadow: 4px 0 16px rgba(15, 23, 42, 0.08);
        }

        body.sidebar-skin-light .sidebar-brand {
            border-bottom-color: #e5e7eb;
        }

        body.sidebar-skin-light .sidebar-section-title {
            color: #9ca3af;
        }

        body.sidebar-skin-light .nav-link-custom {
            color: #374151;
        }

        body.sidebar-skin-light .nav-link-custom:hover {
            background: #eef2ff;
            color: #111827;
        }

        body.sidebar-skin-light .nav-link-active {
            background: linear-gradient(90deg, #4f46e5 0%, #06b6d4 100%);
            color: #f9fafb;
        }

        body.sidebar-skin-light .sidebar-footer {
            border-top-color: #e5e7eb;
            color: #9ca3af;
        }

        /* Sidebar MINI */
        body.sidebar-mini .sidebar {
            width: var(--sidebar-width-mini);
        }

        body.sidebar-mini .sidebar-brand-text,
        body.sidebar-mini .nav-text,
        body.sidebar-mini .sidebar-section-title,
        body.sidebar-mini .sidebar-footer span {
            display: none;
        }

        body.sidebar-mini .nav-link-custom {
            justify-content: center;
            border-radius: 16px;
        }

        /* ================= MAIN ================= */
        .main {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.25s ease;
        }

        body.sidebar-mini .main {
            margin-left: var(--sidebar-width-mini);
        }

        /* ===== HEADER dgn background gambar ===== */
        .app-header {
            position: relative;
            z-index: 100;
            background-image: url('{{ asset("img/uptd.jpg") }}'); /* ganti gambar di folder public/img */
            background-size: cover;
            background-position: center;
            color: #f9fafb;
        }

        .app-header::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, rgba(15, 23, 42, 0.82), rgba(15, 23, 42, 0.55), rgba(15, 23, 42, 0.9));
            z-index: 0;
        }

        .app-header-inner {
            position: relative;
            z-index: 1;
            padding: 12px 20px 18px;
        }

        .header-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .hero-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .hero-logo {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            background: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
        }

        .hero-title-wrap {
            display: flex;
            flex-direction: column;
        }

        .hero-title {
            font-size: 18px;
            font-weight: 700;
        }

        .hero-subtitle {
            font-size: 12px;
            opacity: 0.9;
        }

        .hero-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .hero-date {
            font-size: 14px;
            font-weight: 600;
        }

        .hero-icons {
            display: flex;
            gap: 10px;
        }

        .hero-circle {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            position: relative;
        }

        .hero-circle-badge {
            position: absolute;
            top: -4px;
            right: -2px;
            font-size: 11px;
            padding: 1px 4px;
            border-radius: 999px;
            background: #f97316;
        }

        .header-search-card {
            position: relative;
            z-index: 1;
            margin-top: 16px;
        }

        .header-search-inner {
            background: #ffffff;
            border-radius: 16px;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .header-search-box {
            flex: 1;
            position: relative;
            max-width: 520px;
        }

        .header-search-box input {
            width: 100%;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            padding: 9px 32px;
            font-size: 13px;
        }

        .header-search-box i {
            position: absolute;
            left: 10px;
            top: 9px;
            font-size: 16px;
            color: var(--text-muted);
        }

        .header-user-mini {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-user-mini-avatar {
            width: 32px;
            height: 32px;
            border-radius: 999px;
            overflow: hidden;
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .header-user-mini-meta {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .header-user-mini-meta span:first-child {
            font-size: 13px;
            font-weight: 600;
        }

        .header-user-mini-meta span:last-child {
            font-size: 11px;
            color: #6b7280;
        }

        .icon-button {
            border: none;
            background: #eef2ff;
            width: 36px;
            height: 36px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            transition: background 0.15s ease, transform 0.1s ease;
        }

        .icon-button:hover {
            background: #e0e7ff;
            transform: translateY(-1px);
        }

        .app-main {
            padding: 18px 20px 26px;
        }

        /* ===== Cards / panel / stat ===== */
        .stat-card {
            border-radius: var(--card-radius);
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.07);
            padding: 14px 16px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: "";
            position: absolute;
            inset-inline-end: -30px;
            inset-block-start: -40px;
            width: 90px;
            height: 90px;
            border-radius: 999px;
            opacity: 0.18;
            background: radial-gradient(circle, rgba(79, 70, 229, 1) 0%, transparent 60%);
        }

        .stat-label {
            font-size: 11px;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .stat-value {
            font-size: 22px;
            font-weight: 700;
            margin-top: 4px;
        }

        .stat-meta {
            font-size: 11px;
            color: var(--text-muted);
        }

        .stat-icon-badge {
            position: absolute;
            inset-inline-end: 14px;
            inset-block-start: 14px;
            width: 34px;
            height: 34px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary-soft);
            color: var(--primary);
        }

        .stat-card-yellow .stat-icon-badge {
            background: var(--warning-soft);
            color: #f59e0b;
        }

        .stat-card-green .stat-icon-badge {
            background: var(--success-soft);
            color: #22c55e;
        }

        .stat-card-red .stat-icon-badge {
            background: var(--danger-soft);
            color: #ef4444;
        }

        .reagent-card {
            border-radius: 14px;
            padding: 10px 12px;
            background: #fef2f2;
            color: #b91c1c;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
        }

        .reagent-chip {
            background: #fee2e2;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 11px;
        }

        .panel-card {
            border-radius: var(--card-radius);
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            padding: 16px 18px;
            height: 100%;
        }

        .panel-title {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .panel-subtitle {
            font-size: 11px;
            color: var(--text-muted);
        }

        .mini-chip {
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 999px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }

        .pill-status {
            font-size: 10px;
            border-radius: 999px;
            padding: 3px 8px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .pill-status.in {
            background: #ecfdf3;
            color: #16a34a;
        }

        .pill-status.out {
            background: #fef2f2;
            color: #b91c1c;
        }

        .pill-status.pending {
            background: #fef3c7;
            color: #d97706;
        }

        /* ===== Theme switcher (sidebar skin) ===== */
        .theme-switcher {
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 1200;
        }

        .theme-switcher-toggle {
            width: 44px;
            height: 44px;
            border-radius: 999px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #4f46e5;
            color: #fff;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.3);
            cursor: pointer;
        }

        .theme-panel {
            position: absolute;
            right: 0;
            bottom: 54px;
            width: 220px;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.35);
            padding: 12px 14px;
            font-size: 12px;
            transform: translateY(10px);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
        }

        .theme-switcher.open .theme-panel {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .theme-panel-title {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .theme-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 4px;
            cursor: pointer;
        }

        .theme-swatch {
            width: 24px;
            height: 24px;
            border-radius: 999px;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .theme-swatch-inner {
            width: 16px;
            height: 16px;
            border-radius: 999px;
        }

        .theme-option input {
            accent-color: #4f46e5;
        }

        /* Tombol aksi (dari layout lama) */
        .btn-aksi{
            padding:.35rem .55rem;
            line-height:1;
            border-radius:.35rem;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            font-size:.875rem;
            transition:all .15s ease-in-out;
        }
        .btn-aksi--sq{
            width:36px;height:36px;
            padding:0;
        }
        .aksi-gap > a, .aksi-gap > form{ margin-left:.35rem; }
        .aksi-gap > a:first-child,
        .aksi-gap > form:first-child{ margin-left:0; }
        .btn-aksi:focus, .btn-aksi:focus-visible{
            outline:2px solid rgba(0,0,0,.15);
            outline-offset:2px;
        }

        /* ========= GLOBAL FLASH BANNER (CRUD NOTIF) ========= */
        .app-flash-wrap{
            margin-bottom: 12px;
        }
        .app-flash{
            border-radius: 14px;
            padding: 10px 16px;
            font-size: 13px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(15,23,42,.06);
            border: 1px solid transparent;
        }
        .app-flash-icon{
            font-size: 18px;
            line-height: 1;
            margin-right: 6px;
        }
        .app-flash--success{
            background:#d1fae5;
            color:#065f46;
            border-color:#6ee7b7;
        }
        .app-flash--error{
            background:#fee2e2;
            color:#991b1b;
            border-color:#fecaca;
        }
        .app-flash--warning{
            background:#fef3c7;
            color:#92400e;
            border-color:#fde68a;
        }
        .app-flash-close{
            border:none;
            background:transparent;
            color:inherit;
            padding:0;
            font-size:18px;
            line-height:1;
            cursor:pointer;
            opacity:.8;
        }
        .app-flash-close:hover{
            opacity:1;
        }
        .app-flash-hide{
            opacity:0;
            transform:translateY(-4px);
            transition:opacity .25s ease, transform .25s ease;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            body.sidebar-open .sidebar {
                transform: translateX(0);
            }

            .main {
                margin-left: 0;
            }

            body.sidebar-mini .main {
                margin-left: 0;
            }

            .hero-right {
                flex-direction: column;
                align-items: flex-end;
                gap: 8px;
            }

            .header-search-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-user-mini {
                width: 100%;
                justify-content: space-between;
            }

            /* tampilkan tombol close hanya di mobile */
            .btn-sidebar-close{
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        @media (max-width: 575.98px) {
            .hero-left {
                flex-direction: column;
                align-items: flex-start;
            }

            .hero-right {
                align-items: flex-start;
            }

            .header-search-inner {
                padding: 10px 12px;
            }
        }
    </style>

    @stack('styles')
</head>

{{-- default: sidebar skin dark --}}
<body class="sidebar-skin-dark">
<div class="app-layout">

    {{-- Sidebar --}}
    @include('layouts.partials.sidebar')

    {{-- Main content --}}
    <div class="main">

        {{-- Top header (hero + search + user) --}}
        @include('layouts.partials.topbar')

        {{-- Page content --}}
        <main class="app-main">

            {{-- GLOBAL FLASH BANNER (untuk semua menu & CRUD) --}}
            @if(session('success') || session('error') || ($errors ?? null)->any())
                <div class="app-flash-wrap">
                    @if(session('success'))
                        <div class="app-flash app-flash--success" id="appFlash">
                            <div class="d-flex align-items-start">
                                <span class="app-flash-icon mt-1">
                                    <i class="ri-check-line"></i>
                                </span>
                                <div>{!! session('success') !!}</div>
                            </div>
                            <button type="button" class="app-flash-close" aria-label="Tutup">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    @elseif(session('error'))
                        <div class="app-flash app-flash--error" id="appFlash">
                            <div class="d-flex align-items-start">
                                <span class="app-flash-icon mt-1">
                                    <i class="ri-error-warning-line"></i>
                                </span>
                                <div>{!! session('error') !!}</div>
                            </div>
                            <button type="button" class="app-flash-close" aria-label="Tutup">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    @elseif(($errors ?? null)->any())
                        <div class="app-flash app-flash--warning" id="appFlash">
                            <div class="d-flex align-items-start">
                                <span class="app-flash-icon mt-1">
                                    <i class="ri-alert-line"></i>
                                </span>
                                <div>
                                    <strong>Periksa kembali input Anda:</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach(($errors ?? collect())->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="app-flash-close" aria-label="Tutup">
                                <i class="ri-close-line"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            @yield('content')
        </main>

        {{-- Footer --}}
        @include('layouts.partials.footer')
    </div>
</div>

{{-- Theme switcher (Dark / Light) --}}
<div class="theme-switcher" id="themeSwitcher">
    <button class="theme-switcher-toggle">
        <i class="ri-settings-3-line"></i>
    </button>
    <div class="theme-panel">
        <div class="theme-panel-title">Sidebar Skins</div>
        <div class="theme-option">
            <div class="theme-swatch">
                <div class="theme-swatch-inner" style="background:linear-gradient(180deg,#111827,#020617);"></div>
            </div>
            <div>
                <label>
                    <input type="radio" name="sidebarSkin" value="dark" checked>
                    Dark
                </label>
            </div>
        </div>
        <div class="theme-option">
            <div class="theme-swatch">
                <div class="theme-swatch-inner" style="background:#ffffff;border:1px solid #e5e7eb;"></div>
            </div>
            <div>
                <label>
                    <input type="radio" name="sidebarSkin" value="light">
                    Light
                </label>
            </div>
        </div>
    </div>
</div>

{{-- JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    const bodyEl = document.body;
    const breakpointLg = 992;

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarMiniToggle = document.getElementById('sidebarMiniToggle');
    const sidebarCloseMobile = document.getElementById('sidebarCloseMobile');
    
    function isMobileViewport() {
        return window.innerWidth < breakpointLg;
    }

    function normalizeSidebarState() {
        if (isMobileViewport()) {
            bodyEl.classList.remove('sidebar-mini');
        } else {
            bodyEl.classList.remove('sidebar-open');
        }
    }

    normalizeSidebarState();
    window.addEventListener('resize', normalizeSidebarState);

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            if (isMobileViewport()) {
                bodyEl.classList.toggle('sidebar-open');
            } else {
                bodyEl.classList.toggle('sidebar-mini');
            }
        });
    }

    if (sidebarMiniToggle) {
        sidebarMiniToggle.addEventListener('click', () => {
            if (!isMobileViewport()) {
                bodyEl.classList.toggle('sidebar-mini');
            }
        });
    }

    if (sidebarCloseMobile) {
        sidebarCloseMobile.addEventListener('click', () => {
            bodyEl.classList.remove('sidebar-open');
        });
    }

    const themeSwitcher = document.getElementById('themeSwitcher');
    if (themeSwitcher) {
        const themeToggleBtn = themeSwitcher.querySelector('.theme-switcher-toggle');
        const sidebarSkinRadios = themeSwitcher.querySelectorAll('input[name="sidebarSkin"]');

        themeToggleBtn.addEventListener('click', () => {
            themeSwitcher.classList.toggle('open');
        });

        sidebarSkinRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.value === 'dark') {
                    bodyEl.classList.remove('sidebar-skin-light');
                    bodyEl.classList.add('sidebar-skin-dark');
                } else {
                    bodyEl.classList.remove('sidebar-skin-dark');
                    bodyEl.classList.add('sidebar-skin-light');
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const flash = document.getElementById('appFlash');
        if (!flash) return;

        const closeBtn = flash.querySelector('.app-flash-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                flash.classList.add('app-flash-hide');
                setTimeout(() => flash.remove(), 250);
            });
        }
        setTimeout(() => {
            if (!flash) return;
            flash.classList.add('app-flash-hide');
            setTimeout(() => {
                if (flash && flash.parentNode) {
                    flash.remove();
                }
            }, 250);
        }, 5000);
    });
</script>

@stack('scripts')
</body>
</html>
