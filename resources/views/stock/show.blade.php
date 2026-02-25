@extends('layouts.app')
@section('title','Kartu Stok Varian')

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

    .badge-stock{
        border-radius:999px;
        padding:.25rem .8rem;
        font-size:12px;
        background:linear-gradient(90deg,#4f46e5,#06b6d4);
        color:#fff;
        box-shadow:0 10px 25px rgba(15,23,42,.25);
    }

    .form-inline-flex{
        display:flex;
        flex-wrap:wrap;
        gap:.5rem .75rem;
        align-items:flex-end;
    }

    .card-kartu{
        border-radius:16px;
        border:1px solid #e5e7eb;
        padding:16px 18px 18px;
    }

    /* Tombol modern (kembali / simpan / recompute) */
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
    .btn-pill-primary{
        background:linear-gradient(90deg,#4f46e5,#06b6d4);
        color:#fff;
        box-shadow:0 10px 25px rgba(15,23,42,.25);
    }
    .btn-pill-primary:hover{
        background:linear-gradient(90deg,#6054ff,#13c0df);
        color:#fff;
        box-shadow:0 14px 30px rgba(15,23,42,.28);
    }
    .btn-pill-primary:active{
        background:linear-gradient(90deg,#3d38ca,#0a8ab0);
        box-shadow:0 6px 15px rgba(15,23,42,.22);
    }

    .btn-pill-outline{
        background:#f9fafb;
        color:#4b5563;
        border-color:#d1d5db;
    }
    .btn-pill-outline:hover{
        background:#e5e7eb;
        color:#111827;
    }
    .btn-pill-outline:active{
        background:#d1d5db;
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

    /* Badge FEFO batch */
    .badge-fefo{
        display:inline-flex;
        align-items:center;
        font-size:10px;
        font-weight:600;
        border-radius:999px;
        padding:.20rem .65rem;
        border:1px solid transparent;
    }
    .badge-fefo.badge-danger{
        background:#fee2e2;
        color:#b91c1c;
        border-color:#fecaca;
    }
    .badge-fefo.badge-warning{
        background:#fef3c7;
        color:#b45309;
        border-color:#fde68a;
    }
    .badge-fefo.badge-success{
        background:#dcfce7;
        color:#166534;
        border-color:#bbf7d0;
    }
    .badge-fefo.badge-secondary{
        background:#e5e7eb;
        color:#4b5563;
        border-color:#d1d5db;
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
            <h1 class="page-title">Kartu Stok Varian</h1>
            <p class="page-subtitle">
                Set / sesuaikan stok untuk <strong>brand &amp; lot spesifik</strong>, sekaligus
                melihat histori keluar–masuk.
            </p>
        </div>
        <div class="text-end">
            <div class="small text-muted mb-1">Stok Saat Ini</div>
            <span class="badge-stock">
                {{ (int)($stock->current_quantity ?? 0) }} {{ $variant->itemMaster->base_unit }}
            </span>
        </div>
    </div>

    {{-- PANEL INFO + FORM ADJUST --}}
    <div class="panel-card">
        <div class="mb-3">
            <strong>{{ $variant->itemMaster->item_code }}</strong> —
            {{ $variant->itemMaster->item_name }}
            <div class="text-muted small mt-1">
                Satuan: {{ $variant->itemMaster->base_unit }}
                @if($variant->brand)
                    · Merek: {{ $variant->brand }}
                @endif
                @if($variant->lot_number || $variant->expiration_date)
                    · Lot/Exp:
                    {{ $variant->lot_number ?: '—' }} /
                    {{ $variant->expiration_date ? $variant->expiration_date->format('d-m-Y') : '—' }}
                @endif
            </div>
        </div>

        {{-- FORM ADJUST / RECOMPUTE --}}
        <form action="{{ route('stock.seed', $variant->id) }}"
              method="post"
              class="form-inline-flex">
            @csrf

            <!-- <div>
                <label class="form-label small mb-1">Set / Adjust kuantitas saat ini</label>
                <input type="number"
                       min="0"
                       step="1"
                       name="current_quantity"
                       value="{{ old('current_quantity', (int)($stock->current_quantity ?? 0)) }}"
                       class="form-control form-control-sm @error('current_quantity') is-invalid @enderror">
                @error('current_quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <button class="btn-pill btn-pill-primary mt-3">
                    <i class="ri-save-3-line"></i>
                    <span>Simpan / Upsert</span>
                </button>
            </div> -->

            <div>
                <button formaction="{{ route('stock.recompute', $variant->id) }}"
                        formmethod="post"
                        class="btn-pill btn-pill-outline mt-3">
                    @csrf
                    <i class="ri-loop-right-line"></i>
                    <span>Recompute dari transaksi</span>
                </button>
            </div>

            <div class="ms-auto">
                <a href="{{ route('stock.index') }}"
                   class="btn-pill btn-pill-ghost mt-3">
                    <i class="ri-arrow-left-line"></i>
                    <span>Kembali ke stok</span>
                </a>
            </div>
        </form>
    </div>

    {{-- KARTU STOK VARIAN --}}
    <div class="panel-card">
        <div class="card-kartu">

            <div class="text-center mb-2">
                <strong style="font-size:1.05rem;">KARTU STOK BARANG (VARIAN)</strong>
            </div>

            <div class="row mb-2 small">
                <div class="col-md-8">
                    <div><strong>Nama Barang</strong> : {{ $variant->itemMaster->item_name }}</div>
                    <div><strong>Kode Barang</strong> : {{ $variant->itemMaster->item_code }}</div>
                </div>
                <div class="col-md-4">
                    <div><strong>Satuan</strong> : {{ $variant->itemMaster->base_unit }}</div>
                    @if($variant->brand)
                        <div><strong>Merek</strong> : {{ $variant->brand }}</div>
                    @endif
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
                            $rowBrand = $t->brand ?? $variant->brand ?? '—';
                        @endphp
                        <tr>
                            <td class="text-center align-middle">{{ $i + 1 }}</td>
                            <td class="text-center align-middle">
                                {{ \Carbon\Carbon::parse($t->trans_date)->format('d-m-Y') }}
                            </td>
                            <td class="align-middle">{{ $rowBrand }}</td>
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
                                Belum ada transaksi untuk varian ini.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    {{-- PANEL: DAFTAR BATCH VARIAN INI --}}
    @if(isset($batches) && $batches->count())
        <div class="panel-card">
            <h6 class="mb-1 fw-semibold">Daftar Batch Varian Ini</h6>
            <p class="page-subtitle mb-2">
                Per lot &amp; tanggal kedaluwarsa, lengkap dengan label FEFO dan total masuk/keluar dari transaksi.
            </p>

            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr class="text-center">
                            <th style="width:40px;">No</th>
                            <th style="width:140px;">Lot</th>
                            <th style="width:130px;">Kedaluwarsa</th>
                            <th style="width:140px;">FEFO</th>
                            <th style="width:80px;">Masuk</th>
                            <th style="width:80px;">Keluar</th>
                            <th style="width:80px;">Sisa</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($batches as $i => $b)
                        @php
                            $qtyIn  = (int) $b->quantity_in;
                            $qtyOut = (int) $b->quantity_out;
                            $qtyCur = (int) $b->current_quantity;
                        @endphp
                        <tr class="align-middle">
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ $b->lot_number ?: '—' }}</td>
                            <td class="text-center">
                                {{ $b->expiration_date ? $b->expiration_date->format('d-m-Y') : '—' }}
                            </td>
                            <td class="text-center">
                                @if($b->expiration_date)
                                    <span class="badge-fefo badge-{{ $b->fefo_badge_class }}">
                                        {{ $b->fefo_label }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ $qtyIn ? number_format($qtyIn,0,',','.') : '0' }}
                            </td>
                            <td class="text-end">
                                {{ $qtyOut ? number_format($qtyOut,0,',','.') : '0' }}
                            </td>
                            <td class="text-end">
                                {{ number_format($qtyCur,0,',','.') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
