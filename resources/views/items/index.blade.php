@extends('layouts.app')
@section('title','Item Master')

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
        min-width:180px;
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
    .item-warning{
        font-size:11px;
        color:#dc2626;
    }

    .badge-category{
        display:inline-flex;
        align-items:center;
        padding:.2rem .55rem;
        border-radius:999px;
        background:#eff6ff;
        color:#1d4ed8;
        font-size:11px;
        font-weight:500;
    }

    .badge-unit{
        display:inline-flex;
        align-items:center;
        padding:.2rem .5rem;
        border-radius:999px;
        background:#ecfdf3;
        color:#16a34a;
        font-size:11px;
        font-weight:500;
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

    {{-- FLASH MESSAGE --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- HEADER TITLE + BUTTON --}}
    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Item Master</h1>
            <p class="page-subtitle">
                Daftar seluruh jenis barang yang tercatat di gudang Labkesda.
            </p>
        </div>

        <a href="{{ route('items.create') }}" class="btn-add-item">
            <i class="ri-add-circle-line"></i>
            <span>Tambah Item Master</span>
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
                           placeholder="Cari kode, nama item, atau kategori…"
                           class="item-search-input form-control">
                    @if($s)
                        <span class="item-search-badge">
                            <span>Pencarian aktif</span>
                            <span class="text-xs">“{{ \Illuminate\Support\Str::limit($s,18) }}”</span>
                        </span>
                    @endif
                </div>

                {{-- Category --}}
                <div class="d-flex align-items-center gap-2">
                    <select name="category_id"
                            class="item-filter-select form-select"
                            onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}" {{ (string)$category === (string)$id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>

                    @if($s || $category)
                        <button type="button"
                                onclick="window.location='{{ route('items.index') }}'"
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
                    <tr>
                        <th style="width:140px;">Kode</th>
                        <th>Nama & Detail Item</th>
                        <th style="width:180px;">Kategori</th>
                        <th style="width:110px;">Satuan</th>
                        <th style="width:160px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($items as $it)
                    <tr>
                        {{-- KODE --}}
                        <td class="align-middle">
                            <span class="badge-kode">{{ $it->item_code }}</span>
                        </td>

                        {{-- NAMA + DETAIL --}}
                        <td class="align-middle">
                            <div class="item-name">{{ $it->item_name }}</div>

                            @if($it->size)
                                <div class="item-meta">{{ $it->size }}</div>
                            @endif

                            @if($it->storage_temp)
                                <div class="item-meta">Simpan: {{ $it->storage_temp }}</div>
                            @endif

                            {{-- WARNING STOCK --}}
                            @if(!is_null($it->warning_stock) && $it->warning_stock !== '')
                                <div class="item-meta">
                                    Warning stok: <strong>{{ $it->warning_stock }}</strong>
                                </div>
                            @endif

                            @if($it->warnings)
                                <div class="item-warning">Peringatan: {{ $it->warnings }}</div>
                            @endif
                        </td>

                        {{-- KATEGORI --}}
                        <td class="align-middle">
                            @if(optional($it->category)->category_name)
                                <span class="badge-category">
                                    {{ $it->category->category_name }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:11px;">-</span>
                            @endif
                        </td>

                        {{-- SATUAN --}}
                        <td class="align-middle">
                            @if($it->base_unit_label)
                                <span class="badge-unit">{{ $it->base_unit_label }}</span>
                            @else
                                <span class="text-muted" style="font-size:11px;">-</span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td class="align-middle">
                            <div class="aksi-gap">
                                <a href="{{ route('items.edit',$it) }}"
                                   class="btn-aksi btn-aksi--sq btn-aksi-edit"
                                   title="Edit item">
                                    <i class="ri-pencil-line"></i>
                                </a>

                                <form action="{{ route('items.destroy',$it) }}"
                                      method="post"
                                      onsubmit="return confirm('Hapus item {{ $it->item_code }} — {{ $it->item_name }} ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn-aksi btn-aksi--sq btn-aksi-delete"
                                            title="Hapus item">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Belum ada data item yang tercatat.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($items->hasPages())
            <div class="pagination-wrapper">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
