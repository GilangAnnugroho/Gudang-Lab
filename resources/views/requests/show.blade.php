@extends('layouts.app')
@section('title','Detail Permintaan Barang')

@php
    use App\Models\Request as RequestModel;
    $roleName = strtolower(optional(auth()->user()->role)->role_name ?? '');
@endphp

@push('styles')
<style>
    .page-title{
        font-size:22px;
        font-weight:700;
    }
    .page-subtitle{
        font-size:12px;
        color:var(--text-muted);
        line-height:1.4;
    }
    .btn-outline-soft{
        border-radius:999px;
        font-size:13px;
        padding:.35rem 1.1rem;
    }
    .btn-primary-gradient{
        border:none;
        border-radius:999px;
        padding:.45rem 1.3rem;
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
    .btn-primary-gradient:hover{
        transform:translateY(-1px);
        box-shadow:0 14px 32px rgba(15,23,42,.25);
        opacity:.97;
    }
    .btn-primary-gradient:active{
        transform:translateY(0);
        box-shadow:0 6px 18px rgba(15,23,42,.25);
    }
    .panel-card{
        background:#ffffff;
        border-radius:18px;
        border:1px solid #e5e7eb;
        padding:14px 18px 18px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
    }
    .section-title{
        font-size:13px;
        font-weight:600;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:var(--text-muted);
        margin-bottom:6px;
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
    .badge-status--primary,
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

    .table-req-details{
        font-size:13px;
        border-collapse:separate;
        border-spacing:0;
    }
    .table-req-details thead th{
        background:#f9fafb;
        border-top:1px solid #e5e7eb;
        border-bottom:1px solid #e5e7eb;
        border-right:1px solid #e5e7eb;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.05em;
        color:var(--text-muted);
    }
    .table-req-details thead th:first-child{
        border-left:1px solid #e5e7eb;
    }
    .table-req-details tbody td{
        border-bottom:1px solid #e5e7eb;
        border-right:1px solid #e5e7eb;
        background:#ffffff;
    }
    .table-req-details tbody td:first-child{
        border-left:1px solid #e5e7eb;
    }
    .table-req-details tbody tr:hover td{
        background:#f3f4f6;
    }

    .table-distribusi{
        font-size:13px;
    }
    .table-distribusi thead th{
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.06em;
        color:var(--text-muted);
        background:#f9fafb;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
        <div>
            <h1 class="page-title mb-1">Detail Permintaan Barang</h1>
            <p class="page-subtitle mb-0">
                Permintaan dari unit <strong>{{ optional($req->unit)->unit_name ?? '—' }}</strong>
                pada tanggal <strong>{{ $req->request_date?->format('d-m-Y') ?? '—' }}</strong>.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-end">

            <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary btn-outline-soft">
                <i class="ri-arrow-left-line me-1"></i> Kembali 
            </a>

            @can('update', $req)
                @if($req->is_pending)
                    <a href="{{ route('requests.edit', $req) }}" class="btn btn-outline-warning btn-outline-soft">
                        <i class="ri-edit-2-line me-1"></i> Edit Header
                    </a>
                @endif
            @endcan

            {{-- APPROVE / REJECT --}}
            @if($req->status === RequestModel::STATUS_PENDING
                && in_array($roleName, ['super admin','kepala lab']))

                <form action="{{ route('requests.approve', $req) }}"
                      method="post"
                      onsubmit="return confirm('Setujui permintaan ini (#{{ $req->id }}) ?');">
                    @csrf
                    @method('PUT')
                    <button class="btn btn-success btn-outline-soft">
                        <i class="ri-check-line me-1"></i> Setujui
                    </button>
                </form>

                <form action="{{ route('requests.reject', $req) }}"
                      method="post"
                      onsubmit="return confirm('Tolak permintaan ini (#{{ $req->id }}) ?');">
                    @csrf
                    @method('PUT')
                    <button class="btn btn-outline-danger btn-outline-soft">
                        <i class="ri-close-line me-1"></i> Tolak
                    </button>
                </form>
            @endif

            {{-- DISTRIBUSI BARANG --}}
            @if(in_array($roleName, ['admin gudang','super admin']) && $req->status === RequestModel::STATUS_APPROVED)
                <form action="{{ route('requests.distribute', $req) }}"
                      method="post"
                      onsubmit="return confirm('Proses distribusi untuk permintaan ini (#{{ $req->id }})?');">
                    @csrf
                    <button class="btn-primary-gradient">
                        <i class="ri-truck-line me-1"></i> Distribusi Barang
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- HEADER CARD --}}
    <div class="panel-card mb-3">
        <div class="section-title">Header Permintaan</div>

        <div class="row small">
            <div class="col-md-4 mb-2">
                <strong>Tanggal Permintaan</strong><br>
                {{ $req->request_date?->format('d-m-Y') ?? '—' }}
            </div>
            <div class="col-md-4 mb-2">
                <strong>Unit Peminta</strong><br>
                {{ optional($req->unit)->unit_name ?? '—' }}
            </div>
            <div class="col-md-4 mb-2">
                <strong>Status</strong><br>
                <span class="badge-status badge-status--{{ $req->status_badge }}">
                    {{ $req->status_label }}
                </span>
            </div>

            <div class="col-md-4 mb-2">
                <strong>Peminta</strong><br>
                {{ optional($req->requester)->name ?? '—' }}
            </div>
            <div class="col-md-4 mb-2">
                <strong>Disetujui Oleh</strong><br>
                {{ optional($req->approver)->name ?? '—' }}
            </div>
            <div class="col-md-4 mb-2">
                <strong>Dibuat</strong><br>
                {{ $req->created_at?->format('d-m-Y H:i') ?? '—' }}
            </div>
        </div>
    </div>

    {{-- RINCIAN --}}
    <div class="panel-card mb-3">
        <div class="section-title mb-2">Rincian Barang yang Diminta</div>

        <div class="table-responsive">
            <table class="table table-req-details table-bordered table-hover align-middle mb-0">
                <thead class="text-center">
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Item</th>
                        <th style="width:110px;">Jumlah Diminta</th>
                        <th style="width:140px;">Jumlah Tersalurkan</th>
                        <th style="width:230px;">Merek / Lot / Exp</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($req->details as $i => $d)
                    <tr>
                        <td class="text-center align-middle">{{ $i+1 }}</td>

                        <td class="align-middle">
                            <strong>{{ optional($d->itemMaster)->item_code }}</strong>
                            <div class="text-muted small">
                                {{ optional($d->itemMaster)->item_name }}
                            </div>
                        </td>

                        <td class="align-middle text-end">
                            {{ $d->requested_quantity }}
                        </td>

                        <td class="align-middle text-end">
                            {{ $d->distributed_quantity }}
                        </td>

                        <td class="align-middle">
                            @if($d->itemVariant)
                                <strong>{{ $d->itemVariant->brand }}</strong>
                                <div class="small text-muted">
                                    Lot: {{ $d->itemVariant->lot_number ?: '—' }},
                                    Exp: {{ $d->itemVariant->expiration_date?->format('d-m-Y') ?? '—' }}
                                </div>
                            @else
                                <span class="text-muted small">
                                    Belum ditentukan (dipilih saat distribusi dari gudang)
                                </span>
                            @endif
                        </td>

                        <td class="align-middle">
                            {{ $d->notes }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            Tidak ada detail permintaan.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TRANSAKSI DISTRIBUSI TERKAIT --}}
    @if($req->transactions->isNotEmpty())
        <div class="panel-card">
            <div class="section-title mb-2">
                Transaksi Distribusi Terkait
            </div>

            <div class="table-responsive">
                <table class="table table-distribusi align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;">No</th>
                            <th style="width:110px;">Tanggal</th>
                            <th style="width:70px;">Jenis</th>
                            <th>Nama Barang</th>
                            <th class="text-end" style="width:90px;">Jumlah</th>
                            <th style="width:80px;">Satuan</th>
                            <th style="width:130px;">Merek</th>
                            <th style="width:110px;">No. LOT</th>
                            <th style="width:110px;">Exp</th>
                            <th style="width:80px;" class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($req->transactions as $i => $t)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            <td>{{ optional($t->trans_date)->format('d-m-Y') ?? '—' }}</td>
                            <td>{{ $t->type }}</td>
                            <td>{{ $t->item_name }}</td>
                            <td class="text-end">{{ number_format($t->quantity,0,',','.') }}</td>
                            <td>{{ $t->base_unit ?? '—' }}</td>
                            <td>{{ $t->brand ?? '—' }}</td>
                            <td>{{ $t->lot ?? '—' }}</td>
                            <td>{{ $t->exp ?? '—' }}</td>
                            <td class="text-end">
                                <a href="{{ route('transactions.show', $t) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="small text-muted mt-2">
                Untuk melihat detail lengkap (harga, pajak, rekanan/unit), buka halaman Transaksi Barang dengan filter permintaan ini.
            </div>
        </div>
    @endif

</div>
@endsection
