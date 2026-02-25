@extends('layouts.app')
@section('title','Tambah Batch')

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
            <h1 class="page-title">Tambah Batch</h1>
            <p class="page-subtitle">
                Tambahkan data <strong>batch / lot</strong> untuk varian item tertentu.
                Jumlah masuk, keluar, dan stok batch akan mengikuti transaksi yang memakai batch ini.
            </p>
        </div>
    </div>

    <div class="panel-card">
        <form action="{{ route('batches.store') }}" method="post">
            @csrf

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

                {{-- ITEM --}}
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold">
                        Item <span class="text-danger">*</span>
                    </label>
                    <select id="item_select"
                            class="form-select form-select-sm">
                        <option value="">Pilih item…</option>
                        @foreach($items as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        Pilih item terlebih dahulu untuk memudahkan memilih variant.
                    </div>
                </div>

                {{-- VARIANT --}}
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold">
                        Variant (Merek) <span class="text-danger">*</span>
                    </label>
                    <select name="item_variant_id"
                            id="variant_select"
                            class="form-select form-select-sm @error('item_variant_id') is-invalid @enderror">
                        <option value="">Pilih variant…</option>
                        @foreach($variants as $v)
                            <option value="{{ $v->id }}"
                                    data-item="{{ $v->item_master_id }}"
                                    {{ (string)old('item_variant_id') === (string)$v->id ? 'selected' : '' }}>
                                {{ $v->item->item_code ?? '-' }} — {{ $v->item->item_name ?? '-' }} — {{ $v->brand }}
                            </option>
                        @endforeach
                    </select>
                    @error('item_variant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- LOT --}}
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold">
                        Lot / Batch
                    </label>
                    <input type="text"
                           name="lot_number"
                           class="form-control form-control-sm @error('lot_number') is-invalid @enderror"
                           value="{{ old('lot_number') }}"
                           placeholder="misal: L2301A, BATCH-2025-01">
                    @error('lot_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- EXP --}}
                <div class="col-md-6 col-lg-3">
                    <label class="form-label small fw-semibold">
                        Tanggal Kedaluwarsa
                    </label>
                    <input type="date"
                           name="expiration_date"
                           class="form-control form-control-sm @error('expiration_date') is-invalid @enderror"
                           value="{{ old('expiration_date') }}">
                    @error('expiration_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mt-4 gap-2">
                <a href="{{ route('batches.index') }}" class="btn btn-outline-secondary btn-outline-soft">
                    <i class="ri-arrow-go-back-line me-1"></i> Batal
                </a>

                <button type="submit" class="btn-primary-gradient">
                    <i class="ri-save-3-line me-1"></i> Simpan Batch
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JS filter variant by item --}}
@push('scripts')
<script>
    (function() {
        const itemSelect    = document.getElementById('item_select');
        const variantSelect = document.getElementById('variant_select');

        if (!itemSelect || !variantSelect) return;

        const options = Array.from(variantSelect.options);

        itemSelect.addEventListener('change', function () {
            const itemId = this.value;
            variantSelect.innerHTML = '';

            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = 'Pilih variant…';
            variantSelect.appendChild(placeholder);

            options.forEach(opt => {
                const optItem = opt.getAttribute('data-item');
                if (!itemId || !optItem || optItem === itemId) {
                    variantSelect.appendChild(opt);
                }
            });
        });
    })();
</script>
@endpush
@endsection
