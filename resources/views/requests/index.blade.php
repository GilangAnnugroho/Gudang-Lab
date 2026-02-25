@extends('layouts.app')
@section('title','Daftar Permintaan')

@php
    use App\Models\Request as RequestModel;
    $roleName = strtolower(optional(auth()->user()->role)->role_name ?? '');
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

    .btn-primary-gradient{
        border:none;
        border-radius:999px;
        padding:.5rem 1.4rem;
        font-size:13px;
        font-weight:600;
        background:linear-gradient(90deg,#4f46e5,#06b6d4);
        color:#fff;
        box-shadow:0 10px 25px rgba(15,23,42,.25);
        display:inline-flex;
        align-items:center;
        gap:6px;
        transition:all .15s ease;
    }
    .btn-primary-gradient i{
        font-size:18px;
    }
    .btn-primary-gradient:hover{
        transform:translateY(-1px);
        box-shadow:0 14px 32px rgba(15,23,42,.25);
        opacity:.97;
    }
    .btn-primary-gradient:active{
        transform:translateY(0);
        box-shadow:0 6px 18px rgba(15,23,42,.25);
    }

    .btn-outline-soft{
        border-radius:999px;
        font-size:13px;
        padding:.4rem 1.1rem;
    }

    .panel-card{
        background:#ffffff;
        border-radius:18px;
        border:1px solid #e5e7eb;
        padding:14px 16px 16px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
    }

    .request-filters{
        display:flex;
        flex-wrap:wrap;
        gap:.65rem;
        align-items:flex-end;
        margin-bottom:10px;
    }
    .filter-group{
        display:flex;
        flex-direction:column;
        gap:4px;
    }
    .filter-label{
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--text-muted);
        margin:0;
    }
    .filter-input,
    .filter-select{
        font-size:13px;
        border-radius:999px;
        border:1px solid #e5e7eb;
        padding:.45rem .9rem;
        box-shadow:0 6px 18px rgba(15,23,42,.04);
    }
    .filter-actions{
        display:flex;
        gap:.4rem;
        align-items:center;
    }
    .btn-filter{
        border-radius:999px;
        font-size:12px;
        padding:.4rem 1rem;
    }
    .btn-reset-filter{
        border-radius:999px;
        font-size:12px;
        padding:.4rem .9rem;
    }

    .table-requests{
        font-size:13px;
        border-collapse:separate;
        border-spacing:0;
    }
    .table-requests thead th{
        border-top:none;
        border-bottom:1px solid #e5e7eb;
        background:#f9fafb;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--text-muted);
    }
    .table-requests tbody tr:last-child td{
        border-bottom:0;
    }
    .table-requests tbody tr:hover{
        background:#f9fafb;
    }

    .badge-unit{
        display:inline-flex;
        align-items:center;
        padding:.2rem .55rem;
        border-radius:999px;
        font-size:11px;
        font-weight:600;
        background:rgba(59,130,246,.08);
        color:#1d4ed8;
    }
    .meta-small{
        font-size:11px;
        color:var(--text-muted);
    }

    .badge-status{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        padding:.22rem .75rem;
        border-radius:999px;
        font-size:11px;
        font-weight:600;
        white-space:nowrap;
    }
    .badge-status--warning{
        background:#fef3c7;
        color:#92400e;
    }
    .badge-status--success{
        background:#ecfdf3;
        color:#166534;
    }
    .badge-status--danger{
        background:#fee2e2;
        color:#b91c1c;
    }
    .badge-status--primary{
        background:#e0f2fe;
        color:#075985;
    }
    .badge-status--info{
        background:#e0f2fe;
        color:#075985;
    }
    .badge-status--secondary,
    .badge-status--muted,
    .badge-status--default{
        background:#e5e7eb;
        color:#374151;
    }

    .aksi-gap{
        display:flex;
        justify-content:flex-end;
        gap:.35rem;
    }
    .btn-aksi{
        border-radius:999px;
        padding:.32rem .7rem;
        font-size:13px;
        border:1px solid transparent;
        background:#f9fafb;
        color:#4b5563;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        transition:all .15s ease;
    }
    .btn-aksi--sq{
        width:32px;
        height:32px;
        padding:0;
    }
    .btn-aksi:hover{
        transform:translateY(-1px);
        box-shadow:0 6px 18px rgba(15,23,42,.12);
    }
    .btn-aksi:active{
        transform:translateY(0);
        box-shadow:0 3px 10px rgba(15,23,42,.18);
    }

    .btn-aksi-view{
        background:rgba(59,130,246,.08);
        color:#1d4ed8;
        border-color:rgba(59,130,246,.4);
    }
    .btn-aksi-view:hover{
        background:rgba(59,130,246,.16);
    }
    .btn-aksi-edit{
        background:rgba(234,179,8,.08);
        color:#ca8a04;
        border-color:rgba(234,179,8,.45);
    }
    .btn-aksi-edit:hover{
        background:rgba(234,179,8,.16);
    }
    .btn-aksi-delete{
        background:rgba(248,113,113,.08);
        color:#dc2626;
        border-color:rgba(248,113,113,.45);
    }
    .btn-aksi-delete:hover{
        background:rgba(248,113,113,.16);
    }
    .btn-aksi-approve{
        background:rgba(34,197,94,.08);
        color:#15803d;
        border-color:rgba(34,197,94,.45);
    }
    .btn-aksi-approve:hover{
        background:rgba(34,197,94,.16);
    }
    .btn-aksi-reject{
        background:rgba(248,113,113,.08);
        color:#b91c1c;
        border-color:rgba(248,113,113,.45);
    }
    .btn-aksi-reject:hover{
        background:rgba(248,113,113,.16);
    }
    .btn-aksi-distribute{
        background:rgba(129,140,248,.08);
        color:#4f46e5;
        border-color:rgba(129,140,248,.5);
    }
    .btn-aksi-distribute:hover{
        background:rgba(129,140,248,.16);
    }

    .btn-link-distribusi{
        border-radius:999px;
        font-size:11px;
        padding:.25rem .9rem;
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
        .btn-primary-gradient{
            width:100%;
            justify-content:center;
        }
        .request-filters{
            flex-direction:column;
            align-items:stretch;
        }
        .filter-actions{
            justify-content:flex-start;
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
            <h1 class="page-title">Permintaan Barang</h1>
            <p class="page-subtitle">
                Daftar permintaan barang dari <strong>seluruh unit</strong> beserta status persetujuannya.
            </p>
        </div>

        @can('create', App\Models\Request::class)
            <a href="{{ route('requests.create') }}" class="btn-primary-gradient">
                <i class="ri-add-circle-line"></i>
                <span>Buat Permintaan</span>
            </a>
        @endcan
    </div>

    {{-- PANEL UTAMA --}}
    <div class="panel-card">

        {{-- FILTER BAR --}}
        <form method="get" class="mb-2">
            <div class="request-filters">

                {{-- PERIODE --}}
                <div class="filter-group">
                    <label class="filter-label">Periode</label>
                    <div class="d-flex align-items-center gap-1">
                        <input type="date"
                               name="from"
                               value="{{ $from }}"
                               class="form-control form-control-sm filter-input">
                        <span class="mx-1 meta-small">s/d</span>
                        <input type="date"
                               name="to"
                               value="{{ $to }}"
                               class="form-control form-control-sm filter-input">
                    </div>
                </div>

                {{-- STATUS --}}
                @php
                    $statusOptions = \App\Models\Request::STATUSES;
                @endphp
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select name="status"
                            class="form-select form-select-sm filter-select">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $st)
                            <option value="{{ $st }}" {{ $status === $st ? 'selected' : '' }}>
                                {{ (new RequestModel(['status'=>$st]))->status_label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- UNIT --}}
                <div class="filter-group">
                    <label class="filter-label">Unit</label>
                    <select name="unit_id"
                            class="form-select form-select-sm filter-select">
                        <option value="">Semua Unit</option>
                        @foreach($units as $id => $name)
                            <option value="{{ $id }}" {{ (string)$unitId === (string)$id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ACTIONS --}}
                <div class="filter-group">
                    <label class="filter-label">&nbsp;</label>
                    <div class="filter-actions">
                        <button class="btn btn-outline-secondary btn-filter" type="submit">
                            <i class="ri-filter-3-line me-1"></i> Terapkan
                        </button>

                        @if($status || $unitId || $from || $to)
                            <button type="button"
                                    onclick="window.location='{{ route('requests.index') }}'"
                                    class="btn btn-outline-secondary btn-reset-filter">
                                <i class="ri-refresh-line me-1"></i> Reset
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </form>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-requests align-middle mb-0">
                <thead>
                    <tr class="text-nowrap">
                        <th style="width:90px;">Tanggal</th>
                        <th style="width:160px;">Unit</th>
                        <th>Peminta</th>
                        <th style="width:140px;" class="text-center">Status</th>
                        <th style="width:120px;" class="text-center">Jumlah Item</th>
                        <th style="width:140px;" class="text-center">Distribusi</th>
                        <th style="width:260px;" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($requests as $req)
                    <tr>
                        {{-- TANGGAL --}}
                        <td class="align-middle">
                            <span class="meta-small">
                                {{ $req->request_date?->format('d-m-Y') ?? '—' }}
                            </span>
                        </td>

                        {{-- UNIT --}}
                        <td class="align-middle">
                            @if(optional($req->unit)->unit_name)
                                <span class="badge-unit">
                                    {{ $req->unit->unit_name }}
                                </span>
                            @else
                                <span class="meta-small">—</span>
                            @endif
                        </td>

                        {{-- PEMINTA --}}
                        <td class="align-middle">
                            <div><strong>{{ optional($req->requester)->name ?? '—' }}</strong></div>
                            <div class="meta-small">
                                Dibuat: {{ $req->created_at?->format('d-m-Y H:i') ?? '—' }}
                            </div>
                        </td>

                        {{-- STATUS --}}
                        <td class="align-middle text-center">
                            <span class="badge-status badge-status--{{ $req->status_badge }}">
                                {{ $req->status_label }}
                            </span>
                        </td>

                        {{-- JUMLAH ITEM --}}
                        <td class="align-middle text-center">
                            <span class="badge bg-info text-white">
                                {{ $req->details->count() }}
                            </span>
                        </td>

                        {{-- DISTRIBUSI TERKAIT --}}
                        <td class="align-middle text-center">
                            @if($req->distribution_count > 0)
                                <a href="{{ route('transactions.index', ['request_id' => $req->id, 'type' => 'KELUAR']) }}"
                                   class="btn btn-sm btn-outline-primary btn-link-distribusi">
                                    <i class="ri-truck-line me-1"></i>
                                    Lihat ({{ $req->distribution_count }})
                                </a>
                            @else
                                <span class="meta-small text-muted">
                                    @if($req->status === RequestModel::STATUS_APPROVED && in_array($roleName, ['admin gudang','super admin']))
                                        Belum didistribusi
                                    @else
                                        —
                                    @endif
                                </span>
                            @endif
                        </td>

                        {{-- AKSI --}}
                        <td class="align-middle">
                            <div class="aksi-gap">

                                {{-- Lihat detail --}}
                                <a href="{{ route('requests.show', $req) }}"
                                   class="btn-aksi btn-aksi--sq btn-aksi-view"
                                   title="Lihat detail permintaan">
                                    <i class="ri-eye-line"></i>
                                </a>

                                {{-- Edit header (hanya pemilik & masih PENDING) --}}
                                @can('update', $req)
                                    @if($req->is_pending)
                                        <a href="{{ route('requests.edit', $req) }}"
                                           class="btn-aksi btn-aksi--sq btn-aksi-edit"
                                           title="Edit header permintaan">
                                            <i class="ri-edit-2-line"></i>
                                        </a>
                                    @endif
                                @endcan

                                {{-- Hapus (hanya pemilik & masih PENDING) --}}
                                @can('delete', $req)
                                    @if($req->is_pending)
                                        <form action="{{ route('requests.destroy', $req) }}"
                                              method="post"
                                              onsubmit="return confirm('Hapus permintaan tanggal {{ $req->request_date?->format('d-m-Y') }} dari {{ optional($req->unit)->unit_name ?? '-' }}? Data yang sudah dihapus tidak dapat dikembalikan.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn-aksi btn-aksi--sq btn-aksi-delete"
                                                    title="Hapus permintaan">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan

                                {{-- APPROVE / REJECT dari daftar --}}
                                <!-- @if($req->status === RequestModel::STATUS_PENDING
                                    && in_array($roleName, ['super admin','kepala lab']))
                                    
                                    <form action="{{ route('requests.approve', $req) }}"
                                          method="post"
                                          onsubmit="return confirm('Setujui permintaan #{{ $req->id }} ?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                                class="btn-aksi btn-aksi--sq btn-aksi-approve"
                                                title="Setujui permintaan">
                                            <i class="ri-check-line"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('requests.reject', $req) }}"
                                          method="post"
                                          onsubmit="return confirm('Tolak permintaan #{{ $req->id }} ?');">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit"
                                                class="btn-aksi btn-aksi--sq btn-aksi-reject"
                                                title="Tolak permintaan">
                                            <i class="ri-close-line"></i>
                                        </button>
                                    </form>
                                @endif -->

                                {{-- DISTRIBUSI BARANG --}}
                                @if(in_array($roleName, ['admin gudang','super admin']) && $req->status === RequestModel::STATUS_APPROVED)
                                    <form action="{{ route('requests.distribute', $req) }}"
                                          method="post"
                                          onsubmit="return confirm('Proses distribusi untuk permintaan #{{ $req->id }} dari unit {{ optional($req->unit)->unit_name ?? '-' }} ?');">
                                        @csrf
                                        <button type="submit"
                                                class="btn-aksi btn-aksi--sq btn-aksi-distribute"
                                                title="Distribusi barang ke unit">
                                            <i class="ri-truck-line"></i>
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada permintaan barang.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($requests->hasPages())
            <div class="pagination-wrapper">
                {{ $requests->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
