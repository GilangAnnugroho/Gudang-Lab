@extends('layouts.app')
@section('title','Edit Batch')

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
    /** @var \App\Models\ItemBatch $batch */
    $variant = $batch->variant;                 
    $item    = optional($variant)->itemMaster;   
@endphp

<div class="container-fluid">

    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Edit Batch Stok</h1>

            @if($item)
                <p class="page-subtitle mb-0">
                    {{ $item->item_code }} — {{ $item->item_name }}<br>
                    @if($variant)
                        <span class="text-muted">
                            Merek: <strong>{{ $variant->brand }}</strong>
                            @if($variant->lot_number)
                                · Lot Varian: <strong>{{ $variant->lot_number }}</strong>
                            @endif
                            @if($variant->expiration_date)
                                · Exp Varian: {{ $variant->expiration_date->format('d-m-Y') }}
                            @endif
                        </span>
                    @endif
                </p>
            @else
                <p class="page-subtitle mb-0">
                    Edit batch ID #{{ $batch->id }}.
                </p>
            @endif
        </div>
    </div>

    <div class="panel-card">
        <form action="{{ route('batches.update', $batch) }}" method="post">
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

                {{-- VARIAN (item_variant_id) --}}
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold">
                        Varian Item <span class="text-danger">*</span>
                    </label>
                    <select
                        name="item_variant_id"
                        class="form-select form-select-sm @error('item_variant_id') is-invalid @enderror">
                        <option value="">Pilih varian…</option>
                        @foreach($variants as $v)
                            @php
                                $vItem = $v->itemMaster;
                                $label = ($vItem ? "[{$vItem->item_code}] {$vItem->item_name}" : "[Item ?]")
                                         .' — '.$v->brand;
                            @endphp
                            <option value="{{ $v->id }}"
                                {{ (string)old('item_variant_id', $batch->item_variant_id) === (string)$v->id ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        Tentukan varian (merek) yang terkait dengan batch ini.
                    </div>
                    @error('item_variant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- NOMOR LOT / BATCH --}}
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold">
                        Nomor Lot / Batch
                    </label>
                    <input type="text"
                           name="lot_number"
                           class="form-control form-control-sm @error('lot_number') is-invalid @enderror"
                           value="{{ old('lot_number', $batch->lot_number) }}"
                           placeholder="misal: REG-GLU-B01">
                    <div class="form-text">
                        Diisi sesuai label pada kemasan batch ini.
                    </div>
                    @error('lot_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- TANGGAL KEDALUWARSA --}}
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold">
                        Tanggal Kedaluwarsa Batch
                    </label>
                    <input type="date"
                           name="expiration_date"
                           class="form-control form-control-sm @error('expiration_date') is-invalid @enderror"
                           value="{{ old('expiration_date', optional($batch->expiration_date)->format('Y-m-d')) }}">
                    <div class="form-text">
                        Kosongkan jika batch ini tidak memiliki tanggal kadaluwarsa.
                    </div>
                    @error('expiration_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mt-4 gap-2">
                <a href="{{ route('batches.index') }}" class="btn btn-outline-secondary btn-outline-soft">
                    <i class="ri-arrow-go-back-line me-1"></i> Batal
                </a>

                <button type="submit" class="btn btn-primary-gradient">
                    <i class="ri-save-3-line me-1"></i> Update Batch
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
