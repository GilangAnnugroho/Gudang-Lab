@extends('layouts.app')
@section('title','Kartu Stok Barang (Per Item)')

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
    .badge-stock{
        background:#4f46e5;
        color:#fff;
        font-size:12px;
        border-radius:999px;
        padding:.35rem .9rem;
    }
    .btn-outline-soft{
        border-radius:999px;
        font-size:13px;
        padding:.35rem 1.1rem;
    }

    .table-kartu{
        font-size:13px;
        border-collapse:separate;
        border-spacing:0;
    }
    .table-kartu thead th{
        background:#f9fafb;
        border-top:1px solid #e5e7eb;
        border-bottom:1px solid #e5e7eb;
        border-right:1px solid #e5e7eb;
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.05em;
        color:var(--text-muted);
    }
    .table-kartu thead th:first-child{
        border-left:1px solid #e5e7eb;
    }

    .table-kartu tbody td{
        border-bottom:1px solid #e5e7eb;
        border-right:1px solid #e5e7eb;
        background:white;
    }
    .table-kartu tbody td:first-child{
        border-left:1px solid #e5e7eb;
    }
    .table-kartu tbody tr:hover td{
        background:#f3f4f6;
    }

    .badge-fefo{
        border-radius:999px;
        padding:.25rem .6rem;
        font-size:11px;
        font-weight:600;
        color:#fff;
        white-space: nowrap;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
        <div>
            <h1 class="page-title mb-1">Kartu Stok Barang</h1>

            <p class="page-subtitle mb-0">
                <strong>{{ $item->item_code }}</strong> — {{ $item->item_name }} <br>
                Satuan: {{ $item->base_unit_label ?? $item->base_unit }} <br>
                Menampilkan riwayat <strong>semua varian (brand, batch, expired)</strong> untuk item ini.
            </p>
        </div>

        <div class="text-end">
            <span class="badge-stock">Stok Saat Ini: {{ $currentStock }}</span>
            <div class="mt-2">
                <a href="{{ route('variants.index') }}" class="btn btn-outline-secondary btn-outline-soft btn-sm">
                    <i class="ri-arrow-left-line me-1"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="panel-card">

        {{-- FILTER --}}
        <form method="get" class="row g-2 mb-3">
            <input type="hidden" name="variant_id" value="{{ request('variant_id') }}">
            <div class="col-sm-4 col-md-3 col-lg-2">
                <label class="form-label small text-muted">Filter Jenis</label>
                <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Semua</option>
                    <option value="MASUK"  {{ ($type ?? '') === 'MASUK' ? 'selected' : '' }}>Masuk</option>
                    <option value="KELUAR" {{ ($type ?? '') === 'KELUAR' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>
        </form>

        {{-- TABEL --}}
        <div class="table-responsive">
            <table class="table table-kartu table-bordered table-hover align-middle mb-0">
                <thead class="text-center">
                    <tr>
                        <th style="width:110px;">Tanggal</th>
                        <th style="width:200px;">Merek / Batch</th>
                        <th style="width:110px;">Dokumen</th>
                        <th style="width:80px;">Jenis</th>
                        <th style="width:90px;">Masuk</th>
                        <th style="width:90px;">Keluar</th>
                        <th style="width:90px;">Sisa</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($transactions as $t)
                    @php $v = $t->variant; @endphp
                    <tr>

                        {{-- TANGGAL --}}
                        <td class="text-center text-nowrap">
                            {{ $t->trans_date->format('d-m-Y') }}
                        </td>

                        {{-- BRAND / LOT / EXP --}}
                        <td class="text-center">
                            <strong>{{ optional($v)->brand ?: '–' }}</strong>
                            @if(optional($v)->lot_number)
                                <div class="text-muted small">Lot: {{ $v->lot_number }}</div>
                            @endif

                            @if(optional($v)->expiration_date)
                                @php $fefoClass = $v->fefo_badge_class ?? 'secondary'; @endphp
                                <div class="text-muted small mt-1">
                                    Exp: {{ $v->expiration_date->format('d-m-Y') }}
                                    <br>
                                    <span class="badge-fefo bg-{{ $fefoClass }} ms-1">
                                        {{ $v->fefo_label_text }}
                                    </span>
                                </div>
                            @endif
                        </td>

                        {{-- DOKUMEN --}}
                        <td class="text-center">{{ $t->doc_no ?: '–' }}</td>

                        {{-- JENIS --}}
                        <td class="text-center">
                            <span class="badge rounded-pill bg-{{ $t->type === 'MASUK' ? 'success' : 'danger' }}">
                                {{ $t->type }}
                            </span>
                        </td>

                        {{-- MASUK --}}
                        <td class="text-center">
                            {{ $t->in_qty ? number_format($t->in_qty,0,',','.') : '' }}
                        </td>

                        {{-- KELUAR --}}
                        <td class="text-center">
                            {{ $t->out_qty ? number_format($t->out_qty,0,',','.') : '' }}
                        </td>

                        {{-- SISA --}}
                        <td class="text-center">
                            {{ number_format($t->balance,0,',','.') }}
                        </td>

                        {{-- NOTE --}}
                        <td class="text-start">{{ $t->note }}</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">
                            Belum ada transaksi untuk item ini.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
