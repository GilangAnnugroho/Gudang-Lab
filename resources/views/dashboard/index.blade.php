@extends('layouts.app')
@section('title','Dashboard')
@section('page_title','Dashboard')

@section('content')
@php
    use App\Models\ItemMaster;
    use App\Models\ItemVariant;
    use App\Models\Request;
    use App\Models\Transaction;
    use Illuminate\Support\Carbon;
    use Illuminate\Support\Facades\DB;

    $totalItems        = ItemMaster::count();
    $totalVariants     = ItemVariant::count();
    $pendingRequests   = Request::where('status', 'PENDING')->count();
    $totalTransactions = Transaction::count();
    $today = Carbon::today();

    $reagenVariants = ItemVariant::whereHas('itemMaster', function($q) {
        $q->whereHas('category', function($qc) {
            $qc->where('category_name', 'like', '%reagen%');
        });
    })
    ->whereNotNull('expiration_date')
    ->get();

    $expiredCount = 0;
    $redCount     = 0;
    $yellowCount  = 0;
    $greenCount   = 0;

    foreach ($reagenVariants as $v) {
        $exp = $v->expiration_date;
        if (!$exp) continue;

        $diffDays = $today->diffInDays($exp, false);
        if ($diffDays < 0) {
            $expiredCount++;
            continue;
        }

        $diffMonths = $today->diffInMonths($exp);

        if ($diffMonths < 3) {
            $redCount++;
        } elseif ($diffMonths <= 12) {
            $yellowCount++;
        } else {
            $greenCount++;
        }
    }

    /*
    ✅ PERBAIKAN TAHUN TRANSAKSI OTOMATIS
    */

    $currentYear = $today->year;

    $hasThisYearTransaction = Transaction::whereYear('trans_date', $currentYear)->exists();

    if ($hasThisYearTransaction) {
        $year = $currentYear;
    } else {
        $year = Transaction::selectRaw('YEAR(trans_date) as year')
            ->orderByDesc('year')
            ->value('year');
    }

    $monthlyIn  = array_fill(1, 12, 0);
    $monthlyOut = array_fill(1, 12, 0);

    $monthly = Transaction::selectRaw('MONTH(trans_date) as month, type, SUM(quantity) as total')
        ->whereYear('trans_date', $year)
        ->groupBy('month', 'type')
        ->get();

    foreach ($monthly as $row) {
        if ($row->type === 'MASUK') {
            $monthlyIn[$row->month] = (int)$row->total;
        } elseif ($row->type === 'KELUAR') {
            $monthlyOut[$row->month] = (int)$row->total;
        }
    }

    $monthsLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    $stockByCategory = DB::table('stock_current as sc')
        ->join('item_variants as iv', 'iv.id', '=', 'sc.item_variant_id')
        ->join('items_master as im', 'im.id', '=', 'iv.item_master_id')
        ->join('categories as c', 'c.id', '=', 'im.category_id')
        ->selectRaw('c.category_name as category, SUM(sc.current_quantity) as total')
        ->groupBy('category')
        ->orderBy('category')
        ->get();

    $stockCategoryLabels = $stockByCategory->pluck('category')->toArray();
    $stockCategoryTotals = $stockByCategory->pluck('total')->map(fn($v) => (int)$v)->toArray();

    $compositionMap = Transaction::selectRaw('type, SUM(quantity) as total')
        ->whereYear('trans_date', $year)
        ->groupBy('type')
        ->get()
        ->pluck('total', 'type')
        ->toArray();

    $pieMasuk  = (int)($compositionMap['MASUK']  ?? 0);
    $pieKeluar = (int)($compositionMap['KELUAR'] ?? 0);

    $recentDistributions = Transaction::with(['variant.itemMaster', 'unit', 'request'])
        ->where('type', 'KELUAR')
        ->orderByDesc('trans_date')
        ->limit(5)
        ->get();
@endphp

