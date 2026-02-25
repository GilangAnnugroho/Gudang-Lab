@php
    $user = auth()->user();
    $role = optional($user->role)->role_name ?? '-';
    $unit = optional($user->unit)->unit_name ?? '-';
    $globalSearchValue = request()->routeIs('search.global') ? request('q', '') : '';
@endphp

<header class="app-header">
    <div class="app-header-inner">
        <div class="header-hero">
            <div class="hero-left">
                <button class="icon-button" id="sidebarToggle" title="Toggle Sidebar">
                    <i class="ri-menu-line"></i>
                </button>
                <div class="hero-logo">
                    <i class="ri-flask-line"></i>
                </div>
                <div class="hero-title-wrap">
                    <div class="hero-title">Gudang Labkesda</div>
                    <div class="hero-subtitle">
                        Welcome back, {{ $user->name ?? 'User' }}
                    </div>
                </div>
            </div>

            <div class="hero-right">
                <div class="hero-date">
                    {{-- periode / tanggal bisa kamu ubah bebas --}}
                    {{ now()->timezone('Asia/Jakarta')->format('M d, Y') }}
                </div>
            </div>
        </div>

        {{-- Search + user mini info --}}
        <div class="header-search-card mt-3">
            <div class="header-search-inner">
                <form method="get"
                      action="{{ route('search.global') }}"
                      class="header-search-box d-none d-sm-flex align-items-center">
                    <i class="ri-search-line"></i>
                    <input type="text"
                           name="q"
                           placeholder="Cari item, varian, permintaan, transaksi…"
                           value="{{ $globalSearchValue }}"
                           autocomplete="off">

                    @if(request()->routeIs('search.global') && request()->filled('q'))
                        <button type="button"
                                class="icon-button ms-1"
                                title="Reset pencarian & kembali ke Dashboard"
                                onclick="window.location='{{ route('dashboard') }}'">
                            <i class="ri-refresh-line"></i>
                        </button>
                    @endif
                </form>

                <div class="header-user-mini">
                    <div class="header-user-mini-avatar">
                        <i class="ri-user-line"></i>
                    </div>

                    {{-- 🔹 Paksa warna teks jadi gelap agar jelas di kartu putih --}}
                    <div class="header-user-mini-meta">
                        <span style="color:#111827;font-weight:600;">
                            {{ $user->name ?? 'User' }}
                        </span>
                        <span style="color:#6b7280;font-size:11px;">
                            Role: {{ $role }} · Unit: {{ $unit }}
                        </span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="ms-2">
                        @csrf
                        <button type="submit" class="icon-button" title="Logout">
                            <i class="ri-logout-box-r-line"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
