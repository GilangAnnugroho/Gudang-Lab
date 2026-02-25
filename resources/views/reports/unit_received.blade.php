@extends('layouts.app')

@section('title', 'Laporan Barang Diterima')

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
    }
    .btn-reset-filter:hover{
        background:#4b5563;
        transform:translateY(-1px);
    }
    .btn-reset-filter:active{
        background:#374151;
        transform:translateY(0);
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
        gap:.65rem;
        align-items:flex-end;
        margin-bottom:10px;
    }
    .filter-group{
        display:flex;
        flex-direction:column;
        gap:4px;
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
    .filter-actions{
        display:flex;
        gap:.4rem;
        align-items:center;
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

    @media (max-width: 767.98px){
        .page-title-wrap{
            flex-direction:column;
        }
        .btn-primary-gradient{
            width:100%;
            justify-content:center;
        }
        .request-filters{
            flex-direction:column;
            align-items:stretch;
        }
    }
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
@endphp

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Laporan Barang Diterima Unit</h1>
            <p class="page-subtitle">
                Rekap transaksi <strong>barang yang diterima</strong> oleh unit
                <strong>{{ optional($user->unit)->unit_name ?? '-' }}</strong>
                berdasarkan periode yang dipilih.
            </p>
        </div>
        <div></div>
    </div>

    {{-- FILTER PANEL --}}
    <div class="panel-card">
        <h6 class="panel-title mb-1">Filter Laporan</h6>
        <p class="panel-subtitle mb-3">
            Tentukan periode penerimaan barang untuk unit Anda.
        </p>

        <form method="GET" action="{{ route('reports.unit_received') }}">
            <div class="request-filters">

                {{-- PERIODE --}}
                <div class="filter-group">
                    <label class="filter-label">Periode</label>
                    <div class="d-flex align-items-center gap-1">
                        <input type="date"
                               name="date_from"
                               id="date_from"
                               value="{{ $dateFrom }}"
                               class="form-control form-control-sm filter-input">
                        <span class="mx-1 meta-small">s/d</span>
                        <input type="date"
                               name="date_to"
                               id="date_to"
                               value="{{ $dateTo }}"
                               class="form-control form-control-sm filter-input">
                    </div>
                </div>

                {{-- AKSI --}}
                <div class="filter-group">
                    <label class="filter-label">&nbsp;</label>
                    <div class="filter-actions">

                        {{-- TAMPILKAN --}}
                        <button type="submit" class="btn btn-primary-gradient">
                            <i class="ri-search-line"></i>
                            Tampilkan
                        </button>

                        {{-- CETAK PDF --}}
                        <a href="{{ route('reports.unit_received.print', [
                                'date_from' => $dateFrom,
                                'date_to'   => $dateTo,
                            ]) }}"
                           class="btn-danger-soft"
                           target="_blank">
                            <i class="ri-file-pdf-line"></i>
                            Cetak PDF
                        </a>

                        {{-- RESET FILTER --}}
                        @if($dateFrom || $dateTo)
                            <button type="button"
                                    onclick="window.location='{{ route('reports.unit_received') }}'"
                                    class="btn-reset-filter">
                                <i class="ri-refresh-line"></i>
                                Reset
                            </button>
                        @endif

                    </div>
                </div>

            </div>
        </form>

        {{-- INFO UNIT --}}
        <div class="mt-1 meta-small">
            Unit aktif: <strong>{{ optional($user->unit)->unit_name ?? '-' }}</strong>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="panel-card">
        <div class="d-flex justify-content-between mb-2">
            <div>
                <h6 class="panel-title mb-0">Data Barang Diterima Unit</h6>
                <p class="panel-subtitle mb-0">
                    Menampilkan daftar barang yang diterima oleh unit sesuai periode filter.
                </p>
            </div>
            <div class="text-end d-none d-md-block">
                <span class="mini-chip">
                    Periode:
                    {{ $dateFrom ? \Illuminate\Support\Carbon::parse($dateFrom)->format('d-m-Y') : 'awal' }}
                    s/d
                    {{ $dateTo ? \Illuminate\Support\Carbon::parse($dateTo)->format('d-m-Y') : 'akhir' }}
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-requests align-middle mb-0">
                <thead>
                    <tr class="text-nowrap">
                        <th style="width:40px;">No</th>
                        <th style="width:90px;">Tgl</th>
                        <th style="width:120px;">No. Dok</th>
                        <th>Item</th>
                        <th style="width:130px;">Merek</th>
                        <th style="width:110px;">Lot</th>
                        <th style="width:70px;" class="text-center">Sat</th>
                        <th style="width:90px;" class="text-end">Qty</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $i => $t)
                    @php
                        $item = optional($t->variant->itemMaster);
                    @endphp
                    <tr>
                        <td class="meta-small text-center">{{ $i + 1 }}</td>
                        <td class="meta-small text-center">
                            {{ $t->trans_date ? \Illuminate\Support\Carbon::parse($t->trans_date)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="meta-small">{{ $t->doc_no ?: '-' }}</td>
                        <td>
                            <div><strong>{{ $item->item_code ?? '-' }}</strong></div>
                            <div class="meta-small">{{ $item->item_name ?? '-' }}</div>
                        </td>
                        <td class="meta-small">{{ $t->brand ?? $t->variant->brand ?? '-' }}</td>
                        <td class="meta-small">{{ $t->lot ?? $t->lot_number ?? $t->variant->lot_number ?? '-' }}</td>
                        <td class="meta-small text-center">{{ $item->base_unit ?? '-' }}</td>
                        <td class="text-end">
                            <strong>{{ number_format($t->quantity ?? 0, 0, ',', '.') }}</strong>
                        </td>
                        <td class="meta-small">{{ $t->note ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            Tidak ada data pada periode ini.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