{{-- ROW: STATISTIK UTAMA --}}
<div class="row g-3 mb-3">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="stat-icon-badge">
                <i class="ri-archive-2-line"></i>
            </div>
            <div class="stat-label">Item Master</div>
            <div class="stat-value">{{ $totalItems }}</div>
            <div class="stat-meta">Total jenis barang terdaftar</div>

            <a href="{{ route('items.index') }}"
               class="d-inline-flex align-items-center mt-2 small text-decoration-none fw-medium"
               style="color:#4f46e5;">
                Lihat detail
                <i class="ri-arrow-right-up-line ms-1"></i>
            </a>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card stat-card-green">
            <div class="stat-icon-badge">
                <i class="ri-price-tag-3-line"></i>
            </div>
            <div class="stat-label">Item Variants</div>
            <div class="stat-value">{{ $totalVariants }}</div>
            <div class="stat-meta">Merek / lot aktif di gudang</div>

            <a href="{{ route('variants.index') }}"
               class="d-inline-flex align-items-center mt-2 small text-decoration-none fw-medium"
               style="color:#16a34a;">
                Lihat detail
                <i class="ri-arrow-right-up-line ms-1"></i>
            </a>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card stat-card-yellow">
            <div class="stat-icon-badge">
                <i class="ri-file-list-3-line"></i>
            </div>
            <div class="stat-label">Request Pending</div>
            <div class="stat-value">{{ $pendingRequests }}</div>
            <div class="stat-meta">Permintaan menunggu approval</div>

            <a href="{{ route('requests.index', ['status' => 'PENDING']) }}"
               class="d-inline-flex align-items-center mt-2 small text-decoration-none fw-medium"
               style="color:#d97706;">
                Lihat detail
                <i class="ri-arrow-right-up-line ms-1"></i>
            </a>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="stat-card stat-card-red">
            <div class="stat-icon-badge">
                <i class="ri-exchange-dollar-line"></i>
            </div>
            <div class="stat-label">Total Transaksi</div>
            <div class="stat-value">{{ $totalTransactions }}</div>
            <div class="stat-meta">Transaksi barang masuk & keluar</div>

            <a href="{{ route('transactions.index') }}"
               class="d-inline-flex align-items-center mt-2 small text-decoration-none fw-medium"
               style="color:#dc2626;">
                Lihat detail
                <i class="ri-arrow-right-up-line ms-1"></i>
            </a>
        </div>
    </div>
</div>

    {{-- ROW: REAGEN FEFO + CHARTS --}}
    <div class="row g-3 mb-3">
    {{-- STATUS REAGEN --}}
    <div class="col-12 col-lg-6 col-xxl-4">
        <div class="panel-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <div class="panel-title">Status Reagen</div>
                    <div class="panel-subtitle">Monitoring kadaluwarsa & warna indikator</div>
                </div>
                <div><span class="mini-chip">Reagen</span></div>
            </div>

            {{-- EXPIRED --}}
            <div class="reagent-card mb-2">
                <div>
                    <strong>{{ $expiredCount }}</strong> Reagen <strong>EXPIRED</strong>
                </div>
                <a href="{{ route('stock.index', ['fefo' => 'expired']) }}"
                class="reagent-chip text-decoration-none">
                    Lihat stok EXPIRED
                    <i class="ri-arrow-right-up-line ms-1"></i>
                </a>
            </div>

            {{-- MERAH --}}
            <div class="reagent-card mb-2" style="background:#fee2e2;color:#b91c1c;">
                <div>
                    <strong>{{ $redCount }}</strong> Reagen <strong>&lt; 3 bulan</strong> (MERAH)
                </div>
                <a href="{{ route('stock.index', ['fefo' => 'merah']) }}"
                class="reagent-chip text-decoration-none"
                style="background:#fecaca;">
                    Lihat varian MERAH
                    <i class="ri-arrow-right-up-line ms-1"></i>
                </a>
            </div>

            {{-- KUNING --}}
            <div class="reagent-card" style="background:#fef3c7;color:#92400e;">
                <div>
                    <strong>{{ $yellowCount }}</strong> Reagen <strong>3 – 12 bulan</strong> (KUNING)
                </div>
                <a href="{{ route('stock.index', ['fefo' => 'kuning']) }}"
                class="reagent-chip text-decoration-none"
                style="background:#fde68a;">
                    Lihat varian KUNING
                    <i class="ri-arrow-right-up-line ms-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- CHART 1 --}}
    <div class="col-12 col-lg-6 col-xxl-4">
        <div class="panel-card">
            <div class="panel-title">Ringkasan Stok Aktif</div>
            <canvas id="stockByCategoryChart" height="150"></canvas>
        </div>
    </div>

    {{-- CHART 2 --}}
    <div class="col-12 col-lg-12 col-xxl-4">
        <div class="panel-card">
            <div class="panel-title">Komposisi Transaksi</div>
            <canvas id="transactionPieChart" height="150"></canvas>
        </div>
    </div>
