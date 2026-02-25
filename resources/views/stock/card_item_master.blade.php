@extends('layouts.app')
@section('title','Kartu Stok Barang')

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
        font-size:20px;
        font-weight:700;
        margin:0;
    }
    .page-subtitle{
        font-size:12px;
        color:var(--text-muted);
        margin:2px 0 0;
    }

    .panel-card{
        background:#ffffff;
        border-radius:18px;
        border:1px solid #e5e7eb;
        padding:16px 18px 18px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
        margin-bottom:14px;
    }

    .badge-stock-total{
        border-radius:999px;
        padding:.25rem .8rem;
        font-size:12px;
        background:linear-gradient(90deg,#0ea5e9,#22c55e);
        color:#fff;
        box-shadow:0 10px 25px rgba(15,23,42,.25);
    }

    .card-kartu{
        border-radius:16px;
        border:1px solid #e5e7eb;
        padding:16px 18px 18px;
    }

    /* Tombol kembali modern */
    .btn-pill{
        border-radius:999px;
        font-size:12px;
        padding:.45rem 1.1rem;
        display:inline-flex;
        align-items:center;
        gap:6px;
        border:1px solid transparent;
        transition:all .18s ease-in-out;
    }
    .btn-pill i{
        font-size:16px;
    }
    .btn-pill-ghost{
        background:#ffffff;
        color:#4b5563;
        border-color:#e5e7eb;
    }
    .btn-pill-ghost:hover{
        background:#f3f4f6;
        color:#111827;
    }
    .btn-pill-ghost:active{
        background:#e5e7eb;
    }

    @media (max-width: 767.98px){
        .page-title-wrap{
            flex-direction:column;
            align-items:flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Kartu Stok Barang</h1>
            <p class="page-subtitle">
                Rekap <strong>stok per nama barang</strong> (semua merek &amp; varian digabung).
            </p>
        </div>
        <div class="text-end">
            <div class="small text-muted mb-1">Stok Saat Ini (total semua merek)</div>
            <span class="badge-stock-total">
                {{ (int)$currentStock }} {{ $item->base_unit }}
            </span>
        </div>
    </div>

    {{-- INFO BARANG + TOMBOL KEMBALI --}}
    <div class="panel-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <strong>{{ $item->item_code }}</strong> — {{ $item->item_name }}
                <div class="text-muted small mt-1">
                    Satuan: {{ $item->base_unit }}
                </div>
            </div>

            <a href="{{ route('stock.index') }}"
               class="btn-pill btn-pill-ghost mt-2 mt-md-0">
                <i class="ri-arrow-left-line"></i>
                <span>Kembali ke stok</span>
            </a>
        </div>
    </div>

    {{-- KARTU STOK PER ITEM --}}
    <div class="panel-card">
        <div class="card-kartu">

            <div class="text-center mb-2">
                <strong style="font-size:1.05rem;">KARTU STOK BARANG</strong>
            </div>

            <div class="row mb-2 small">
                <div class="col-md-8">
                    <div><strong>Nama Barang</strong> : {{ $item->item_name }}</div>
                    <div><strong>Kode Barang</strong> : {{ $item->item_code }}</div>
                </div>
                <div class="col-md-4">
                    <div><strong>Satuan</strong> : {{ $item->base_unit }}</div>
                    <div><strong>Stok Saat Ini</strong> : {{ (int)$currentStock }}</div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr class="text-center">
                            <th style="width:40px;">No</th>
                            <th style="width:85px;">Tanggal</th>
                            <th>Merek</th>
                            <th style="width:80px;">Masuk</th>
                            <th style="width:80px;">Keluar</th>
                            <th style="width:80px;">Sisa</th>
                            <th style="width:80px;">Paraf</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($transactions as $i => $t)
                        @php
                            $brand = $t->brand_display ?? $t->brand ?? ($t->variant->brand ?? '—');
                        @endphp
                        <tr>
                            <td class="text-center align-middle">{{ $i + 1 }}</td>
                            <td class="text-center align-middle">
                                {{ \Carbon\Carbon::parse($t->trans_date)->format('d-m-Y') }}
                            </td>
                            <td class="align-middle">{{ $brand }}</td>
                            <td class="text-end align-middle">
                                {{ $t->in_qty ? number_format($t->in_qty,0,',','.') : '' }}
                            </td>
                            <td class="text-end align-middle">
                                {{ $t->out_qty ? number_format($t->out_qty,0,',','.') : '' }}
                            </td>
                            <td class="text-end align-middle">
                                {{ number_format($t->balance,0,',','.') }}
                            </td>
                            <td class="text-center align-middle">&nbsp;</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted p-3">
                                Belum ada transaksi untuk barang ini.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection
