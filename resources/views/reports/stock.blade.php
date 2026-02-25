@extends('layouts.app')

@section('title', 'Laporan Stok Akhir')

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
        box-shadow:0 10px 25px rgba(15,23,42,.25);
        display:inline-flex;
        align-items:center;
        gap:6px;
        transition:all .15s ease;
        white-space:nowrap;
    }
    .btn-primary-gradient i{
        font-size:18px;
    }
    .btn-primary-gradient:hover{
        transform:translateY(-1px);
        box-shadow:0 14px 32px rgba(15,23,42,.25);
        opacity:.97;
        color:#000 !important;
    }
    .btn-primary-gradient:active{
        transform:translateY(0);
        box-shadow:0 6px 18px rgba(15,23,42,.25);
        color:#000 !important;
    }

    .btn-danger-soft{
        border:none;
        border-radius:999px;
        padding:.5rem 1.4rem;
        font-size:13px;
        font-weight:600;
        background:#fee2e2;
        color:#b91c1c;
        box-shadow:0 10px 25px rgba(15,23,42,.08);
        display:inline-flex;
        align-items:center;
        gap:6px;
        transition:all .15s ease;
        white-space:nowrap;
    }
    .btn-danger-soft i{
        font-size:18px;
    }
    .btn-danger-soft:hover{
        background:#fecaca;
        transform:translateY(-1px);
        box-shadow:0 14px 32px rgba(15,23,42,.12);
    }
    .btn-danger-soft:active{
        background:#fca5a5;
        transform:translateY(0);
        box-shadow:0 6px 18px rgba(15,23,42,.12);
    }

    .btn-reset-filter{
        border-radius:999px;
        padding:.45rem 1.3rem;
        font-size:13px;
        font-weight:600;
        background:#6b7280;
        color:#ffffff;
        border:1px solid transparent;
        display:inline-flex;
        align-items:center;
        gap:6px;
        transition:all .15s ease;
        white-space:nowrap;
    }
    .btn-reset-filter:hover{
        background:#4b5563;
        transform:translateY(-1px);
    }
    .btn-reset-filter:active{
        background:#374151;
        transform:translateY(0);
    }

    .btn-reset-filter.is-inactive{
        visibility:hidden;
        pointer-events:none;
    }

    .panel-card{
        background:#ffffff;
        border-radius:18px;
        border:1px solid #e5e7eb;
        padding:14px 16px 16px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
        margin-bottom:14px;
    }

    .request-filters{
        display:flex;
        flex-wrap:wrap;
        gap:.9rem;
        align-items:flex-start;
        margin-bottom:10px;
    }
    .filter-group{
        display:flex;
        flex-direction:column;
        gap:4px;
        min-width:260px;
    }
    .filter-label{
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--text-muted);
        margin:0;
    }
    .filter-input,
    .filter-select{
        font-size:13px;
        border-radius:999px;
        border:1px solid #e5e7eb;
        padding:.45rem .9rem;
        box-shadow:0 6px 18px rgba(15,23,42,.04);
    }

    .filter-group-actions-right{
        margin-left:auto;
    }
    .filter-actions{
        width:100%;
        display:flex;
        flex-direction:column;
        align-items:flex-end;
        gap:.45rem;
    }
    .action-row{
        width:100%;
        display:flex;
        justify-content:flex-end;
        align-items:center;
        gap:.4rem;
        flex-wrap:wrap;
    }

    .table-requests{
        font-size:13px;
        border-collapse:separate;
        border-spacing:0;
    }
    .table-requests thead th{
        border-top:none;
        border-bottom:1px solid #e5e7eb;
        background:#f9fafb;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--text-muted);
    }
    .table-requests tbody tr:hover{
        background:#f9fafb;
    }

    .meta-small{
        font-size:11px;
        color:var(--text-muted);
    }

    .mini-chip{
        font-size:10px;
        padding:4px 8px;
        border-radius:999px;
        background:#f9fafb;
        border:1px solid #e5e7eb;
    }

    .badge-fefo{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:2px 8px;
        border-radius:999px;
        font-size:10px;
        font-weight:600;
        line-height:1.2;
    }
    .badge-fefo--expired{
        background:#fee2e2;
        color:#b91c1c;
    }
    .badge-fefo--near{
        background:#fef3c7;
        color:#92400e;
    }
    .badge-fefo--ok{
        background:#dcfce7;
        color:#166534;
    }
    .badge-fefo--noexp{
        background:#e5e7eb;
        color:#374151;
    }

    @media (max-width: 991.98px){
        .request-filters{
            flex-direction:column;
        }
        .filter-group{
            min-width:100%;
        }
        .filter-group-actions-right{
            margin-left:0;
        }
        .filter-actions{
            align-items:stretch;
        }
        .action-row{
            justify-content:flex-start;
            align-items:stretch;
        }
    }

    @media (max-width: 767.98px){
        .page-title-wrap{
            flex-direction:column;
        }
        .btn-primary-gradient{
            width:100%;
            justify-content:center;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Laporan Stok Akhir</h1>
            <p class="page-subtitle">
                Rekap posisi <strong>stok akhir</strong> untuk setiap item dan lot di gudang Labkesda.
            </p>
        </div>
        <div></div>
    </div>

    {{-- FILTER PANEL --}}
    <div class="panel-card">
        <h6 class="panel-title mb-1">Filter Laporan</h6>
        <p class="panel-subtitle mb-3">
            Gunakan filter di bawah ini untuk menampilkan stok akhir sesuai kebutuhan.
        </p>

        @php
            $modeVal = $mode ?? 'simple';

            // ✅ Reset aktif jika ada filter apa pun (termasuk checkbox "reagen")
            $hasAnyFilter = (bool)($onlyReagen) || !empty($asOf) || ($modeVal !== 'simple');
            $resetInactiveClass = $hasAnyFilter ? '' : 'is-inactive';
        @endphp

        <form method="GET" action="{{ route('reports.stock') }}">
            <div class="request-filters">

                {{-- HANYA REAGEN --}}
                <div class="filter-group">
                    <label class="filter-label">Kategori</label>
                    <div class="filter-input d-flex align-items-center gap-2">
                        <input type="checkbox"
                               name="reagen_only"
                               id="reagen_only"
                               value="1"
                               class="form-check-input m-0"
                               {{ $onlyReagen ? 'checked' : '' }}>
                        <span class="meta-small">
                            Hanya kategori yang mengandung kata "<strong>reagen</strong>"
                        </span>
                    </div>
                </div>

                {{-- PER TANGGAL --}}
                <div class="filter-group">
                    <label class="filter-label">Posisi per tanggal</label>
                    <input type="date"
                           name="as_of"
                           value="{{ $asOf }}"
                           class="form-control form-control-sm filter-input">
                    <span class="meta-small">
                        Kosongkan untuk posisi stok terkini (semua transaksi).
                    </span>
                </div>

                {{-- MODE TAMPILAN --}}
                <div class="filter-group">
                    <label class="filter-label">Tampilan</label>
                    <select name="mode" class="form-select form-select-sm filter-select">
                        <option value="simple" {{ $modeVal === 'simple' ? 'selected' : '' }}>
                            Sederhana (Qty Akhir per Lot)
                        </option>
                        <option value="opname" {{ $modeVal === 'opname' ? 'selected' : '' }}>
                            Format Stok Opname (Saldo–Masuk–Keluar–Sisa)
                        </option>
                    </select>
                </div>

                {{-- ACTIONS --}}
                <div class="filter-group filter-group-actions-right">
                    <label class="filter-label">&nbsp;</label>

                    <div class="filter-actions">
                        {{-- ✅ BARIS ATAS (posisi lama Reset): Tampilkan + Cetak PDF --}}
                        <div class="action-row">
                            <button type="submit" class="btn btn-primary-gradient">
                                <i class="ri-search-line"></i>
                                Tampilkan
                            </button>

                            <a href="{{ route('reports.stock.print', [
                                    'reagen_only' => $onlyReagen ? 1 : null,
                                    'as_of'       => $asOf,
                                    'mode'        => $modeVal,
                                ]) }}"
                               class="btn-danger-soft"
                               target="_blank">
                                <i class="ri-file-pdf-line"></i>
                                Cetak PDF
                            </a>
                        </div>

                        {{-- ✅ BARIS BAWAH (posisi lama PDF): Reset --}}
                        <div class="action-row">
                            <button type="button"
                                    onclick="window.location='{{ route('reports.stock') }}'"
                                    class="btn-reset-filter {{ $resetInactiveClass }}"
                                    {{ $hasAnyFilter ? '' : 'disabled' }}>
                                <i class="ri-refresh-line me-1"></i>
                                Reset
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- TABEL STOK AKHIR --}}
    <div class="panel-card">
        <div class="d-flex justify-content-between mb-2">
            <div>
                <h6 class="panel-title mb-0">Data Stok Akhir</h6>
                <p class="page-subtitle mb-0">
                    Menampilkan stok per item, merek, dan lot berdasarkan hasil filter.
                </p>
            </div>
            <div class="text-end d-none d-md-block">
                <span class="mini-chip">
                    Kategori:
                    {{ $onlyReagen ? 'Hanya kategori reagen' : 'Semua kategori' }}
                </span>
                <span class="mini-chip ms-1">
                    Posisi stok:
                    @if($asOf)
                        per {{ \Carbon\Carbon::parse($asOf)->format('d-m-Y') }}
                    @else
                        terkini (s/d semua transaksi)
                    @endif
                </span>
                <span class="mini-chip ms-1">
                    Tampilan:
                    @if($modeVal === 'opname')
                        Format Stok Opname
                    @else
                        Sederhana (Qty Akhir per Lot)
                    @endif
                </span>
            </div>
        </div>

        <div class="table-responsive">
            @if($modeVal === 'simple')
                {{-- MODE SEDERHANA: Qty Akhir per Lot --}}
                <table class="table table-requests align-middle mb-0">
                    <thead>
                        <tr class="text-nowrap">
                            <th style="width:40px;">No</th>
                            <th style="width:90px;">Kode</th>
                            <th>Nama Item</th>
                            <th style="width:130px;">Merek</th>
                            <th style="width:110px;">Lot</th>
                            <th style="width:100px;" class="text-center">Exp</th>
                            <th style="width:80px;" class="text-center">FEFO</th>
                            <th style="width:70px;" class="text-center">Sat</th>
                            <th style="width:100px;" class="text-end">Qty Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($variants as $i => $v)
                        @php
                            $item      = optional($v->itemMaster);
                            $qty       = (int) ($qtyByVariant[$v->id] ?? 0);
                            $fefoLabel = $v->fefo_label_text ?? null;
                            $fefoClass = $v->fefo_css_class ?? null;
                        @endphp
                        <tr>
                            <td class="meta-small text-center">{{ $i + 1 }}</td>
                            <td class="meta-small">{{ $item->item_code ?? '-' }}</td>
                            <td>
                                <div><strong>{{ $item->item_name ?? '-' }}</strong></div>
                            </td>
                            <td class="meta-small">{{ $v->brand ?: '-' }}</td>
                            <td class="meta-small">{{ $v->lot_number ?: '-' }}</td>
                            <td class="meta-small text-center">
                                @if(!empty($v->expiration_date))
                                    {{ $v->expiration_date instanceof \Illuminate\Support\Carbon
                                            ? $v->expiration_date->format('d-m-Y')
                                            : \Illuminate\Support\Carbon::parse($v->expiration_date)->format('d-m-Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="meta-small text-center">
                                @if($fefoLabel)
                                    <span class="badge-fefo {{ $fefoClass }}">
                                        {{ $fefoLabel }}
                                    </span>
                                @else
                                    <span class="badge-fefo badge-fefo--noexp">-</span>
                                @endif
                            </td>
                            <td class="meta-small text-center">
                                {{ $item->base_unit ?? '-' }}
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($qty, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                Tidak ada data stok.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            @else
                {{-- MODE STOK OPNAME: Saldo–Masuk–Keluar–Sisa --}}
                <table class="table table-requests align-middle mb-0">
                    <thead>
                        <tr class="text-nowrap">
                            <th style="width:40px;">No</th>
                            <th style="width:90px;">Kode</th>
                            <th>Nama Item</th>
                            <th style="width:130px;">Merek</th>
                            <th style="width:110px;">Lot</th>
                            <th style="width:100px;" class="text-center">Exp</th>
                            <th style="width:70px;" class="text-center">Sat</th>
                            <th style="width:100px;" class="text-end">Saldo Awal</th>
                            <th style="width:100px;" class="text-end">Masuk</th>
                            <th style="width:100px;" class="text-end">Keluar</th>
                            <th style="width:100px;" class="text-end">Sisa Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($variants as $i => $v)
                        @php
                            $item  = optional($v->itemMaster);
                            $id    = $v->id;
                            $open  = (int) ($saldoAwal[$id] ?? 0);
                            $in    = (int) ($qtyInPeriod[$id] ?? 0);
                            $out   = (int) ($qtyOutPeriod[$id] ?? 0);
                            $end   = (int) ($saldoAkhirOpname[$id] ?? 0);
                        @endphp
                        <tr>
                            <td class="meta-small text-center">{{ $i + 1 }}</td>
                            <td class="meta-small">{{ $item->item_code ?? '-' }}</td>
                            <td>
                                <div><strong>{{ $item->item_name ?? '-' }}</strong></div>
                            </td>
                            <td class="meta-small">{{ $v->brand ?: '-' }}</td>
                            <td class="meta-small">{{ $v->lot_number ?: '-' }}</td>
                            <td class="meta-small text-center">
                                @if(!empty($v->expiration_date))
                                    {{ $v->expiration_date instanceof \Illuminate\Support\Carbon
                                            ? $v->expiration_date->format('d-m-Y')
                                            : \Illuminate\Support\Carbon::parse($v->expiration_date)->format('d-m-Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="meta-small text-center">
                                {{ $item->base_unit ?? '-' }}
                            </td>
                            <td class="text-end meta-small">
                                {{ number_format($open, 0, ',', '.') }}
                            </td>
                            <td class="text-end meta-small">
                                {{ number_format($in, 0, ',', '.') }}
                            </td>
                            <td class="text-end meta-small">
                                {{ number_format($out, 0, ',', '.') }}
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($end, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">
                                Tidak ada data stok.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection
