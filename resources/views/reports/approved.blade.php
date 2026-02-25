@extends('layouts.app')

@section('title', 'Laporan Permintaan Disetujui')

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
        color:#000 !important;
    }
    .btn-primary-gradient:active{
        transform:translateY(0);
        box-shadow:0 6px 18px rgba(15,23,42,.25);
        color:#000 !important;
    }

    .btn-danger-soft{
        border:none;
        border-radius:999px;
        padding:.5rem 1.4rem;
        font-size:13px;
        font-weight:600;
        background:#fee2e2;
        color:#b91c1c;
        box-shadow:0 10px 25px rgba(15,23,42,.08);
        display:inline-flex;
        align-items:center;
        gap:6px;
        transition:all .15s ease;
    }
    .btn-danger-soft i{
        font-size:18px;
    }
    .btn-danger-soft:hover{
        background:#fecaca;
        transform:translateY(-1px);
        box-shadow:0 14px 32px rgba(15,23,42,.12);
    }
    .btn-danger-soft:active{
        background:#fca5a5;
        transform:translateY(0);
        box-shadow:0 6px 18px rgba(15,23,42,.12);
    }

    .btn-reset-filter{
        border-radius:999px;
        padding:.45rem 1.3rem;
        font-size:13px;
        font-weight:600;
        background:#6b7280;
        color:#ffffff;
        border:1px solid transparent;
        display:inline-flex;
        align-items:center;
        gap:6px;
        transition:all .15s ease;
    }
    .btn-reset-filter:hover{
        background:#4b5563;
        transform:translateY(-1px);
    }
    .btn-reset-filter:active{
        background:#374151;
        transform:translateY(0);
    }

    .panel-card{
        background:#ffffff;
        border-radius:18px;
        border:1px solid #e5e7eb;
        padding:14px 16px 16px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
        margin-bottom:14px;
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
    .table-requests tbody tr:hover{
        background:#f9fafb;
    }

    .meta-small{
        font-size:11px;
        color:var(--text-muted);
    }

    .mini-chip{
        font-size:10px;
        padding:4px 8px;
        border-radius:999px;
        background:#f9fafb;
        border:1px solid #e5e7eb;
    }

    @media (max-width: 767.98px){
        .page-title-wrap{
            flex-direction:column;
        }
        .btn-primary-gradient{
            width:100%;
            justify-content:center;
        }
        .request-filters{
            flex-direction:column;
            align-items:stretch;
        }
    }
</style>
@endpush

@section('content')
@php
    use Illuminate\Support\Carbon;
@endphp

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Laporan Permintaan Disetujui</h1>
            <p class="page-subtitle">
                Daftar <strong>permintaan barang</strong> yang telah disetujui beserta informasi unit dan peminta.
            </p>
        </div>
        <div></div>
    </div>

    {{-- FILTER PANEL --}}
    <div class="panel-card">
        <h6 class="panel-title mb-1">Filter Laporan</h6>
        <p class="page-subtitle mb-3">
            Sesuaikan periode untuk menampilkan permintaan yang disetujui pada rentang tanggal tertentu.
        </p>

        <form method="GET" action="{{ route('reports.approved') }}">
            <div class="request-filters">

                {{-- PERIODE --}}
                <div class="filter-group">
                    <label class="filter-label">Periode</label>
                    <div class="d-flex align-items-center gap-1">
                        <input type="date"
                               name="date_from"
                               id="date_from"
                               value="{{ $dateFrom }}"
                               class="form-control form-control-sm filter-input">
                        <span class="mx-1 meta-small">s/d</span>
                        <input type="date"
                               name="date_to"
                               id="date_to"
                               value="{{ $dateTo }}"
                               class="form-control form-control-sm filter-input">
                    </div>
                </div>

                {{-- AKSI --}}
                <div class="filter-group">
                    <label class="filter-label">&nbsp;</label>
                    <div class="filter-actions">

                        {{-- TAMPILKAN --}}
                        <button type="submit" class="btn btn-primary-gradient">
                            <i class="ri-search-line"></i>
                            Tampilkan
                        </button>

                        {{-- RESET --}}
                        @if($dateFrom || $dateTo)
                            <button type="button"
                                    onclick="window.location='{{ route('reports.approved') }}'"
                                    class="btn-reset-filter">
                                <i class="ri-refresh-line me-1"></i>
                                Reset
                            </button>
                        @endif

                        {{-- CETAK PDF --}}
                        <a href="{{ route('reports.approved.print', [
                                'date_from' => $dateFrom,
                                'date_to'   => $dateTo,
                            ]) }}"
                           class="btn-danger-soft"
                           target="_blank">
                            <i class="ri-file-pdf-line"></i>
                            Cetak PDF
                        </a>

                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- TABEL PERMINTAAN DISETUJUI --}}
    <div class="panel-card">
        <div class="d-flex justify-content-between mb-2">
            <div>
                <h6 class="panel-title mb-0">Daftar Permintaan Disetujui</h6>
                <p class="page-subtitle mb-0">
                    Menampilkan permintaan yang sudah berstatus <strong>disetujui</strong>.
                </p>
            </div>
            <div class="text-end d-none d-md-block">
                <span class="mini-chip">
                    Periode:
                    {{ $dateFrom ? Carbon::parse($dateFrom)->format('d-m-Y') : 'awal' }}
                    s/d
                    {{ $dateTo ? Carbon::parse($dateTo)->format('d-m-Y') : 'akhir' }}
                </span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-requests align-middle mb-0">
                <thead>
                    <tr class="text-nowrap">
                        <th style="width:40px;">No</th>
                        <th style="width:100px;">Tgl Permintaan</th>
                        <th style="width:80px;">No. Req</th>
                        <th style="width:150px;">Unit</th>
                        <th>Peminta</th>
                        <th style="width:150px;">Disetujui Oleh</th>
                        <th style="width:90px;">Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($rows as $i => $r)
                    @php
                        $unit     = optional($r->unit);
                        $peminta  = optional($r->requester);   // relasi peminta
                        $approver = optional($r->approver);

                        // Pakai tanggal permintaan agar sama dengan detail
                        $tglPermintaan = '-';
                        if (!empty($r->request_date)) {
                            if ($r->request_date instanceof Carbon) {
                                $tglPermintaan = $r->request_date->format('d-m-Y');
                            } else {
                                $tglPermintaan = Carbon::parse($r->request_date)->format('d-m-Y');
                            }
                        }
                    @endphp
                    <tr>
                        <td class="meta-small text-center">{{ $i + 1 }}</td>
                        <td class="meta-small text-center">{{ $tglPermintaan }}</td>
                        <td class="meta-small text-center">{{ $r->id }}</td>
                        <td class="meta-small">{{ $unit->unit_name ?? '-' }}</td>
                        <td class="meta-small">{{ $peminta->name ?? '-' }}</td>
                        <td class="meta-small">{{ $approver->name ?? '-' }}</td>
                        <td class="meta-small text-center">
                            {{ $r->status_label ?? $r->status ?? 'Disetujui' }}
                        </td>
                        <td class="meta-small">{{ $r->note ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Tidak ada permintaan yang disetujui pada periode ini.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
