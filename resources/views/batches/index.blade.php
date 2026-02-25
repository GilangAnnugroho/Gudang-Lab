@extends('layouts.app')
@section('title','Batch Per Lot')

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
        min-width:200px;
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

    .table-batches{
        font-size:13px;
        border-collapse:separate;
        border-spacing:0;
    }
    .table-batches thead th{
        border-top:none;
        border-bottom:1px solid #e5e7eb;
        background:#f9fafb;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--text-muted);
    }
    .table-batches tbody tr:last-child td{
        border-bottom:0;
    }
    .table-batches tbody tr:hover{
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
        background:#fee2e2;
        color:#b91c1c;
    }

    .aksi-gap{
        display:flex;
        justify-content:flex-end;
        gap:.35rem;
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
            <h1 class="page-title">Item Batch</h1>
            <p class="page-subtitle">
                Manajemen <strong>batch / lot</strong> per varian item.
                Jumlah <strong>masuk, keluar, dan stok</strong> di bawah ini dihitung otomatis dari transaksi.
            </p>
        </div>

        <a href="{{ route('batches.create') }}" class="btn-add-item">
            <i class="ri-add-circle-line"></i>
            <span>Tambah Batch</span>
        </a>
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
                           placeholder="Cari kode item, nama, merek, atau lot…"
                           class="item-search-input form-control">
                    @if($s)
                        <span class="item-search-badge">
                            <span>Pencarian aktif</span>
                            <span class="text-xs">“{{ \Illuminate\Support\Str::limit($s,18) }}”</span>
                        </span>
                    @endif
                </div>

                {{-- Filter Item --}}
                <select name="item_id"
                        class="item-filter-select form-select"
                        onchange="this.form.submit()">
                    <option value="">Semua Item</option>
                    @foreach($items as $id => $label)
                        <option value="{{ $id }}" {{ (string)$itemId === (string)$id ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                {{-- Filter Variant (optional) --}}
                <select name="variant_id"
                        class="item-filter-select form-select"
                        onchange="this.form.submit()">
                    <option value="">Semua Variant</option>
                    @foreach($variants as $v)
                        <option value="{{ $v->id }}" {{ (string)$variantId === (string)$v->id ? 'selected' : '' }}>
                            {{ optional($v->itemMaster)->item_code ?? '-' }} — {{ $v->brand }}
                        </option>
                    @endforeach
                </select>

                @if($s || $itemId || $variantId)
                    <button type="button"
                            onclick="window.location='{{ route('batches.index') }}'"
                            class="btn btn-outline-secondary btn-reset-filter">
                        <i class="ri-refresh-line me-1"></i> Reset
                    </button>
                @endif

            </div>
        </form>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-batches align-middle mb-0">
                <thead>
                    <tr class="text-nowrap">
                        <th style="width:200px;">Item</th>
                        <th style="width:160px;">Merek / Variant</th>
                        <th style="width:120px;">Lot / Batch</th>
                        <th style="width:120px;">Kedaluwarsa</th>
                        <th style="width:90px;" class="text-end">Masuk</th>
                        <th style="width:90px;" class="text-end">Keluar</th>
                        <th style="width:110px;" class="text-center">Stok Batch</th>
                        <th style="width:140px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($batches as $b)
                    @php
                        $item    = optional($b->item);
                        $variant = optional($b->variant);
                        $current = (int) $b->current_quantity;
                    @endphp
                    <tr>
                        {{-- ITEM --}}
                        <td>
                            @if($item)
                                <span class="badge-kode">{{ $item->item_code }}</span>
                                <div class="item-name mt-1">{{ $item->item_name }}</div>
                            @else
                                <span class="text-muted small">Item tidak ditemukan</span>
                            @endif
                        </td>

                        {{-- VARIANT --}}
                        <td>
                            <div class="item-name">{{ $variant->brand ?? '—' }}</div>
                        </td>

                        {{-- LOT --}}
                        <td>
                            <span class="item-meta">{{ $b->lot_number ?: '—' }}</span>
                        </td>

                        {{-- EXP --}}
                        <td>
                            @if($b->expiration_date)
                                <span class="item-meta">
                                    {{ $b->expiration_date->format('d-m-Y') }}
                                </span>
                            @else
                                <span class="item-meta">—</span>
                            @endif
                        </td>

                        {{-- QTY IN --}}
                        <td class="text-end">
                            {{ number_format($b->quantity_in, 0, ',', '.') }}
                        </td>

                        {{-- QTY OUT --}}
                        <td class="text-end">
                            {{ number_format($b->quantity_out, 0, ',', '.') }}
                        </td>

                        {{-- CURRENT --}}
                        <td class="text-center">
                            <span class="badge-stock {{ $current > 0 ? 'badge-stock--ok' : 'badge-stock--empty' }}">
                                {{ number_format($current, 0, ',', '.') }}
                            </span>
                        </td>

                        {{-- AKSI --}}
                        <td class="text-end">
                            <div class="aksi-gap">
                                <a href="{{ route('batches.edit', $b) }}"
                                   class="btn-aksi btn-aksi--sq btn-aksi-edit"
                                   title="Edit batch">
                                    <i class="ri-pencil-line"></i>
                                </a>

                                <form action="{{ route('batches.destroy', $b) }}"
                                      method="post"
                                      onsubmit="return confirm('Hapus batch {{ $b->lot_number ?: 'tanpa lot' }} ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn-aksi btn-aksi--sq btn-aksi-delete"
                                            title="Hapus batch">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Belum ada data batch yang tercatat.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($batches->hasPages())
            <div class="pagination-wrapper">
                {{ $batches->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
