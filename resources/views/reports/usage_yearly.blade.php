@extends('layouts.app')
@section('title','Rekap Pemakaian Tahunan')

@push('styles')
<style>
    .page-title-wrap{
        display:flex;
        align-items:flex-start;
        justify-content:space-between;
        gap:12px;
        margin-bottom:14px;
    }
    .page-title{
        font-size:22px;
        font-weight:700;
        margin:0;
    }
    .page-subtitle{
        font-size:12px;
        color:var(--text-muted);
        margin:2px 0 0;
    }

    .btn-primary-gradient{
        border:none;
        border-radius:999px;
        padding:.5rem 1.4rem;
        font-size:13px;
        font-weight:600;
        background:linear-gradient(90deg,#4f46e5,#06b6d4);
        color:#fff;
        display:inline-flex;
        align-items:center;
        gap:6px;
        box-shadow:0 10px 25px rgba(15,23,42,.25);
        transition:all .15s ease;
    }
    .btn-primary-gradient:hover{
        transform:translateY(-1px);
        box-shadow:0 14px 32px rgba(15,23,42,.25);
        color:#000 !important;
    }

    .btn-danger-soft{
        border:none;
        border-radius:999px;
        padding:.5rem 1.4rem;
        background:#fee2e2;
        color:#b91c1c;
        font-size:13px;
        font-weight:600;
        display:inline-flex;
        align-items:center;
        gap:6px;
        box-shadow:0 10px 25px rgba(15,23,42,.1);
        transition:all .15s ease;
    }
    .btn-danger-soft:hover{
        background:#fecaca;
        transform:translateY(-1px);
    }

    .btn-reset-filter{
        border-radius:999px;
        padding:.45rem 1.3rem;
        font-size:13px;
        background:#6b7280;
        color:white;
        display:inline-flex;
        align-items:center;
        gap:6px;
        transition:.15s;
    }
    .btn-reset-filter:hover{
        background:#4b5563;
        transform:translateY(-1px);
    }

    .panel-card{
        background:white;
        border-radius:18px;
        border:1px solid #e5e7eb;
        padding:16px 18px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
        margin-bottom:16px;
    }

    .filter-group{display:flex;flex-direction:column;gap:4px;}
    .filter-label{
        font-size:11px;
        letter-spacing:.06em;
        color:var(--text-muted);
        text-transform:uppercase;
    }
    .filter-select,
    .filter-input{
        font-size:13px;
        border-radius:999px;
        border:1px solid #e5e7eb;
        padding:.45rem .9rem;
        box-shadow:0 6px 18px rgba(15,23,42,.04);
    }
    .request-filters{
        display:flex;
        flex-wrap:wrap;
        gap:.7rem;
        align-items:flex-end;
    }

    .table-requests{
        border-collapse:separate;
        border-spacing:0;
    }
    .table-requests thead th{
        background:#f9fafb;
        border-bottom:1px solid #e5e7eb;
        font-size:11px;
        letter-spacing:.06em;
        text-transform:uppercase;
        vertical-align:middle;
    }
    .table-requests th,
    .table-requests td{
        border-bottom:1px solid #e5e7eb;
        vertical-align:middle;
    }
    .table-requests tbody tr:hover{
        background:#f9fafb;
    }

    .meta-small{font-size:11px;color:var(--text-muted);}
    .mini-chip{
        font-size:10px;
        padding:4px 8px;
        border-radius:999px;
        border:1px solid #e5e7eb;
        background:#f9fafb;
    }
</style>
@endpush

@section('content')
@php
    $modeLabel = $mode === 'variant'
        ? 'Per Item + Varian (Merek/Lot)'
        : 'Per Item';

    $monthNames = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    // No, Kode, Nama, Sat  => 4 kolom non-bulan
    // mode variant: + Brand, Lot, Exp => total 7 kolom non-bulan
    $nonMonthCols = ($mode === 'variant') ? 7 : 4;
    $totalColumns = $nonMonthCols + 12 + 1; // 12 bulan + kolom total
@endphp

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Rekap Pemakaian Tahunan</h1>
            <p class="page-subtitle">
                Rekap total pemakaian barang keluar per bulan dalam satu tahun.
            </p>
        </div>
        <div></div>
    </div>

    {{-- FILTER PANEL --}}
    <div class="panel-card">
        <form method="GET">
            <div class="request-filters">

                {{-- Tahun --}}
                <div class="filter-group">
                    <label class="filter-label">Tahun</label>
                    <select name="year" class="filter-select">
                        @if($availableYears->isEmpty())
                            <option>{{ $year }}</option>
                        @else
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" {{ (int)$year==(int)$y ? 'selected':'' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Mode --}}
                <div class="filter-group">
                    <label class="filter-label">Mode Rekap</label>
                    <select name="mode" class="filter-select">
                        <option value="item" {{ $mode==='item'?'selected':'' }}>Per Item</option>
                        <option value="variant" {{ $mode==='variant'?'selected':'' }}>Per Item + Varian</option>
                    </select>
                </div>

                {{-- Aksi --}}
                <div class="filter-group">
                    <label class="filter-label">&nbsp;</label>
                    <div style="display:flex; gap:.4rem">

                        <button class="btn-primary-gradient">
                            <i class="ri-search-line"></i>
                            Tampilkan
                        </button>

                        <a href="{{ route('reports.usage_yearly.print', ['year' => $year, 'mode' => $mode]) }}"
                           class="btn-danger-soft"
                           target="_blank">
                            <i class="ri-file-pdf-line"></i>
                            Cetak PDF
                        </a>

                        @if(request('year') || request('mode'))
                        <button type="button"
                                onclick="window.location='{{ route('reports.usage_yearly') }}'"
                                class="btn-reset-filter">
                            <i class="ri-refresh-line"></i>
                            Reset
                        </button>
                        @endif

                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- TABEL --}}
    <div class="panel-card">
        <div class="d-flex justify-content-between mb-2">
            <div>
                <h6 class="panel-title mb-0">Data Rekap</h6>
                <p class="meta-small mb-0">
                    Tahun: <strong>{{ $year }}</strong> — Mode: <strong>{{ $modeLabel }}</strong>
                </p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-requests align-middle mb-0">
                <thead>
                    <tr class="text-nowrap">
                        <th class="text-center" style="width:40px">No</th>
                        <th class="text-center" style="width:90px">Kode Item</th>
                        <th class="text-left">Nama Item</th>
                        <th class="text-center" style="width:60px">Sat</th>

                        @if($mode==='variant')
                            <th class="text-left" style="width:140px">Brand</th>
                            <th class="text-center" style="width:110px">Lot</th>
                            <th class="text-center" style="width:110px">Exp</th>
                        @endif

                        @foreach($monthNames as $label)
                            <th class="text-end" style="width:70px">{{ $label }}</th>
                        @endforeach

                        <th class="text-end" style="width:90px">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($rows as $i => $row)
                        @php
                            $qtyByMonth = $row->qty_by_month ?? [];
                        @endphp
                        <tr>
                            <td class="meta-small text-center">{{ $i+1 }}</td>
                            <td class="text-center">
                                <strong>{{ $row->item_code }}</strong>
                            </td>
                            <td class="align-middle">
                                {{ $row->item_name }}
                            </td>
                            <td class="meta-small text-center align-middle">
                                {{ $row->base_unit ?? '—' }}
                            </td>

                            @if($mode==='variant')
                                <td class="meta-small align-middle">
                                    {{ $row->brand ?: '—' }}
                                </td>
                                <td class="meta-small text-center align-middle">
                                    {{ $row->lot_number ?: '—' }}
                                </td>
                                <td class="meta-small text-center align-middle">
                                    @if(!empty($row->expiration_date))
                                        {{ \Carbon\Carbon::parse($row->expiration_date)->format('d-m-Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                            @endif

                            @for($m = 1; $m <= 12; $m++)
                                <td class="text-end meta-small">
                                    {{ $qtyByMonth[$m] ?? 0 ? number_format($qtyByMonth[$m],0,',','.') : '0' }}
                                </td>
                            @endfor

                            <td class="text-end align-middle">
                                <strong>{{ number_format($row->total_qty, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $totalColumns }}"
                                class="text-center text-muted py-4">
                                Tidak ada data pemakaian untuk tahun ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                @if(count($rows) > 0)
                    <tfoot>
                        <tr>
                            <th colspan="{{ $nonMonthCols }}" class="text-end">
                                TOTAL
                            </th>

                            @for($m = 1; $m <= 12; $m++)
                                <th class="text-end">
                                    {{ $monthlyTotals[$m] ?? 0 ? number_format($monthlyTotals[$m],0,',','.') : '0' }}
                                </th>
                            @endfor

                            <th class="text-end">
                                {{ number_format($grandTotal ?? 0, 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                @endif

            </table>
        </div>
    </div>

</div>
@endsection
