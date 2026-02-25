@extends('layouts.app')
@section('title','Edit Permintaan Barang')

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
        padding:.45rem 1.1rem;
    }
    .panel-card{
        background:#ffffff;
        border-radius:18px;
        border:1px solid #e5e7eb;
        padding:16px 18px 18px;
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
    .form-label.small{
        font-size:12px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Edit Permintaan Barang</h1>
            <p class="page-subtitle mb-0">
                Permintaan dari unit <strong>{{ optional($req->unit)->unit_name ?? '—' }}</strong>
                pada tanggal <strong>{{ $req->request_date?->format('d-m-Y') ?? '—' }}</strong>.
            </p>
        </div>
        <!-- <a href="{{ route('requests.index', $req) }}" class="btn btn-outline-secondary btn-outline-soft">
            <i class="ri-arrow-left-line me-1"></i> Kembali 
        </a> -->
    </div>

    {{-- PANEL EDIT HEADER --}}
    <div class="panel-card mb-3">
        <form action="{{ route('requests.update', $req) }}" method="post">
            @csrf
            @method('PUT')

            {{-- VALIDASI --}}
            @if($errors->any())
                <div class="alert alert-warning mb-3">
                    <strong>Periksa kembali input Anda:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="section-title">Header Permintaan</div>

            <div class="row g-3 mb-1">
                {{-- TANGGAL --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Tanggal Permintaan <span class="text-danger">*</span>
                    </label>
                    <input type="date"
                           name="request_date"
                           class="form-control form-control-sm @error('request_date') is-invalid @enderror"
                           value="{{ old('request_date', $req->request_date?->format('Y-m-d')) }}">
                    @error('request_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- UNIT --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Unit Peminta <span class="text-danger">*</span>
                    </label>
                    <select name="unit_id"
                            class="form-select form-select-sm @error('unit_id') is-invalid @enderror">
                        <option value="">– pilih unit –</option>
                        @foreach($units as $id => $name)
                            <option value="{{ $id }}"
                                {{ (string)old('unit_id', $req->unit_id) === (string)$id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- PEMINTA --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Peminta
                    </label>
                    <input type="text"
                           class="form-control form-control-sm"
                           value="{{ optional($req->requester)->name }}"
                           readonly>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mt-4 gap-2">
                <a href="{{ route('requests.show', $req) }}" class="btn btn-outline-secondary btn-outline-soft">
                    <i class="ri-arrow-go-back-line me-1"></i> Batal
                </a>

                <button type="submit" class="btn btn-primary-gradient">
                    <i class="ri-save-3-line me-1"></i> Update Permintaan
                </button>
            </div>
        </form>
    </div>

    {{-- RINCIAN (READ ONLY) --}}
    <div class="panel-card">
        <div class="section-title mb-2">Rincian Barang (Tidak diubah di halaman ini)</div>

        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Item</th>
                        <th style="width:110px;" class="text-end">Jumlah</th>
                        <th style="width:180px;">Merek / Lot / Exp</th>
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
                            <td class="align-middle">
                                @if($d->itemVariant)
                                    {{ $d->itemVariant->brand }}
                                    @if($d->itemVariant->lot_number || $d->itemVariant->expiration_date)
                                        <small class="text-muted d-block">
                                            Lot: {{ $d->itemVariant->lot_number ?: '—' }},
                                            Exp: {{ $d->itemVariant->expiration_date?->format('d-m-Y') ?? '—' }}
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted small">Belum ditentukan</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                {{ $d->notes }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                Tidak ada detail permintaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
