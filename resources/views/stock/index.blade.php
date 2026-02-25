@extends('layouts.app')
@section('title','Stok Barang')

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

    .panel-card{
        background:#ffffff;
        border-radius:18px;
        border:1px solid #e5e7eb;
        padding:16px 18px 18px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
        margin-bottom:14px;
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

    .badge-fefo{
        display:inline-flex;
        align-items:center;
        font-size:10px;
        font-weight:600;
        border-radius:999px;
        padding:.20rem .65rem;
        border:1px solid transparent;
    }
    /* Warna FEFO sesuai status */
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

    .table-stock{
        font-size:13px;
        border-collapse:separate;
        border-spacing:0;
    }
    .table-stock thead th{
        background:#f9fafb;
        border-top:none;
        border-bottom:1px solid #e5e7eb;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--text-muted);
    }
    .table-stock tbody tr:hover{
        background:#f9fafb;
    }

    .item-meta{
        font-size:11px;
        color:var(--text-muted);
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
    .btn-aksi i{
        font-size:15px;
    }
    .btn-aksi-card{
        background:rgba(59,130,246,.08);
        color:#1d4ed8;
        border-color:rgba(59,130,246,.45);
    }
    .btn-aksi-card:hover{
        background:rgba(59,130,246,.18);
    }
    .btn-aksi-stock{
        background:rgba(34,197,94,.08);
        color:#15803d;
        border-color:rgba(34,197,94,.45);
    }
    .btn-aksi-stock:hover{
        background:rgba(34,197,94,.18);
    }

    .btn-reset-filter{
        border-radius:999px;
        padding:.25rem .8rem;
        font-size:11px;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        white-space:nowrap;
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
            <h1 class="page-title">Stok Barang</h1>
            <p class="page-subtitle">
                Monitoring <strong>stok terkini</strong> per varian (brand, lot, expired).
            </p>
        </div>
    </div>

    {{-- FILTER & SEARCH --}}
    <div class="panel-card">
        @php
            $hasFilter = !empty($s ?? '') || !empty($fefo ?? '');
        @endphp

        <form method="get" class="mb-1">
            <div class="filter-bar">
                <div style="min-width:190px;">
                    <label class="form-label small mb-1">Status FEFO</label>
                    <select name="fefo"
                            id="fefo"
                            class="form-select form-select-sm"
                            onchange="this.form.submit()">
                        <option value="">Semua status</option>
                        <option value="expired" {{ ($fefo ?? null) === 'expired' ? 'selected' : '' }}>
                            EXPIRED
                        </option>
                        <option value="merah" {{ ($fefo ?? null) === 'merah' ? 'selected' : '' }}>
                            Merah (&lt; 3 bln)
                        </option>
                        <option value="kuning" {{ ($fefo ?? null) === 'kuning' ? 'selected' : '' }}>
                            Kuning (3–12 bln)
                        </option>
                        <option value="hijau" {{ ($fefo ?? null) === 'hijau' ? 'selected' : '' }}>
                            Hijau (&gt; 12 bln)
                        </option>
                    </select>
                </div>

                <div style="min-width:240px; max-width:320px;">
                    <label class="form-label small mb-1">Pencarian</label>
                    <div class="d-flex">
                        <div class="input-group input-group-sm">
                            <input type="text"
                                   name="q"
                                   value="{{ $s ?? '' }}"
                                   class="form-control"
                                   placeholder="Cari kode / nama / brand / lot...">
                            <button class="btn btn-outline-primary">
                                <i class="ri-search-line"></i>
                            </button>
                        </div>

                        {{-- Reset selalu tampil supaya ukuran tidak berubah --}}
                        <a href="{{ route('stock.index') }}"
                           class="btn btn-sm btn-outline-secondary ms-2 btn-reset-filter {{ $hasFilter ? '' : 'disabled opacity-50' }}"
                           @if(!$hasFilter) tabindex="-1" aria-disabled="true" @endif>
                            <i class="ri-refresh-line me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TABEL STOK --}}
    <div class="panel-card">
        <div class="table-responsive">
            <table class="table table-stock align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:260px;">Item</th>
                        <th style="width:140px;">Merek</th>
                        <th style="width:130px;">Lot</th>
                        <th style="width:130px;">Kedaluwarsa</th>
                        <th style="width:130px;" class="text-center">Label FEFO</th>
                        <th style="width:110px;" class="text-center">Jml Batch</th> {{-- 🔹 kolom baru --}}
                        <th style="width:100px;" class="text-center">Qty</th>
                        <th style="width:180px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($variants as $v)
                    @php
                        $qty        = (int) (optional($v->stock)->current_quantity ?? 0);
                        $batchCount = (int) ($v->batches_count ?? 0);
                    @endphp
                    <tr>
                        {{-- ITEM --}}
                        <td>
                            <strong>{{ $v->itemMaster->item_code }}</strong>
                            <div class="item-meta">{{ $v->itemMaster->item_name }}</div>
                            <div class="item-meta">
                                Satuan: {{ $v->itemMaster->base_unit }}
                            </div>
                        </td>

                        {{-- BRAND --}}
                        <td>{{ $v->brand }}</td>

                        {{-- LOT --}}
                        <td>{{ $v->lot_number ?: '—' }}</td>

                        {{-- EXP --}}
                        <td>
                            {{ $v->expiration_date ? $v->expiration_date->format('d-m-Y') : '—' }}
                        </td>

                        {{-- LABEL FEFO --}}
                        <td class="text-center">
                            @if($v->expiration_date)
                                <span class="badge-fefo badge-{{ $v->fefo_badge_class }}">
                                    {{ $v->fefo_label_text }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- JML BATCH --}}
                        <td class="text-center">
                            @if($batchCount > 0)
                                <a href="{{ route('batches.index', ['variant_id' => $v->id]) }}"
                                   class="text-decoration-none">
                                    {{ $batchCount }} batch
                                </a>
                            @else
                                <span class="text-muted">0</span>
                            @endif
                        </td>

                        {{-- STOK --}}
                        <td class="text-center">
                            <span class="badge {{ $qty > 0 ? 'bg-success' : 'bg-secondary' }} text-white">
                                {{ $qty }}
                            </span>
                        </td>

                        {{-- AKSI --}}
                        <td class="text-end">
                            <div class="aksi-gap">
                                {{-- Kartu stok per item (semua merek) --}}
                                <a href="{{ route('stock.card-item', $v->itemMaster->id) }}"
                                   class="btn-aksi btn-aksi--sq btn-aksi-card"
                                   title="Kartu Stok Barang">
                                    <i class="ri-clipboard-line"></i>
                                </a>

                                {{-- Stok varian (set / recompute) --}}
                                <a href="{{ route('stock.show', $v) }}"
                                   class="btn-aksi btn-aksi--sq btn-aksi-stock"
                                   title="Kartu Stok Variant">
                                    <i class="nav-icon ri-database-2-line"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Belum ada data stok.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($variants->hasPages())
            <div class="pagination-wrapper">
                {{ $variants->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
