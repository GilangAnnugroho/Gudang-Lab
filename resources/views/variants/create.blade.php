@extends('layouts.app')
@section('title','Tambah Variant')

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
    }
    .btn-primary-gradient i{
        font-size:18px;
    }
    .btn-outline-soft{
        border-radius:999px;
        font-size:13px;
        padding:.45rem 1.1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Tambah Item Variant</h1>
            <p class="page-subtitle mb-0">
                Registrasi <strong>merek / batch / tanggal kedaluwarsa</strong> untuk item master gudang.
            </p>
        </div>
    </div>

    <div class="panel-card">
        <form action="{{ route('variants.store') }}" method="post">
            @csrf

            {{-- VALIDASI (bukan flash sukses) --}}
            @if ($errors->any())
                <div class="alert alert-warning mb-3">
                    <strong>Periksa kembali input Anda:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">

                {{-- ITEM MASTER (item_master_id) --}}
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold">
                        Item Master <span class="text-danger">*</span>
                    </label>
                    <select
                        name="item_master_id"
                        class="form-select form-select-sm @error('item_master_id') is-invalid @enderror">
                        <option value="">Pilih item…</option>
                        @foreach($items as $id => $label)
                            <option value="{{ $id }}" {{ (string)old('item_master_id') === (string)$id ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Hubungkan varian ke item master yang tepat.</div>
                    @error('item_master_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- BRAND (brand) --}}
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold">
                        Merek / Brand <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="brand"
                           class="form-control form-control-sm @error('brand') is-invalid @enderror"
                           value="{{ old('brand') }}"
                           placeholder="misal: Nesco Lab, BioChem, OneMed">
                    <div class="form-text">Nama merek yang tercetak pada kemasan.</div>
                    @error('brand')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- LOT NUMBER (lot_number) --}}
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold">
                        Nomor Lot / Batch
                    </label>
                    <input type="text"
                           name="lot_number"
                           class="form-control form-control-sm @error('lot_number') is-invalid @enderror"
                           value="{{ old('lot_number') }}"
                           placeholder="misal: REG-GLU-01, ATK-2025-A1">
                    <div class="form-text">
                        Wajib diisi 
                    </div>
                    @error('lot_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- EXPIRATION DATE (expiration_date) --}}
                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold">
                        Tanggal Kedaluwarsa
                    </label>
                    <input type="date"
                           name="expiration_date"
                           class="form-control form-control-sm @error('expiration_date') is-invalid @enderror"
                           value="{{ old('expiration_date') }}">
                    <div class="form-text">
                        Wajib diisi untuk reagen; boleh kosong untuk barang non-expired.
                    </div>
                    @error('expiration_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mt-4 gap-2">
                <a href="{{ route('variants.index') }}" class="btn btn-outline-secondary btn-outline-soft">
                    <i class="ri-arrow-go-back-line me-1"></i> Batal
                </a>

                <button type="submit" class="btn btn-primary-gradient">
                    <i class="ri-save-3-line me-1"></i> Simpan Variant
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
