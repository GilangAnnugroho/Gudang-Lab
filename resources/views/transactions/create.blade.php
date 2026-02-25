@extends('layouts.app')
@section('title','Tambah Transaksi')

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
        transition:all .18s ease-in-out;
        display:inline-flex;
        align-items:center;
        gap:6px;
    }
    .btn-primary-gradient i{
        font-size:18px;
    }
    .btn-primary-gradient:hover{
        background:linear-gradient(90deg,#6054ff,#13c0df);
        color:#ffffff;
        box-shadow:0 14px 30px rgba(15,23,42,.25);
    }
    .btn-primary-gradient:active{
        background:linear-gradient(90deg,#3d38ca,#0a8ab0);
        color:#e8e8e8;
        box-shadow:0 6px 15px rgba(15,23,42,.22);
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

    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Tambah Transaksi</h1>
            <p class="page-subtitle mb-0">
                Pencatatan <strong>barang masuk / keluar</strong> dengan urutan field mengikuti buku pencatatan Labkesda.
            </p>
        </div>
    </div>

    <div class="panel-card">
        <form method="post" action="{{ route('transactions.store') }}">
            @csrf

            {{-- VALIDASI (tanpa flash sukses) --}}
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

            {{-- BARIS 1: Jenis, Tanggal, Nama Barang, Jumlah --}}
            <div class="row g-3 mb-2">
                {{-- Jenis Transaksi --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Jenis Transaksi <span class="text-danger">*</span>
                    </label>
                    <select name="type"
                            id="trans-type"
                            class="form-select form-select-sm @error('type') is-invalid @enderror">
                        <option value="MASUK"  {{ old('type','MASUK') === 'MASUK'  ? 'selected' : '' }}>Masuk</option>
                        <option value="KELUAR" {{ old('type') === 'KELUAR' ? 'selected' : '' }}>Keluar</option>
                    </select>
                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Tanggal Penerimaan --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Tanggal Penerimaan <span class="text-danger">*</span>
                    </label>
                    <input type="date"
                           name="trans_date"
                           class="form-control form-control-sm @error('trans_date') is-invalid @enderror"
                           value="{{ old('trans_date', now()->format('Y-m-d')) }}">
                    @error('trans_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Nama Barang --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Nama Barang <span class="text-danger">*</span>
                    </label>
                    <select name="item_variant_id"
                            id="item_variant_id"
                            class="form-select form-select-sm @error('item_variant_id') is-invalid @enderror">
                        <option value="">— Pilih barang —</option>
                        @foreach($variants as $variant)
                            <option value="{{ $variant->id }}"
                                data-unit="{{ $variant->base_unit }}"
                                data-brand="{{ $variant->brand }}"
                                data-lot="{{ $variant->lot_number }}"
                                data-exp="{{ optional($variant->expiration_date)->format('Y-m-d') }}"
                                {{ (string)old('item_variant_id') === (string)$variant->id ? 'selected' : '' }}>
                                {{ $variant->item_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('item_variant_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Jumlah --}}
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        Jumlah <span class="text-danger">*</span>
                    </label>
                    <input type="number"
                           name="quantity"
                           min="1"
                           class="form-control form-control-sm @error('quantity') is-invalid @enderror"
                           value="{{ old('quantity',1) }}">
                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- BARIS 2: Satuan, Merek, Nomor LOT, Tanggal Kedaluwarsa --}}
            <div class="row g-3 mb-2">
                {{-- Satuan --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Satuan
                    </label>
                    <input type="text"
                           id="base_unit"
                           class="form-control form-control-sm"
                           placeholder="Otomatis dari master"
                           readonly>
                    <div class="form-text">
                        Mengikuti satuan pada master item.
                    </div>
                </div>

                {{-- Merek --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Merek
                    </label>
                    <input type="text"
                           name="brand"
                           id="brand"
                           class="form-control form-control-sm @error('brand') is-invalid @enderror"
                           value="{{ old('brand') }}"
                           placeholder="Kosongkan untuk pakai merek varian">
                    @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Nomor LOT + helper batch --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Nomor LOT
                    </label>
                    <input type="text"
                           name="lot_number"
                           id="lot_number"
                           class="form-control form-control-sm @error('lot_number') is-invalid @enderror"
                           value="{{ old('lot_number') }}">
                    <div class="form-text">
                        Bisa diketik manual atau pilih dari daftar batch di bawah.
                    </div>
                    @error('lot_number')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <select id="batch_helper"
                            class="form-select form-select-sm mt-1">
                        <option value="">— Pilih dari batch varian ini —</option>
                        @foreach($batches as $batch)
                            <option value="{{ $batch->id }}"
                                    data-variant="{{ $batch->item_variant_id }}"
                                    data-lot="{{ $batch->lot_number }}"
                                    data-exp="{{ optional($batch->expiration_date)->format('Y-m-d') }}">
                                {{ $batch->lot_number ?: 'Tanpa Lot' }}
                                @if($batch->expiration_date)
                                    — Exp: {{ $batch->expiration_date->format('d-m-Y') }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">
                        Jika dipilih, LOT & tanggal kadaluarsa akan terisi otomatis.
                    </div>
                </div>

                {{-- Tanggal Kadaluarsa --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Tanggal Kadaluarsa
                    </label>
                    <input type="date"
                           name="expiration_date"
                           id="expiration_date"
                           class="form-control form-control-sm @error('expiration_date') is-invalid @enderror"
                           value="{{ old('expiration_date') }}">
                    <div class="form-text">
                        Bisa ikut dari batch / varian.
                    </div>
                    @error('expiration_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- BARIS 3: Ukuran, Suhu, Rekanan, No Faktur --}}
            <div class="row g-3 mb-2">
                {{-- Ukuran Barang --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Ukuran Barang
                    </label>
                    <input type="text"
                           name="package_size"
                           class="form-control form-control-sm @error('package_size') is-invalid @enderror"
                           value="{{ old('package_size') }}"
                           placeholder="misal: 20 test, 2×3 mL, 3×250 mL">
                    @error('package_size')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Suhu Barang --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Suhu Barang
                    </label>
                    <input type="text"
                           name="storage_condition"
                           class="form-control form-control-sm @error('storage_condition') is-invalid @enderror"
                           value="{{ old('storage_condition') }}"
                           placeholder="2–8°C / suhu ruang, dll">
                    @error('storage_condition')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Nama Rekanan --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Nama Rekanan
                    </label>
                    <select name="supplier_id"
                            class="form-select form-select-sm @error('supplier_id') is-invalid @enderror">
                        <option value="">— Pilih rekanan —</option>
                        @foreach($suppliers as $id => $name)
                            <option value="{{ $id }}" {{ (string)old('supplier_id') === (string)$id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Nomor Faktur --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Nomor Faktur
                    </label>
                    <input type="text"
                           name="invoice_no"
                           class="form-control form-control-sm @error('invoice_no') is-invalid @enderror"
                           value="{{ old('invoice_no') }}">
                    @error('invoice_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- BARIS 4: Harga, Pajak, Total, Ket Pembayaran --}}
            <div class="row g-3 mb-3">
                {{-- Harga Barang --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Harga Barang
                    </label>
                    <input type="number"
                           step="0.01"
                           name="price"
                           class="form-control form-control-sm @error('price') is-invalid @enderror"
                           value="{{ old('price') }}">
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Pajak --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Pajak
                    </label>
                    <input type="number"
                           step="0.01"
                           name="tax_amount"
                           class="form-control form-control-sm @error('tax_amount') is-invalid @enderror"
                           value="{{ old('tax_amount') }}">
                    @error('tax_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Harga + Pajak --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Harga + Pajak
                    </label>
                    <input type="number"
                           step="0.01"
                           name="total_amount"
                           class="form-control form-control-sm @error('total_amount') is-invalid @enderror"
                           value="{{ old('total_amount') }}">
                    @error('total_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Keterangan Pembayaran --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Keterangan Pembayaran
                    </label>
                    <select name="payment_status"
                            class="form-select form-select-sm @error('payment_status') is-invalid @enderror">
                        <option value="">— Pilih —</option>
                        <option value="LUNAS"  {{ old('payment_status') === 'LUNAS'  ? 'selected' : '' }}>Lunas</option>
                        <option value="HUTANG" {{ old('payment_status') === 'HUTANG' ? 'selected' : '' }}>Hutang</option>
                    </select>
                    @error('payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- DETAIL BARANG KELUAR --}}
            <div class="mt-2 trans-section-keluar">
                <div class="section-title">Detail Barang Keluar</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">
                            Nama Rekanan / Unit Tujuan <span class="text-danger">*</span>
                        </label>
                        <select name="unit_id"
                                class="form-select form-select-sm @error('unit_id') is-invalid @enderror">
                            <option value="">— Pilih unit —</option>
                            @foreach($units as $id => $name)
                                <option value="{{ $id }}" {{ (string)old('unit_id') === (string)$id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">
                            ID Permintaan (Opsional)
                        </label>
                        <input type="text"
                               name="request_id"
                               class="form-control form-control-sm @error('request_id') is-invalid @enderror"
                               value="{{ old('request_id') }}"
                               placeholder="Diisi jika terkait permintaan gudang">
                        @error('request_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mt-4 gap-2">
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-outline-soft">
                    <i class="ri-arrow-go-back-line me-1"></i> Batal
                </a> 

                <button type="submit" class="btn btn-primary-gradient">
                    <i class="ri-save-3-line"></i>
                    <span>Simpan Transaksi</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectType    = document.getElementById('trans-type');
    const sectionKeluar = document.querySelector('.trans-section-keluar');
    const variantSelect = document.getElementById('item_variant_id');
    const unitInput     = document.getElementById('base_unit');
    const brandInput    = document.getElementById('brand');
    const lotInput      = document.getElementById('lot_number');
    const expInput      = document.getElementById('expiration_date');
    const batchSelect   = document.getElementById('batch_helper');

    const batchOptionsAll = batchSelect ? Array.from(batchSelect.options) : [];

    function toggleSections(){
        if (!selectType || !sectionKeluar) return;
        const type = selectType.value;
        sectionKeluar.style.display = (type === 'MASUK') ? 'none' : '';
    }

    function applyVariantData(){
        if (!variantSelect) return;
        const opt = variantSelect.options[variantSelect.selectedIndex];
        if (!opt) return;

        const unit  = opt.getAttribute('data-unit') || '';
        const brand = opt.getAttribute('data-brand') || '';
        const lot   = opt.getAttribute('data-lot') || '';
        const exp   = opt.getAttribute('data-exp') || '';

        if (unitInput)  unitInput.value  = unit;
        if (brandInput) brandInput.value = brand;
        if (lotInput)   lotInput.value   = lot;
        if (batchSelect) batchSelect.value = '';
        if (expInput)   expInput.value   = exp || '';
    }

    function filterBatchOptionsForVariant(){
        if (!batchSelect) return;

        const currentVariantId = variantSelect ? variantSelect.value : '';
        const varLotOpt = batchSelect.querySelector('option[data-kind="variant-lot"]');

        batchOptionsAll.forEach(function(opt){
            if (!opt.value) {
                opt.hidden = false;
                return;
            }

            if (opt.getAttribute('data-kind') === 'variant-lot') {
                return;
            }

            const optVar = opt.getAttribute('data-variant') || '';
            opt.hidden = currentVariantId && optVar !== currentVariantId;
        });

        batchSelect.value = '';
    }

    function ensureVariantBatchOption(){
        if (!batchSelect || !variantSelect) return;

        const currentVariantId = variantSelect.value;
        const optVariant = variantSelect.options[variantSelect.selectedIndex];
        let varLotOpt = batchSelect.querySelector('option[data-kind="variant-lot"]');

        if (!currentVariantId || !optVariant) {
            if (varLotOpt) varLotOpt.remove();
            return;
        }

        const vLot = optVariant.getAttribute('data-lot') || '';
        const vExp = optVariant.getAttribute('data-exp') || '';

        if (!vLot && !vExp) {
            if (varLotOpt) varLotOpt.remove();
            return;
        }

        if (!varLotOpt) {
            varLotOpt = document.createElement('option');
            varLotOpt.setAttribute('data-kind','variant-lot');
            const firstReal = batchSelect.options[1] || null;
            batchSelect.insertBefore(varLotOpt, firstReal);
        }

        varLotOpt.value        = '__variant__';
        varLotOpt.dataset.lot  = vLot;
        varLotOpt.dataset.exp  = vExp;

        let label = vLot || 'Lot bawaan varian';
        if (vExp) {
            const expDate = new Date(vExp);
            if (!isNaN(expDate)) {
                const d = String(expDate.getDate()).padStart(2,'0');
                const m = String(expDate.getMonth()+1).padStart(2,'0');
                const y = expDate.getFullYear();
                label += ' — Exp: ' + d + '-' + m + '-' + y;
            }
        }
        varLotOpt.textContent = label;
        varLotOpt.hidden = false;
    }

    function applyBatchSelection(){
        if (!batchSelect) return;
        const opt = batchSelect.options[batchSelect.selectedIndex];
        if (!opt || !opt.value) return;

        const lot = opt.getAttribute('data-lot') || '';
        const exp = opt.getAttribute('data-exp') || '';

        if (lotInput && lot) lotInput.value = lot;
        if (expInput && exp) expInput.value = exp;
    }

    if (selectType){
        selectType.addEventListener('change', toggleSections);
        toggleSections();
    }

    if (variantSelect) {
        variantSelect.addEventListener('change', function () {
            applyVariantData();          
            filterBatchOptionsForVariant();
            ensureVariantBatchOption();  
        });

        applyVariantData();
        filterBatchOptionsForVariant();
        ensureVariantBatchOption();
    }

    if (batchSelect) {
        batchSelect.addEventListener('change', applyBatchSelection);
    }
});
</script>
@endpush
@endsection
