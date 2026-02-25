@extends('layouts.app')
@section('title','Edit Transaksi')

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
    .badge-type{
        border-radius:999px;
        padding:.25rem .7rem;
        font-size:11px;
        font-weight:600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    @php
        $isKeluar   = $transaction->type === 'KELUAR';
        $hasRequest = !is_null($transaction->request_id);

        $baseUnit        = null;
        $variantDefault  = null;
        foreach($variants as $v){
            if((string)$v->id === (string)$transaction->item_variant_id){
                $baseUnit        = $v->base_unit;
                $variantDefault  = $v;
                break;
            }
        }
    @endphp

    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Edit Transaksi</h1>
            <p class="page-subtitle mb-0">
                {{ $transaction->item_code }} — {{ $transaction->item_name }} <br>
                <span class="text-muted">
                    Jenis:
                    <span class="badge-type {{ $isKeluar ? 'bg-danger text-white' : 'bg-success text-white' }}">
                        {{ $transaction->type }}
                    </span>
                    · Merek: <strong>{{ $transaction->brand ?? '—' }}</strong>
                    @if($transaction->lot !== '—')
                        · No. LOT: <strong>{{ $transaction->lot }}</strong>
                    @endif
                    · Exp: {{ $transaction->exp ?? '—' }}
                </span>
                @if($hasRequest)
                    <br>
                    <span class="text-muted">
                        Terkait permintaan:
                        <a href="{{ route('requests.show', $transaction->request_id) }}" target="_blank">
                            #{{ $transaction->request_id }}
                        </a>
                        (transaksi tidak dapat dihapus)
                    </span>
                @endif
            </p>
        </div>
    </div>

    <div class="panel-card">
        <form method="post" action="{{ route('transactions.update', $transaction) }}">
            @csrf
            @method('PUT')

            {{-- supaya required_if:type,KELUAR di validator tetap jalan --}}
            <input type="hidden" name="type" value="{{ $transaction->type }}">

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

            {{-- BARIS 1 --}}
            <div class="row g-3 mb-2">
                {{-- Jenis Transaksi --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Jenis Transaksi
                    </label>
                    <input type="text"
                           class="form-control form-control-sm"
                           value="{{ $transaction->type }}"
                           disabled>
                </div>

                {{-- Tanggal Penerimaan --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Tanggal Penerimaan<span class="text-danger"></span>
                    </label>
                    <input type="date"
                        name="trans_date"
                        class="form-control form-control-sm @error('trans_date') is-invalid @enderror"
                        value="{{ old('trans_date', optional($transaction->trans_date)->format('Y-m-d')) }}">
                    @error('trans_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Nama Barang (terkunci) --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Nama Barang 
                    </label>
                    <select class="form-select form-select-sm" disabled>
                        @foreach($variants as $variant)
                            <option value="{{ $variant->id }}"
                                {{ (string)$transaction->item_variant_id === (string)$variant->id ? 'selected' : '' }}>
                                {{ $variant->item_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Jumlah --}}
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">
                        Jumlah<span class="text-danger"></span>
                    </label>

                    @if($isKeluar)
                        {{-- terkunci untuk KELUAR: tampil disabled + hidden --}}
                        <input type="number"
                               class="form-control form-control-sm"
                               value="{{ old('quantity', $transaction->quantity) }}"
                               disabled>
                        <input type="hidden" name="quantity" value="{{ old('quantity', $transaction->quantity) }}">
                    @else
                        <input type="number"
                               name="quantity"
                               min="1"
                               class="form-control form-control-sm @error('quantity') is-invalid @enderror"
                               value="{{ old('quantity', $transaction->quantity) }}">
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @endif
                </div>
            </div>

            {{-- BARIS 2 --}}
            <div class="row g-3 mb-2">
                {{-- Satuan --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Satuan 
                    </label>
                    <input type="text"
                           class="form-control form-control-sm"
                           value="{{ $baseUnit }}"
                           disabled>
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
                           class="form-control form-control-sm @error('brand') is-invalid @enderror"
                           value="{{ old('brand', $transaction->getRawOriginal('brand')) }}"
                           disabled>
                    @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Nomor LOT + helper batch --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Nomor LOT 
                    </label>

                    @if($isKeluar)
                        {{-- terkunci untuk KELUAR: tampil disabled + hidden --}}
                        <input type="text"
                               class="form-control form-control-sm"
                               value="{{ old('lot_number', $transaction->lot_number) }}"
                               disabled>
                        <input type="hidden" name="lot_number" value="{{ old('lot_number', $transaction->lot_number) }}">

                        <select class="form-select form-select-sm mt-1" disabled>
                            <option>— Batch terkunci untuk transaksi keluar —</option>
                        </select>

                    @else
                        <input type="text"
                               name="lot_number"
                               id="lot_number"
                               class="form-control form-control-sm @error('lot_number') is-invalid @enderror"
                               value="{{ old('lot_number', $transaction->lot_number) }}">
                        @error('lot_number')<div class="invalid-feedback">{{ $message }}</div>@enderror

                        <select id="batch_helper"
                                class="form-select form-select-sm mt-1">
                            <option value="">— Pilih dari batch varian ini —</option>

                            @if($variantDefault && ($variantDefault->lot_number || $variantDefault->expiration_date))
                                @php
                                    $varLot = $variantDefault->lot_number;
                                    $varExp = optional($variantDefault->expiration_date)->format('Y-m-d');
                                    $txExp  = optional($transaction->expiration_date)->format('Y-m-d');
                                    $selectVariantLot = !$transaction->batch_id
                                        && ($transaction->lot_number === $varLot)
                                        && ($txExp === $varExp);
                                @endphp
                                <option value="__variant__"
                                        data-kind="variant-lot"
                                        data-lot="{{ $variantDefault->lot_number }}"
                                        data-exp="{{ $varExp }}"
                                        {{ $selectVariantLot ? 'selected' : '' }}>
                                    {{ $variantDefault->lot_number ?: 'Lot bawaan varian' }}
                                    @if($variantDefault->expiration_date)
                                        — Exp: {{ $variantDefault->expiration_date->format('d-m-Y') }}
                                    @endif
                                </option>
                            @endif

                            @foreach($batches as $batch)
                                <option value="{{ $batch->id }}"
                                        data-lot="{{ $batch->lot_number }}"
                                        data-exp="{{ optional($batch->expiration_date)->format('Y-m-d') }}"
                                        {{ (string)$transaction->batch_id === (string)$batch->id ? 'selected' : '' }}>
                                    {{ $batch->lot_number ?: 'Tanpa Lot' }}
                                    @if($batch->expiration_date)
                                        — Exp: {{ $batch->expiration_date->format('d-m-Y') }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <div class="form-text">
                            Pilih batch untuk mengisi LOT & tanggal kadaluarsa otomatis.
                        </div>
                    @endif
                </div>

                {{-- Tanggal Kadaluarsa --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Tanggal Kadaluarsa 
                    </label>

                    @if($isKeluar)
                        {{-- terkunci untuk KELUAR: tampil disabled + hidden --}}
                        <input type="date"
                               class="form-control form-control-sm"
                               value="{{ old('expiration_date', optional($transaction->expiration_date)->format('Y-m-d')) }}"
                               disabled>
                        <input type="hidden" name="expiration_date" value="{{ old('expiration_date', optional($transaction->expiration_date)->format('Y-m-d')) }}">
                    @else
                        <input type="date"
                               name="expiration_date"
                               id="expiration_date"
                               class="form-control form-control-sm @error('expiration_date') is-invalid @enderror"
                               value="{{ old('expiration_date', optional($transaction->expiration_date)->format('Y-m-d')) }}">
                        @error('expiration_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @endif
                </div>
            </div>

            {{-- BARIS 3 --}}
            <div class="row g-3 mb-2">
                {{-- Ukuran Barang --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">
                        Ukuran Barang
                    </label>
                    <input type="text"
                           name="package_size"
                           class="form-control form-control-sm @error('package_size') is-invalid @enderror"
                           value="{{ old('package_size', $transaction->package_size) }}">
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
                           value="{{ old('storage_condition', $transaction->storage_condition) }}">
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
                            <option value="{{ $id }}" {{ (string)old('supplier_id', $transaction->supplier_id) === (string)$id ? 'selected' : '' }}>
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
                           value="{{ old('invoice_no', $transaction->invoice_no) }}">
                    @error('invoice_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- BARIS 4 --}}
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
                           value="{{ old('price', $transaction->price) }}">
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
                           value="{{ old('tax_amount', $transaction->tax_amount) }}">
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
                           value="{{ old('total_amount', $transaction->total_amount) }}">
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
                        <option value="LUNAS"  {{ old('payment_status', $transaction->payment_status) === 'LUNAS'  ? 'selected' : '' }}>Lunas</option>
                        <option value="HUTANG" {{ old('payment_status', $transaction->payment_status) === 'HUTANG' ? 'selected' : '' }}>Hutang</option>
                    </select>
                    @error('payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            {{-- DETAIL BARANG KELUAR --}}
            @if($isKeluar)
                <div class="mt-2">
                    <div class="section-title">Detail Barang Keluar</div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">
                                Unit Tujuan
                            </label>

                            {{-- terkunci untuk KELUAR --}}
                            <select class="form-select form-select-sm" disabled>
                                <option value="">— Pilih unit —</option>
                                @foreach($units as $id => $name)
                                    <option value="{{ $id }}" {{ (string)old('unit_id', $transaction->unit_id) === (string)$id ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="unit_id" value="{{ old('unit_id', $transaction->unit_id) }}">
                        </div>

                        @if($hasRequest)
                            {{-- request_id terkunci jika sudah terisi --}}
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">
                                    ID Permintaan
                                </label>
                                <input type="text"
                                       class="form-control form-control-sm"
                                       value="#{{ $transaction->request_id }}"
                                       disabled>
                                <input type="hidden" name="request_id" value="{{ $transaction->request_id }}">
                                <div class="form-text">
                                    Transaksi sudah terkait permintaan, ID Permintaan dikunci.
                                </div>
                            </div>
                        @else
                            {{-- request_id boleh diisi jika masih null --}}
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">
                                    ID Permintaan (Opsional)
                                </label>
                                <input type="text"
                                       name="request_id"
                                       class="form-control form-control-sm @error('request_id') is-invalid @enderror"
                                       value="{{ old('request_id', $transaction->request_id) }}"
                                       placeholder="Contoh: 5">
                                @error('request_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">
                                    Boleh diisi jika belum terkait permintaan. Setelah terisi, field ini terkunci.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mt-4 gap-2">
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary btn-outline-soft">
                    <i class="ri-arrow-go-back-line me-1"></i> Batal
                </a>

                <button type="submit" class="btn-primary-gradient">
                    <i class="ri-save-3-line"></i>
                    <span>Update Transaksi</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const batchSelect = document.getElementById('batch_helper');
    const lotInput    = document.getElementById('lot_number');
    const expInput    = document.getElementById('expiration_date');

    function applyBatchSelection(){
        if (!batchSelect) return;
        const opt = batchSelect.options[batchSelect.selectedIndex];
        if (!opt || !opt.value) return;

        const lot = opt.getAttribute('data-lot') || '';
        const exp = opt.getAttribute('data-exp') || '';

        if (lotInput && lot) lotInput.value = lot;
        if (expInput && exp) expInput.value = exp;
    }

    if (batchSelect) {
        batchSelect.addEventListener('change', applyBatchSelection);
        if (batchSelect.value) {
            applyBatchSelection();
        }
    }
});
</script>
@endpush
@endsection
