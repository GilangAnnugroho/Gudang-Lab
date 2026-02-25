@php
    use App\Models\Request as RequestModel;

    $user = auth()->user();
    $role = strtolower(optional($user->role)->role_name ?? '');
@endphp

<aside class="sidebar">
    {{-- Tombol close khusus mobile --}}
    <button class="btn-sidebar-close" id="sidebarCloseMobile">
        <i class="ri-close-line"></i>
    </button>

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <i class="ri-flask-line"></i>
        </div>
        <div class="sidebar-brand-text">
            <span>Gudang Labkesda</span>
            <span>Warehouse Management</span>
        </div>
    </div>

    {{-- User panel --}}
    <div class="px-3 pt-2 pb-1">
        <div class="d-flex align-items-center gap-2" style="font-size:12px;">
            <div style="width:28px;height:28px;border-radius:999px;background:#111827;display:flex;align-items:center;justify-content:center;">
                <i class="ri-user-line"></i>
            </div>
            <div class="nav-text" style="display:flex;flex-direction:column;line-height:1.2;">
                <div style="font-weight:600;font-size:12px;">
                    {{ $user->name }}
                </div>
                <div style="font-size:11px;color:#9ca3af;">
                    {{ optional($user->role)->role_name ?? '-' }} · {{ optional($user->unit)->unit_name ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        {{-- MAIN MENU --}}
        <div class="sidebar-section-title">Main Menu</div>
        <div class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link-custom {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">
                <i class="nav-icon ri-dashboard-line"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </div>

        {{-- ===================== SUPER ADMIN ===================== --}}
        @if($role === 'super admin')
            <div class="sidebar-section-title">Master Data</div>

            <div class="nav-item">
                <a href="{{ route('items.index') }}"
                   class="nav-link-custom {{ request()->routeIs('items.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-archive-2-line"></i>
                    <span class="nav-text">Item Master</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('variants.index') }}"
                   class="nav-link-custom {{ request()->routeIs('variants.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-price-tag-3-line"></i>
                    <span class="nav-text">Item Variant</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('categories.index') }}"
                   class="nav-link-custom {{ request()->routeIs('categories.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-shape-line"></i>
                    <span class="nav-text">Kategori</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('suppliers.index') }}"
                   class="nav-link-custom {{ request()->routeIs('suppliers.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-truck-line"></i>
                    <span class="nav-text">Rekanan</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('users.index') }}"
                   class="nav-link-custom {{ request()->routeIs('users.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-team-line"></i>
                    <span class="nav-text">User & Role</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('units.index') }}"
                   class="nav-link-custom {{ request()->routeIs('units.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-building-2-line"></i>
                    <span class="nav-text">Unit</span>
                </a>
            </div>

            <div class="sidebar-section-title">Permintaan</div>

            <div class="nav-item">
                <a href="{{ route('requests.index') }}"
                   class="nav-link-custom {{ request()->routeIs('requests.index') && !request('status') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-clipboard-line"></i>
                    <span class="nav-text">Daftar Permintaan</span>
                </a>
            </div>

            <div class="sidebar-section-title">Transaksi & Stok</div>

            <div class="nav-item">
                <a href="{{ route('transactions.index', ['type' => 'MASUK']) }}"
                   class="nav-link-custom {{ request()->routeIs('transactions.index') && request('type') === 'MASUK' ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-arrow-down-line"></i>
                    <span class="nav-text">Barang Masuk</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('transactions.index', ['type' => 'KELUAR']) }}"
                   class="nav-link-custom {{ request()->routeIs('transactions.index') && request('type') === 'KELUAR' ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-arrow-up-line"></i>
                    <span class="nav-text">Barang Keluar</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('stock.index') }}"
                   class="nav-link-custom {{ request()->routeIs('stock.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-database-2-line"></i>
                    <span class="nav-text">Stok Barang</span>
                </a>
            </div>

            {{-- MENU BARU (SUPER ADMIN JUGA BISA LIHAT) --}}
            <div class="nav-item">
                <a href="{{ route('stock-opnames.index') }}"
                   class="nav-link-custom {{ request()->routeIs('stock-opnames.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-survey-line"></i>
                    <span class="nav-text">Riwayat Stok Opname</span>
                </a>
            </div>

            {{-- 🔹 BATCH PER LOT (SUPER ADMIN) --}}
            <div class="nav-item">
                <a href="{{ route('batches.index') }}"
                   class="nav-link-custom {{ request()->routeIs('batches.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-stack-line"></i>
                    <span class="nav-text">Batch per Lot</span>
                </a>
            </div>

            <div class="sidebar-section-title">Laporan</div>

            <div class="nav-item">
                <a href="{{ route('reports.distribution') }}"
                   class="nav-link-custom {{ request()->routeIs('reports.distribution') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-file-chart-line"></i>
                    <span class="nav-text">Laporan Distribusi</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('reports.stock') }}"
                   class="nav-link-custom {{ request()->routeIs('reports.stock') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-file-list-3-line"></i>
                    <span class="nav-text">Laporan Stok Akhir</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('reports.usage_yearly') }}"
                   class="nav-link-custom {{ request()->routeIs('reports.usage_yearly') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-bar-chart-2-line"></i>
                    <span class="nav-text">Rekap Pemakaian Tahunan</span>
                </a>
            </div>
        @endif

        {{-- ===================== KEPALA LAB ===================== --}}
        @if($role === 'kepala lab')
            <div class="sidebar-section-title">Permintaan</div>
            <div class="nav-item">
                <a href="{{ route('requests.index') }}"
                   class="nav-link-custom {{ request()->routeIs('requests.index') && !request('status') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-list-unordered"></i>
                    <span class="nav-text">Daftar Permintaan</span>
                </a>
            </div>

            {{-- MENU BARU UNTUK REVISI (KEPALA LAB BISA INPUT) --}}
            <div class="sidebar-section-title">Stok Opname</div>
            <div class="nav-item">
                <a href="{{ route('stock-opnames.index') }}"
                   class="nav-link-custom {{ request()->routeIs('stock-opnames.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-clipboard-fill"></i>
                    <span class="nav-text">Stok Opname</span>
                </a>
            </div>
            {{-- END MENU BARU --}}

            <div class="sidebar-section-title">Laporan</div>
            <div class="nav-item">
                <a href="{{ route('reports.outgoing') }}"
                   class="nav-link-custom {{ request()->routeIs('reports.outgoing') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-file-download-line"></i>
                    <span class="nav-text">Laporan Barang Keluar</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('reports.stock') }}"
                   class="nav-link-custom {{ request()->routeIs('reports.stock') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-file-list-3-line"></i>
                    <span class="nav-text">Laporan Stok Akhir</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('reports.usage_yearly') }}"
                   class="nav-link-custom {{ request()->routeIs('reports.usage_yearly') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-bar-chart-2-line"></i>
                    <span class="nav-text">Rekap Pemakaian Tahunan</span>
                </a>
            </div>
        @endif

        {{-- ===================== ADMIN GUDANG ===================== --}}
        @if($role === 'admin gudang')
            <div class="sidebar-section-title">Master Data</div>

            <div class="nav-item">
                <a href="{{ route('items.index') }}"
                   class="nav-link-custom {{ request()->routeIs('items.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-archive-2-line"></i>
                    <span class="nav-text">Item Master</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('variants.index') }}"
                   class="nav-link-custom {{ request()->routeIs('variants.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-price-tag-3-line"></i>
                    <span class="nav-text">Item Variant</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('suppliers.index') }}"
                   class="nav-link-custom {{ request()->routeIs('suppliers.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-truck-line"></i>
                    <span class="nav-text">Rekanan </span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('categories.index') }}"
                   class="nav-link-custom {{ request()->routeIs('categories.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-shape-line"></i>
                    <span class="nav-text">Kategori</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('units.index') }}"
                   class="nav-link-custom {{ request()->routeIs('units.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-building-2-line"></i>
                    <span class="nav-text">Unit</span>
                </a>
            </div>

            <div class="sidebar-section-title">Permintaan</div>

            <div class="nav-item">
                <a href="{{ route('requests.index') }}"
                   class="nav-link-custom {{ request()->routeIs('requests.index') && !request('status') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-clipboard-line"></i>
                    <span class="nav-text">Daftar Permintaan</span>
                </a>
            </div>

            <div class="sidebar-section-title">Transaksi & Stok</div>
            <div class="nav-item">
                <a href="{{ route('transactions.index', ['type' => 'MASUK']) }}"
                   class="nav-link-custom {{ request()->routeIs('transactions.index') && request('type') === 'MASUK' ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-arrow-down-line"></i>
                    <span class="nav-text">Barang Masuk</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('transactions.index', ['type' => 'KELUAR']) }}"
                   class="nav-link-custom {{ request()->routeIs('transactions.index') && request('type') === 'KELUAR' ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-arrow-up-line"></i>
                    <span class="nav-text">Barang Keluar</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('stock.index') }}"
                   class="nav-link-custom {{ request()->routeIs('stock.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-database-2-line"></i>
                    <span class="nav-text">Stok Barang</span>
                </a>
            </div>

            {{-- MENU BARU UNTUK REVISI (ADMIN CUMA LIHAT) --}}
            <div class="nav-item">
                <a href="{{ route('stock-opnames.index') }}"
                   class="nav-link-custom {{ request()->routeIs('stock-opnames.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-survey-line"></i>
                    <span class="nav-text">Stok Opname</span>
                </a>
            </div>
            {{-- END MENU BARU --}}

            {{-- 🔹 BATCH PER LOT (ADMIN GUDANG) --}}
            <div class="nav-item">
                <a href="{{ route('batches.index') }}"
                   class="nav-link-custom {{ request()->routeIs('batches.*') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-stack-line"></i>
                    <span class="nav-text">Batch per Lot</span>
                </a>
            </div>

            <div class="sidebar-section-title">Laporan</div>

            <div class="nav-item">
                <a href="{{ route('reports.distribution') }}"
                   class="nav-link-custom {{ request()->routeIs('reports.distribution') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-file-chart-line"></i>
                    <span class="nav-text">Laporan Distribusi</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('reports.stock') }}"
                   class="nav-link-custom {{ request()->routeIs('reports.stock') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-file-list-3-line"></i>
                    <span class="nav-text">Laporan Stok Akhir</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('reports.usage_yearly') }}"
                   class="nav-link-custom {{ request()->routeIs('reports.usage_yearly') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-bar-chart-2-line"></i>
                    <span class="nav-text">Rekap Pemakaian Tahunan</span>
                </a>
            </div>
        @endif

        {{-- ===================== PETUGAS UNIT ===================== --}}
        @if($role === 'petugas unit')
            <div class="sidebar-section-title">Permintaan</div>

            <div class="nav-item">
                <a href="{{ route('requests.index') }}"
                   class="nav-link-custom {{ request()->routeIs('requests.index') && !request('status') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-clipboard-line"></i>
                    <span class="nav-text">Daftar Permintaan</span>
                </a>
            </div>

            <div class="sidebar-section-title">Laporan Unit</div>

            <div class="nav-item">
                <a href="{{ route('reports.unit_received') }}"
                   class="nav-link-custom {{ request()->routeIs('reports.unit_received') ? 'nav-link-active' : '' }}">
                    <i class="nav-icon ri-box-3-line"></i>
                    <span class="nav-text">Laporan Barang Diterima</span>
                </a>
            </div>
        @endif
    </nav>

    <div class="sidebar-footer">
        <span>&copy; {{ now()->year }} Uptd Labkesda Kab. Cirebon</span>
    </div>
</aside>