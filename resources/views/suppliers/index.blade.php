@extends('layouts.app')
@section('title','Rekanan')

@php
    $r       = strtolower(optional(auth()->user()->role)->role_name ?? '');
    $canCrud = in_array($r, ['super admin','admin gudang']);
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

    .table-suppliers{
        font-size:13px;
        border-collapse:separate;
        border-spacing:0;
    }
    .table-suppliers thead th{
        border-top:none;
        border-bottom:1px solid #e5e7eb;
        background:#f9fafb;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--text-muted);
    }
    .table-suppliers tbody tr:last-child td{
        border-bottom:0;
    }
    .table-suppliers tbody tr:hover{
        background:#f9fafb;
    }

    .supplier-chip{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:.25rem .65rem;
        border-radius:999px;
        background:#ecfeff;
        color:#0369a1;
        font-size:11px;
        font-weight:600;
    }
    .supplier-chip i{
        font-size:14px;
    }

    .supplier-name{
        font-weight:600;
        font-size:13px;
        margin-top:4px;
    }
    .supplier-meta{
        font-size:11px;
        color:var(--text-muted);
    }

    .badge-transaksi{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        min-width:42px;
        padding:.2rem .55rem;
        border-radius:999px;
        font-size:11px;
        font-weight:600;
        background:#ecfdf3;
        color:#16a34a;
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
            <h1 class="page-title">Rekanan</h1>
            <p class="page-subtitle">
                Daftar perusahaan / vendor pemasok untuk kebutuhan gudang Labkesda.
            </p>
        </div>

        @if($canCrud)
            <a href="{{ route('suppliers.create') }}" class="btn-add-item">
                <i class="ri-add-circle-line"></i>
                <span>Tambah Rekanan</span>
            </a>
        @endif
    </div>

    {{-- PANEL UTAMA --}}
    <div class="panel-card">

        {{-- FILTER / SEARCH --}}
        <form method="get" class="mb-2">
            <div class="item-filters">
                <div class="item-search-box">
                    <i class="ri-search-line"></i>
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        class="form-control"
                        placeholder="Cari nama rekanan, contact person, atau telepon…">
                    @if($search)
                        <span class="item-search-badge">
                            <span>Pencarian:</span>
                            <span class="text-xs">“{{ \Illuminate\Support\Str::limit($search,18) }}”</span>
                        </span>
                    @endif
                </div>

                @if($search)
                    <button type="button"
                            onclick="window.location='{{ route('suppliers.index') }}'"
                            class="btn btn-outline-secondary btn-reset-filter">
                        <i class="ri-refresh-line me-1"></i> Reset
                    </button>
                @endif

                <span class="ms-auto small text-muted d-none d-md-inline">
                    Total Rekanan: <strong>{{ $suppliers->total() }}</strong>
                </span>
            </div>
        </form>

        {{-- TABEL --}}
        <div class="table-responsive">
            <table class="table table-suppliers align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:28%">Rekanan</th>
                        <th style="width:18%">Kontak</th>
                        <th style="width:14%">Telepon</th>
                        <th>Alamat</th>
                        <th style="width:130px" class="text-center">Transaksi Masuk</th>
                        <th style="width:150px" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($suppliers as $row)
                    <tr>
                        {{-- SUPPLIER + TAG --}}
                        <td>
                            <span class="supplier-chip">
                                <i class="ri-truck-line"></i>
                                REKANAN
                            </span>
                            <div class="supplier-name">
                                {{ $row->supplier_name }}
                            </div>
                        </td>

                        {{-- CONTACT PERSON --}}
                        <td>
                            <div class="supplier-meta">
                                {{ $row->contact_person ?: '—' }}
                            </div>
                        </td>

                        {{-- TELEPON --}}
                        <td>
                            <div class="supplier-meta">
                                {{ $row->phone ?: '—' }}
                            </div>
                        </td>

                        {{-- ALAMAT --}}
                        <td>
                            <div class="supplier-meta">
                                {{ $row->address ?: 'Belum ada alamat.' }}
                            </div>
                        </td>

                        {{-- TOTAL TRANSAKSI MASUK --}}
                        <td class="text-center">
                            <span class="badge-transaksi">
                                {{ $row->total_masuk }}
                            </span>
                        </td>

                        {{-- AKSI --}}
                        <td class="text-end">
                            @if($canCrud)
                                <div class="aksi-gap">
                                    <a href="{{ route('suppliers.edit', $row) }}"
                                       class="btn-aksi btn-aksi--sq btn-aksi-edit"
                                       title="Edit supplier">
                                        <i class="ri-pencil-line"></i>
                                    </a>

                                    <form action="{{ route('suppliers.destroy', $row) }}"
                                          method="post"
                                          class="form-delete-supplier"
                                          data-supplier-name="{{ $row->supplier_name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-aksi btn-aksi--sq btn-aksi-delete"
                                                title="Hapus supplier">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted" style="font-size:11px;">Tidak ada aksi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Belum ada data supplier.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($suppliers->hasPages())
            <div class="pagination-wrapper">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.form-delete-supplier').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                const name = this.dataset.supplierName || 'supplier ini';
                const ok = confirm('Hapus supplier "' + name + '" ?\nAksi ini tidak dapat dibatalkan.');
                if (!ok) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush
