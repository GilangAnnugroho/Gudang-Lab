@extends('layouts.app')
@section('title','Item Variant')

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
    }
    .btn-add-item i{
        font-size:18px;
    }

    .item-filters{
        display:flex;
        flex-wrap:wrap;
        gap:.65rem;
        margin-bottom:12px;
    }
    .item-search-box{
        position:relative;
        flex:1 1 220px;
    }
    .item-search-box input{
        width:100%;
        border-radius:999px;
        padding:.55rem 2.4rem .55rem 2.2rem;
        font-size:13px;
        border:1px solid #e5e7eb;
        box-shadow:0 6px 18px rgba(15,23,42,.06);
    }
    .item-search-box i{
        position:absolute;
        left:12px;
        top:50%;
        transform:translateY(-50%);
        font-size:18px;
        color:var(--text-muted);
    }
    .item-search-badge{
        position:absolute;
        right:12px;
        top:50%;
        transform:translateY(-50%);
        font-size:11px;
        color:var(--text-muted);
        display:flex;
        align-items:center;
        gap:4px;
    }

    .item-filter-select{
        min-width:220px;
        border-radius:999px;
        font-size:13px;
        padding:.45rem 1rem;
        border:1px solid #e5e7eb;
        box-shadow:0 6px 18px rgba(15,23,42,.04);
    }

    .btn-reset-filter{
        border-radius:999px;
        font-size:12px;
        padding:.4rem .9rem;
    }

    .table-items{
        font-size:13px;
        border-collapse:separate;
        border-spacing:0;
    }
    .table-items thead th{
        border-top:none;
        border-bottom:1px solid #e5e7eb;
        background:#f9fafb;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--text-muted);
    }
    .table-items tbody tr:last-child td{
        border-bottom:0;
    }
    .table-items tbody tr:hover{
        background:#f9fafb;
    }

    .badge-kode{
        display:inline-flex;
        align-items:center;
        padding:.2rem .55rem;
        border-radius:999px;
        font-size:11px;
        font-weight:600;
        background:rgba(79,70,229,.08);
        color:#4f46e5;
    }

    .item-name{
        font-weight:600;
        font-size:13px;
    }
    .item-meta{
        font-size:11px;
        color:var(--text-muted);
    }

    .badge-fefo{
        display:inline-flex;
        align-items:center;
        padding:.2rem .55rem;
        border-radius:999px;
        font-size:11px;
        font-weight:500;
    }
    .badge-fefo--good{
        background:#ecfdf3;
        color:#16a34a;
    }
    .badge-fefo--warning{
        background:#fef3c7;
        color:#d97706;
    }
    .badge-fefo--danger{
        background:#fee2e2;
        color:#b91c1c;
    }

    .badge-stock{
        display:inline-flex;
        align-items:center;
        padding:.2rem .6rem;
        border-radius:999px;
        font-size:11px;
        font-weight:600;
    }
    .badge-stock--ok{
        background:#ecfdf3;
        color:#16a34a;
    }
    .badge-stock--empty{
        background:#e5e7eb;
        color:#4b5563;
    }

    /* 🔹 Badge jumlah batch */
    .badge-batch{
        display:inline-flex;
        align-items:center;
        padding:.2rem .6rem;
        border-radius:999px;
        font-size:11px;
        font-weight:500;
        background:rgba(129,140,248,.08);
        color:#4f46e5;
        border:1px solid rgba(129,140,248,.5);
        text-decoration:none;
    }
    .badge-batch:hover{
        background:rgba(129,140,248,.16);
        color:#4338ca;
        text-decoration:none;
    }

    .aksi-gap{
        display:flex;
        justify-content:flex-end;
        gap:.35rem;
    }

    .btn-aksi-warehouse{
        background:rgba(59,130,246,.08);
        color:#1d4ed8;
        border:1px solid rgba(59,130,246,.4);
    }
    .btn-aksi-warehouse:hover{
        background:rgba(59,130,246,.16);
    }
    .btn-aksi-card{
        background:rgba(45,212,191,.08);
        color:#0f766e;
        border:1px solid rgba(45,212,191,.45);
    }
    .btn-aksi-card:hover{
        background:rgba(45,212,191,.16);
    }

    /* 🔹 TOMBOL KELOLA BATCH (AKSI) */
    .btn-aksi-batch{
        background:rgba(129,140,248,.08); /* indigo soft */
        color:#4f46e5;
        border:1px solid rgba(129,140,248,.5);
    }
    .btn-aksi-batch:hover{
        background:rgba(129,140,248,.16);
    }

    .btn-aksi-edit{
        background:rgba(234,179,8,.08);
        color:#ca8a04;
        border:1px solid rgba(234,179,8,.45);
    }
    .btn-aksi-edit:hover{
        background:rgba(234,179,8,.16);
    }
    .btn-aksi-delete{
        background:rgba(248,113,113,.08);
        color:#dc2626;
        border:1px solid rgba(248,113,113,.45);
    }
    .btn-aksi-delete:hover{
        background:rgba(248,113,113,.16);
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

    {{-- HEADER TITLE + BUTTON --}}
    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Item Variant</h1>
            <p class="page-subtitle">
                Daftar <strong>merek, batch, dan kedaluwarsa</strong> untuk setiap item master.
            </p>
        </div>

        @if($canCrud)
            <a href="{{ route('variants.create') }}" class="btn-add-item">
                <i class="ri-add-circle-line"></i>
                <span>Tambah Item Variant</span>
            </a>
        @endif
    </div>

    {{-- PANEL UTAMA --}}
    <div class="panel-card">

        {{-- FILTER BAR --}}
        <form method="get" class="mb-2">
            <div class="item-filters">
                {{-- Search --}}
                <div class="item-search-box">
                    <i class="ri-search-line"></i>
                    <input type="text"
                           name="q"
                           value="{{ $s }}"
                           placeholder="Cari merek, lot, atau kode item…"
                           class="item-search-input form-control">
                    @if($s)
                        <span class="item-search-badge">
                            <span>Pencarian aktif</span>
                            <span class="text-xs">“{{ \Illuminate\Support\Str::limit($s,18) }}”</span>
                        </span>
                    @endif
                </div>

                {{-- Filter Item --}}
                <div class="d-flex align-items-center gap-2">
                    <select name="item_master_id"
                            class="item-filter-select form-select"
                            onchange="this.form.submit()">
                        <option value="">Semua Item</option>
                        @foreach($items as $id => $label)
                            <option value="{{ $id }}" {{ (string)$itemId === (string)$id ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    @if($s || $itemId)
                        <button type="button"
                                onclick="window.location='{{ route('variants.index') }}'"
                                class="btn btn-outline-secondary btn-reset-filter">
                            <i class="ri-refresh-line me-1"></i> Reset
                        </button>
                    @endif
                </div>
            </div>
        </form>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-items align-middle mb-0">
                <thead>
                    <tr class="text-nowrap">
                        <th style="width:200px;">Item</th>
                        <th>Merek</th>
                        <th style="width:130px;">Lot / Batch</th>
                        <th style="width:130px;">Kedaluwarsa</th>
                        <th style="width:150px;" class="text-center">Label FEFO</th>
                        <th style="width:100px;" class="text-center">Jml Batch</th> {{-- 🔹 kolom baru --}}
                        <th style="width:130px;" class="text-center">Stok Varian</th>
                        <th style="width:260px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($variants as $v)
                    @php
                        $item      = $v->itemMaster;
                        $qty       = (int) (optional($v->stock)->current_quantity ?? 0);
                        $fefoClass = $v->fefo_badge_class ?? 'secondary';
                        $fefoStyle = match($fefoClass){
                            'success' => 'badge-fefo badge-fefo--good',
                            'warning' => 'badge-fefo badge-fefo--warning',
                            'danger','danger-soft' => 'badge-fefo badge-fefo--danger',
                            default   => 'badge-fefo badge-fefo--good',
                        };
                        $batchCount = (int) ($v->batches_count ?? $v->batchesCount ?? 0);
                    @endphp
                    <tr>
                        {{-- ITEM --}}
                        <td class="align-middle">
                            <span class="badge-kode mb-1">{{ $item->item_code }}</span>
                            <div class="item-name mt-1">{{ $item->item_name }}</div>
                            @if($item->base_unit)
                                <div class="item-meta">
                                    Satuan: {{ $item->base_unit_label ?? $item->base_unit }}
                                </div>
                            @endif
                        </td>

                        {{-- MEREK --}}
                        <td class="align-middle">
                            <div class="item-name">{{ $v->brand }}</div>
                        </td>

                        {{-- LOT --}}
                        <td class="align-middle">
                            <span class="item-meta">{{ $v->lot_number ?: '—' }}</span>
                        </td>

                        {{-- EXP --}}
                        <td class="align-middle">
                            @if($v->expiration_date)
                                <span class="item-meta">
                                    {{ $v->expiration_date->format('d-m-Y') }}
                                </span>
                            @else
                                <span class="item-meta">—</span>
                            @endif
                        </td>

                        {{-- FEFO --}}
                        <td class="align-middle text-center">
                            @if($v->expiration_date)
                                <span class="{{ $fefoStyle }}">
                                    {{ $v->fefo_label_text }}
                                </span>
                            @else
                                <span class="item-meta">—</span>
                            @endif
                        </td>

                        {{-- JML BATCH --}}
                        <td class="align-middle text-center">
                            @if($batchCount > 0)
                                <a href="{{ route('batches.index', ['variant_id' => $v->id]) }}"
                                   class="badge-batch">
                                    {{ $batchCount }} batch
                                </a>
                            @else
                                <span class="item-meta">Belum ada</span>
                            @endif
                        </td>

                        {{-- STOK --}}
                        <td class="align-middle text-center">
                            <span class="badge-stock {{ $qty > 0 ? 'badge-stock--ok' : 'badge-stock--empty' }}">
                                {{ $qty }}
                            </span>
                        </td>

                        {{-- AKSI --}}
                        <td class="align-middle">
                            <div class="aksi-gap">
                                {{-- Stok varian --}}
                                <a href="{{ route('stock.show', $v) }}"
                                   class="btn-aksi btn-aksi--sq btn-aksi-warehouse"
                                   title="Kartu Stok Variant">
                                    <i class="ri-archive-2-line"></i>
                                </a>

                                {{-- Kartu stok per item --}}
                                <a href="{{ route('variants.show', $v) }}"
                                   class="btn-aksi btn-aksi--sq btn-aksi-card"
                                   title="Kartu stok item (semua variant)">
                                    <i class="ri-clipboard-line"></i>
                                </a>

                                {{-- 🔹 KELOLA BATCH VARIAN INI --}}
                                <a href="{{ route('batches.index', ['variant_id' => $v->id]) }}"
                                   class="btn-aksi btn-aksi--sq btn-aksi-batch"
                                   title="Kelola batch untuk varian ini">
                                    <i class="ri-stack-line"></i>
                                </a>

                                @if($canCrud)
                                    {{-- Edit --}}
                                    <a href="{{ route('variants.edit', $v) }}"
                                       class="btn-aksi btn-aksi--sq btn-aksi-edit"
                                       title="Edit variant">
                                        <i class="ri-edit-2-line"></i>
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('variants.destroy',$v) }}"
                                          method="post"
                                          onsubmit="return confirm('Hapus variant {{ $v->brand }} / {{ $v->lot_number ?: "-" }} ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-aksi btn-aksi--sq btn-aksi-delete"
                                                title="Hapus variant">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Belum ada data variant yang tercatat.
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
