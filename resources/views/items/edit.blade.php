@extends('layouts.app')
@section('title','Edit Item: '.$item->item_code)

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
@php
    $baseUnits = \App\Models\ItemMaster::BASE_UNITS ?? [];
@endphp

<div class="container-fluid">

    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Edit Item Master</h1>
            <p class="page-subtitle">
                Perbarui informasi item master <strong>{{ $item->item_code }}</strong>.
            </p>
        </div>
    </div>

    <div class="panel-card">

        {{-- BADGE INFO --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <span class="badge bg-light text-muted border" style="border-radius:999px;">
                    Kode Item:
                    <span class="fw-semibold ms-1">{{ $item->item_code }}</span>
                </span>
            </div>
        </div>

        <form action="{{ route('items.update',$item) }}" method="post">
            @csrf
            @method('PUT')

            {{-- VALIDASI --}}
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

                {{-- KODE ITEM --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Kode Item <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="item_code"
                           class="form-control form-control-sm @error('item_code') is-invalid @enderror"
                           value="{{ old('item_code', $item->item_code) }}"
                           placeholder="HM-01, ATK001, LAB003, ...">
                    @error('item_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- NAMA ITEM --}}
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">
                        Nama Item <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="item_name"
                           class="form-control form-control-sm @error('item_name') is-invalid @enderror"
                           value="{{ old('item_name', $item->item_name) }}"
                           placeholder="Nama lengkap barang / reagen / ATK">
                    @error('item_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- KATEGORI --}}
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small fw-semibold">
                        Kategori
                    </label>
                    <select name="category_id"
                            class="form-select form-select-sm @error('category_id') is-invalid @enderror">
                        <option value="">Pilih kategori…</option>
                        @foreach($categories as $id => $name)
                            <option value="{{ $id }}"
                                {{ (string)old('category_id', $item->category_id) === (string)$id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- SATUAN DASAR --}}
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small fw-semibold">
                        Satuan Dasar <span class="text-danger">*</span>
                    </label>
                    <select name="base_unit"
                            class="form-select form-select-sm @error('base_unit') is-invalid @enderror">
                        <option value="">Pilih satuan…</option>
                        @foreach($baseUnits as $label)
                            <option value="{{ $label }}"
                                {{ old('base_unit', $item->base_unit) === $label ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('base_unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- WARNING STOK MINIMUM --}}
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small fw-semibold">
                        Warning Stok Minimum
                    </label>
                    <input type="number"
                           name="warning_stock"
                           min="0"
                           class="form-control form-control-sm @error('warning_stock') is-invalid @enderror"
                           value="{{ old('warning_stock', $item->warning_stock) }}"
                           placeholder="misal: 10">
                    @error('warning_stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- UKURAN / KEMASAN --}}
                <div class="col-md-4 col-lg-3">
                    <label class="form-label small fw-semibold">
                        Ukuran / Kemasan
                    </label>
                    <input type="text"
                           name="size"
                           class="form-control form-control-sm @error('size') is-invalid @enderror"
                           value="{{ old('size', $item->size) }}"
                           placeholder="misal: 100 test / kit, 500 ml, 1 box isi 10">
                    @error('size')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- SUHU SIMPAN --}}
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold">
                        Suhu / Cara Penyimpanan
                    </label>
                    <input type="text"
                           name="storage_temp"
                           class="form-control form-control-sm @error('storage_temp') is-invalid @enderror"
                           value="{{ old('storage_temp', $item->storage_temp) }}"
                           placeholder="misal: 2-8°C, suhu ruang, simpan terlindung dari cahaya">
                    @error('storage_temp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- PERINGATAN --}}
                <div class="col-md-6 col-lg-8">
                    <label class="form-label small fw-semibold">
                        Peringatan / Catatan Khusus
                    </label>
                    <textarea
                        name="warnings"
                        rows="2"
                        class="form-control form-control-sm @error('warnings') is-invalid @enderror"
                        placeholder="misal: Bahan berbahaya, gunakan APD; jangan dibekukan; hanya untuk pemeriksaan in-vitro">{{ old('warnings', $item->warnings) }}</textarea>
                    @error('warnings')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mt-4 gap-2">
                <a href="{{ route('items.index') }}" class="btn btn-outline-secondary btn-outline-soft">
                    <i class="ri-arrow-go-back-line me-1"></i> Batal
                </a>

                <button type="submit" class="btn btn-primary-gradient">
                    <i class="ri-save-3-line me-1"></i> Update Item
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
