@extends('layouts.app')
@section('title','Kategori')

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
        align-items:center;
    }
    .item-search-box{
        position:relative;
        flex:1 1 260px;
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

    .btn-reset-filter{
        border-radius:999px;
        font-size:12px;
        padding:.4rem .9rem;
    }

    .table-categories{
        font-size:13px;
        border-collapse:separate;
        border-spacing:0;
    }
    .table-categories thead th{
        border-top:none;
        border-bottom:1px solid #e5e7eb;
        background:#f9fafb;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--text-muted);
    }
    .table-categories tbody tr:last-child td{
        border-bottom:0;
    }
    .table-categories tbody tr:hover{
        background:#f9fafb;
    }

    .badge-cat{
        display:inline-flex;
        align-items:center;
        gap:4px;
        padding:.2rem .6rem;
        border-radius:999px;
        font-size:11px;
        font-weight:600;
        background:#eef2ff;
        color:#4f46e5;
    }
    .badge-cat i{
        font-size:14px;
    }

    .cat-name{
        font-weight:600;
        font-size:13px;
        margin-top:4px;
    }
    .cat-desc{
        font-size:11px;
        color:var(--text-muted);
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
    .btn-aksi-edit:hover{ background:rgba(234,179,8,.16); }

    .btn-aksi-delete{
        background:rgba(248,113,113,.08);
        color:#dc2626;
        border:1px solid rgba(248,113,113,.45);
    }
    .btn-aksi-delete:hover{ background:rgba(248,113,113,.16); }

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
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- HEADER TITLE + BUTTON --}}
    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Kategori</h1>
            <p class="page-subtitle">
                Pengelompokan jenis barang untuk memudahkan penyusunan stok dan laporan.
            </p>
        </div>

        @if($canCrud)
            <a href="{{ route('categories.create') }}" class="btn-add-item">
                <i class="ri-add-circle-line"></i>
                <span>Tambah Kategori</span>
            </a>
        @endif
    </div>

    {{-- PANEL UTAMA --}}
    <div class="panel-card">

        {{-- FILTER BAR (SEARCH) --}}
        <form method="get" class="mb-2">
            <div class="item-filters">
                <div class="item-search-box">
                    <i class="ri-search-line"></i>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        class="item-search-input form-control"
                        placeholder="Cari nama kategori atau deskripsi…">
                    @if($search)
                        <span class="item-search-badge">
                            <span>Pencarian:</span>
                            <span class="text-xs">“{{ \Illuminate\Support\Str::limit($search,18) }}”</span>
                        </span>
                    @endif
                </div>

                @if($search)
                    <button type="button"
                            onclick="window.location='{{ route('categories.index') }}'"
                            class="btn btn-outline-secondary btn-reset-filter">
                        <i class="ri-refresh-line me-1"></i> Reset
                    </button>
                @endif

                <span class="ms-auto small text-muted d-none d-md-inline">
                    Total kategori: <strong>{{ $categories->total() }}</strong>
                </span>
            </div>
        </form>

        {{-- TABEL --}}
        <div class="table-responsive">
            <table class="table table-categories align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:260px;">Kategori</th>
                        <th>Deskripsi</th>
                        <th style="width:160px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($categories as $row)
                    <tr>
                        {{-- NAMA --}}
                        <td>
                            <span class="badge-cat">
                                <i class="ri-price-tag-3-line"></i>
                                Kategori
                            </span>
                            <div class="cat-name">
                                {{ $row->category_name }}
                            </div>
                        </td>

                        {{-- DESKRIPSI --}}
                        <td>
                            <div class="cat-desc">
                                {{ $row->description ?: 'Belum ada deskripsi.' }}
                            </div>
                        </td>

                        {{-- AKSI --}}
                        <td>
                            <div class="aksi-gap">
                                @if($canCrud)
                                    <a href="{{ route('categories.edit', $row) }}"
                                       class="btn-aksi btn-aksi--sq btn-aksi-edit"
                                       title="Ubah kategori">
                                        <i class="ri-pencil-line"></i>
                                    </a>

                                    <form action="{{ route('categories.destroy', $row) }}"
                                          method="post"
                                          onsubmit="return confirm('Hapus kategori {{ $row->category_name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="btn-aksi btn-aksi--sq btn-aksi-delete"
                                                title="Hapus kategori">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted" style="font-size:11px;">Tidak ada aksi</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            Belum ada data kategori.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($categories->hasPages())
            <div class="pagination-wrapper">
                {{ $categories->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