</div>

{{-- ROW: BULANAN + ✅ DISTRIBUSI TERBARU --}}
<div class="row g-3">

    {{-- BULANAN --}}
    <div class="col-12 col-xl-7">
        <div class="panel-card h-100">
            <div class="panel-title">Volume Transaksi Bulanan Tahun {{ $year }}</div>
            <canvas id="transactionMonthlyChart" height="210"></canvas>
        </div>
    </div>

    {{-- ✅ AKTIVITAS DISTRIBUSI TERBARU (DITAMBAHKAN PENUH TANPA DIUBAH) --}}
    <div class="col-12 col-xl-5">
        <div class="panel-card h-100 d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <div class="panel-title">Aktivitas Distribusi Terbaru</div>
                    <div class="panel-subtitle">5 transaksi barang keluar terakhir</div>
                </div>
                <div><span class="mini-chip">Distribusi</span></div>
            </div>

            @if($recentDistributions->isEmpty())
                <p class="text-muted small mb-0">Belum ada data distribusi barang keluar.</p>
            @else
                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle mb-0" style="font-size:13px;">
                        <thead style="background:#f9fafb;">
                        <tr class="text-uppercase" style="font-size:11px;letter-spacing:.05em;color:#6b7280;">
                            <th class="border-0">Tanggal</th>
                            <th class="border-0">No. Permintaan</th>
                            <th class="border-0">Unit Penerima</th>
                            <th class="border-0">Item Dikeluarkan</th>
                            <th class="border-0 text-center">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($recentDistributions as $trx)
                            @php
                                $req        = $trx->request;
                                $reqCode    = $req ? ('REQ-'.$req->id) : '-';
                                $unitName   = optional($trx->unit)->unit_name ?? '-';
                                $item       = optional(optional($trx->variant)->itemMaster);
                                $itemName   = $item->item_name ?? 'Item tidak diketahui';
                                $baseUnit   = $item->base_unit ?? '';
                                $qty        = $trx->quantity ?? 0;
                                $status     = $req->status ?? 'DISTRIBUTED';

                                $statusClass = 'pill-status pending';
                                if ($status === 'DISTRIBUTED') {
                                    $statusClass = 'pill-status in';
                                } elseif ($status === 'REJECTED') {
                                    $statusClass = 'pill-status out';
                                }
                            @endphp
                            <tr>
                                <td style="white-space:nowrap;">
                                    {{ optional($trx->trans_date)->format('d M Y') ?? '-' }}
                                </td>
                                <td>{{ $reqCode }}</td>
                                <td>{{ $unitName }}</td>
                                <td>
                                    {{ $itemName }}
                                    @if($qty)
                                        ({{ $qty }} {{ $baseUnit }})
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="{{ $statusClass }}">
                                        {{ strtoupper($status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-auto d-flex justify-content-end">
                <a href="{{ route('transactions.index', ['type' => 'KELUAR']) }}"
                   class="text-decoration-none small fw-medium"
                   style="color:#4f46e5;">
                    Lihat semua distribusi
                    <i class="ri-arrow-right-up-line ms-1"></i>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    new Chart(document.getElementById('stockByCategoryChart'), {
        type: 'bar',
        data: {
            labels: @json($stockCategoryLabels),
            datasets: [{
                label: 'Total Stok',
                data: @json($stockCategoryTotals),
                borderRadius: 8
            }]
        }
    });

    new Chart(document.getElementById('transactionPieChart'), {
        type: 'doughnut',
        data: {
            labels: ['MASUK', 'KELUAR'],
            datasets: [{
                data: [{{ $pieMasuk }}, {{ $pieKeluar }}]
            }]
        }
    });

    new Chart(document.getElementById('transactionMonthlyChart'), {
        type: 'bar',
        data: {
            labels: @json($monthsLabels),
            datasets: [
                { label: 'Barang Masuk', data: @json(array_values($monthlyIn)) },
                { label: 'Barang Keluar', data: @json(array_values($monthlyOut)) }
            ]
        }
    });
</script>
@endpush
