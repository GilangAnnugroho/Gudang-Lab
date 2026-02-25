@extends('layouts.app')
@section('title','Transaksi Barang')

@php
  $role    = strtolower(optional(auth()->user()->role)->role_name ?? '');
  $canCrud = in_array($role, ['super admin','admin gudang']);
@endphp

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

    .btn-add-item{
        border-radius:999px;
        padding:.5rem 1.2rem;
        font-size:13px;
        display:inline-flex;
        align-items:center;
        gap:6px;
        background:linear-gradient(90deg,#4f46e5,#06b6d4);
        border:none;
        color:#fff;
        box-shadow:0 10px 25px rgba(15,23,42,.25);
        transition:all .18s ease-in-out;
    }
    .btn-add-item i{
        font-size:18px;
    }
    .btn-add-item:hover{
        background:linear-gradient(90deg,#6054ff,#13c0df);
        box-shadow:0 14px 30px rgba(15,23,42,.28);
    }
    .btn-add-item:active{
        background:linear-gradient(90deg,#3d38ca,#0a8ab0);
        box-shadow:0 6px 15px rgba(15,23,42,.22);
    }

    .panel-card{
        background:#ffffff;
        border-radius:18px;
        border:1px solid #e5e7eb;
        padding:16px 18px 18px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
    }

    .filter-bar{
        display:flex;
        flex-wrap:wrap;
        gap:.5rem;
        align-items:flex-end;
    }
    .filter-bar .form-control,
    .filter-bar .form-select{
        font-size:12px;
    }

    .badge-summary{
        display:inline-flex;
        align-items:center;
        font-size:11px;
        border-radius:999px;
        padding:.25rem .7rem;
        background:#f3f4ff;
        color:#4338ca;
        margin-right:.25rem;
    }

    .table-trans{
        font-size:13px;
        border-collapse:separate;
        border-spacing:0;
    }
    .table-trans thead th{
        background:#f9fafb;
        border-top:none;
        border-bottom:1px solid #e5e7eb;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--text-muted);
        white-space:nowrap;
    }
    .table-trans tbody tr:hover{
        background:#f9fafb;
    }

    .badge-type{
        border-radius:999px;
        padding:.2rem .6rem;
        font-size:11px;
        font-weight:600;
    }

    .aksi-gap{
        display:flex;
        justify-content:flex-end;
        gap:.35rem;
    }
    .btn-aksi{
        border-radius:999px;
        padding:.25rem .6rem;
        font-size:11px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        border:1px solid transparent;
    }
    .btn-aksi--sq{
        width:30px;
        height:30px;
    }
    .btn-aksi-view{
        background:rgba(59,130,246,.08);
        color:#1d4ed8;
        border-color:rgba(59,130,246,.45);
    }
    .btn-aksi-view:hover{
        background:rgba(59,130,246,.18);
    }
    .btn-aksi-edit{
        background:rgba(234,179,8,.08);
        color:#ca8a04;
        border-color:rgba(234,179,8,.45);
    }
    .btn-aksi-edit:hover{
        background:rgba(234,179,8,.18);
    }
    .btn-aksi-delete{
        background:rgba(248,113,113,.08);
        color:#dc2626;
        border-color:rgba(248,113,113,.45);
    }
    .btn-aksi-delete:hover{
        background:rgba(248,113,113,.18);
    }
    .btn-aksi-delete[disabled]{
        opacity:.55;
        cursor:not-allowed;
    }

    .pagination-wrapper{
        display:flex;
        justify-content:flex-end;
        align-items:center;
        margin-top:10px;
    }

    @media (max-width: 767.98px){
        .page-title-wrap{
            flex-direction:column;
            align-items:flex-start;
        }
        .btn-add-item{
            width:100%;
            justify-content:center;
        }
        .pagination-wrapper{
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
            <h1 class="page-title">Transaksi Barang</h1>
            <p class="page-subtitle">
                Riwayat <strong>barang masuk & keluar</strong> dengan urutan kolom mengikuti buku pencatatan barang masuk.
            </p>
        </div>

        @if($canCrud)
            <a href="{{ route('transactions.create') }}" class="btn-add-item">
                <i class="ri-add-circle-line"></i>
                <span>Tambah Transaksi</span>
            </a>
        @endif
    </div>

    <div class="panel-card mb-3">
        {{-- FILTER BAR --}}
        <form method="get" class="mb-2">
            <div class="filter-bar">
                <div style="min-width:260px; flex:1 1 260px;">
                    <label class="form-label small mb-1">Varian</label>
                    <select name="variant_id" class="form-select form-select-sm">
                        <option value="">Semua Varian</option>
                        @foreach($variants as $id => $label)
                            <option value="{{ $id }}" {{ (string)$variantId === (string)$id ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="min-width:140px;">
                    <label class="form-label small mb-1">Jenis</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="MASUK"  {{ ($type ?? '')==='MASUK'  ? 'selected':'' }}>Masuk</option>
                        <option value="KELUAR" {{ ($type ?? '')==='KELUAR' ? 'selected':'' }}>Keluar</option>
                    </select>
                </div>

                <div style="min-width:150px;">
                    <label class="form-label small mb-1">ID Permintaan</label>
                    <input type="text"
                           name="request_id"
                           value="{{ $requestId }}"
                           class="form-control form-control-sm"
                           placeholder="misal: 12">
                </div>

                <div>
                    <label class="form-label small mb-1">Dari</label>
                    <input type="date"
                           name="date_from"
                           value="{{ $dateFrom }}"
                           class="form-control form-control-sm">
                </div>
                <div>
                    <label class="form-label small mb-1">Sampai</label>
                    <input type="date"
                           name="date_to"
                           value="{{ $dateTo }}"
                           class="form-control form-control-sm">
                </div>

                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-primary mt-1">
                        <i class="ri-filter-3-line me-1"></i> Filter
                    </button>
                    <a href="{{ route('transactions.index') }}"
                       class="btn btn-sm btn-outline-secondary mt-1">
                        <i class="ri-refresh-line me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- RINGKASAN --}}
        <div class="mt-1">
            <span class="badge-summary">
                Masuk: <strong class="ms-1">{{ (int)$sumMasuk }}</strong>
            </span>
            <span class="badge-summary">
                Keluar: <strong class="ms-1">{{ (int)$sumKeluar }}</strong>
            </span>
            <span class="badge-summary">
                Net: <strong class="ms-1">{{ (int)$sumMasuk - (int)$sumKeluar }}</strong>
            </span>
        </div>
    </div>

    {{-- TABEL TRANSAKSI --}}
    <div class="panel-card">
        <div class="table-responsive">
            @php
                $rowNumberStart = ($transactions->currentPage() - 1) * $transactions->perPage();
            @endphp
            <table class="table table-trans align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th style="width:80px;">Jenis</th>
                        <th style="width:110px;">Tanggal</th>
                        <th>Nama Barang</th>
                        <th class="text-end" style="width:80px;">Jumlah</th>
                        <th style="width:80px;">Satuan</th>
                        <th style="width:130px;">Merek</th>
                        <th style="width:110px;">No. LOT</th>
                        <th style="width:120px;">Tgl. Kedaluarsa</th>
                        <th style="width:130px;">Ukuran Barang</th>
                        <th style="width:140px;">Suhu Barang</th>
                        <th style="width:180px;">Nama Rekanan / Unit</th>
                        <th style="width:120px;">No. Faktur</th>
                        <th class="text-end" style="width:110px;">Harga</th>
                        <th class="text-end" style="width:90px;">Pajak</th>
                        <th class="text-end" style="width:110px;">Total</th>
                        <th style="width:120px;">Ket. Pembayaran</th>
                        @if($canCrud)
                            <th style="width:150px;" class="text-end">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                @forelse($transactions as $index => $t)
                    @php
                        $rowNo      = $rowNumberStart + $index + 1;
                        $itemMaster = optional($t->variant)->itemMaster;
                        $itemName   = $t->item_name ?? ($itemMaster->item_name ?? '-');
                        $baseUnit   = $t->base_unit ?? ($itemMaster->base_unit ?? '—');

                        // LOT: dari transaksi dulu, fallback ke varian
                        $lotDisplay = $t->lot
                            ?? $t->lot_number
                            ?? optional($t->variant)->lot_number;

                        if (!$lotDisplay || trim($lotDisplay) === '' || in_array(trim($lotDisplay), ['—','-'], true)) {
                            $lotDisplay = '—';
                        }

                        // EXP: bisa string "—" dari accessor → jangan langsung parse
                        $expSourceRaw = $t->exp
                            ?? $t->expiration_date
                            ?? optional($t->variant)->expiration_date;

                        $expDisplay = '—';

                        if ($expSourceRaw instanceof \Carbon\Carbon) {
                            $expDisplay = $expSourceRaw->format('d-m-Y');
                        } elseif (!is_null($expSourceRaw)) {
                            $expSourceStr = trim((string)$expSourceRaw);

                            if ($expSourceStr !== '' && !in_array($expSourceStr, ['—','-'], true)) {
                                try {
                                    $expDisplay = \Carbon\Carbon::parse($expSourceStr)->format('d-m-Y');
                                } catch (\Exception $e) {
                                    // Kalau gagal parse (format aneh), tampilkan mentahnya
                                    $expDisplay = $expSourceStr;
                                }
                            }
                        }

                        // Tanggal transaksi juga kita amankan
                        $transDateRaw = $t->trans_date;
                        $transDate    = '—';

                        if ($transDateRaw instanceof \Carbon\Carbon) {
                            $transDate = $transDateRaw->format('d-m-Y');
                        } elseif (!is_null($transDateRaw)) {
                            $transDateStr = trim((string)$transDateRaw);

                            if ($transDateStr !== '' && !in_array($transDateStr, ['—','-'], true)) {
                                try {
                                    $transDate = \Carbon\Carbon::parse($transDateStr)->format('d-m-Y');
                                } catch (\Exception $e) {
                                    $transDate = $transDateStr;
                                }
                            }
                        }
                    @endphp
                    <tr>
                        {{-- No --}}
                        <td>{{ $rowNo }}</td>

                        {{-- Jenis --}}
                        <td>
                            <span class="badge-type {{ $t->type === 'MASUK' ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                {{ $t->type }}
                            </span>
                        </td>

                        {{-- Tanggal --}}
                        <td>{{ $transDate }}</td>

                        {{-- Nama Barang (hanya nama) --}}
                        <td>
                            <div>{{ $itemName }}</div>
                        </td>

                        {{-- Jumlah & Satuan --}}
                        <td class="text-end">
                            {{ number_format($t->quantity,0,',','.') }}
                        </td>
                        <td>{{ $baseUnit }}</td>

                        {{-- Merek --}}
                        <td>{{ $t->brand ?: '—' }}</td>

                        {{-- LOT & Exp --}}
                        <td>{{ $lotDisplay }}</td>
                        <td>{{ $expDisplay }}</td>

                        {{-- Ukuran Barang --}}
                        <td>{{ $t->package_size ?? '—' }}</td>

                        {{-- Suhu Barang --}}
                        <td>{{ $t->storage_condition ?: '—' }}</td>

                        {{-- Rekanan / Unit --}}
                        <td>
                            @if($t->type === 'MASUK')
                                {{ $t->supplier_name ?? optional($t->supplier)->supplier_name ?? '—' }}
                            @else
                                {{ $t->unit_name ?? optional($t->unit)->unit_name ?? '—' }}
                            @endif
                        </td>

                        {{-- Nomor Faktur --}}
                        <td>{{ $t->invoice_no ?: '—' }}</td>

                        {{-- Harga, Pajak, Total --}}
                        <td class="text-end">
                            {{ $t->price !== null ? number_format($t->price,2,',','.') : '—' }}
                        </td>
                        <td class="text-end">
                            {{ $t->tax_amount !== null ? number_format($t->tax_amount,2,',','.') : '—' }}
                        </td>
                        <td class="text-end">
                            {{ $t->total_amount !== null ? number_format($t->total_amount,2,',','.') : '—' }}
                        </td>

                        {{-- Ket. Pembayaran --}}
                        <td>{{ $t->payment_status ?: '—' }}</td>

                        {{-- Aksi --}}
                        @if($canCrud)
                            <td class="align-middle">
                                <div class="aksi-gap">
                                    {{-- Detail --}}
                                    <a href="{{ route('transactions.show', $t) }}"
                                       class="btn-aksi btn-aksi--sq btn-aksi-view"
                                       title="Lihat detail">
                                        <i class="ri-eye-line"></i>
                                    </a>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $canCrud ? 19 : 18 }}" class="text-center text-muted py-4">
                            Belum ada transaksi yang tercatat.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($transactions->hasPages())
            <div class="pagination-wrapper">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
