@extends('layouts.app')
@section('title','Unit')

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

    .unit-name{
        font-weight:600;
        font-size:13px;
    }

    .badge-count{
        display:inline-flex;
        align-items:center;
        padding:.2rem .55rem;
        border-radius:999px;
        font-size:11px;
        font-weight:600;
    }
    .badge-count--info{ background:#eef2ff; color:#4f46e5; }
    .badge-count--primary{ background:#eff6ff; color:#1d4ed8; }
    .badge-count--success{ background:#ecfdf3; color:#16a34a; }

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

    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Unit</h1>
            <p class="page-subtitle">
                Daftar unit yang menjadi tujuan distribusi barang gudang Labkesda.
            </p>
        </div>

        <a href="{{ route('units.create') }}" class="btn-add-item">
            <i class="ri-add-circle-line"></i>
            <span>Tambah Unit</span>
        </a>
    </div>

    <div class="panel-card">

        {{-- FILTER BAR --}}
        <form method="get" class="mb-2">
            <div class="item-filters">
                <div class="item-search-box">
                    <i class="ri-search-line"></i>
                    <input type="text"
                           name="q"
                           value="{{ $search }}"
                           placeholder="Cari nama unit / ruangan…"
                           class="item-search-input form-control">
                    @if($search)
                        <span class="item-search-badge">
                            <span>Pencarian aktif</span>
                            <span class="text-xs">“{{ \Illuminate\Support\Str::limit($search,18) }}”</span>
                        </span>
                    @endif
                </div>

                @if($search)
                    <button type="button"
                            onclick="window.location='{{ route('units.index') }}'"
                            class="btn btn-outline-secondary btn-reset-filter">
                        <i class="ri-refresh-line me-1"></i> Reset
                    </button>
                @endif

                <div class="ms-auto small text-muted">
                    Total: {{ $units->total() }}
                </div>
            </div>
        </form>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-items align-middle mb-0">
                <thead>
                <tr>
                    <th>Nama Unit</th>
                    <th class="text-center" style="width:120px">User</th>
                    <th class="text-center" style="width:140px">Permintaan</th>
                    <th class="text-center" style="width:160px">Transaksi Keluar</th>
                    <th class="text-end" style="width:160px">Aksi</th>
                </tr>
                </thead>

                <tbody>
                @forelse($units as $u)
                    <tr>
                        <td class="align-middle">
                            <span class="unit-name">
                                <i class="ri-building-line me-1 text-muted"></i>
                                {{ $u->unit_name }}
                            </span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge-count badge-count--info">{{ $u->users_count }}</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge-count badge-count--primary">{{ $u->requests_count }}</span>
                        </td>
                        <td class="align-middle text-center">
                            <span class="badge-count badge-count--success">{{ $u->destination_transactions_count }}</span>
                        </td>

                        <td class="align-middle">
                            <div class="aksi-gap">
                                <a href="{{ route('units.edit', $u) }}"
                                   class="btn-aksi btn-aksi--sq btn-aksi-edit">
                                    <i class="ri-pencil-line"></i>
                                </a>

                                <form action="{{ route('units.destroy', $u) }}"
                                      method="post"
                                      onsubmit="return confirm('Hapus unit {{ $u->unit_name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-aksi btn-aksi--sq btn-aksi-delete">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Belum ada data unit.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($units->hasPages())
            <div class="pagination-wrapper">
                {{ $units->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
