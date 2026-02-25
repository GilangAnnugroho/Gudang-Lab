@extends('layouts.app')
@section('title','Input Stok Opname')

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
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: opacity 0.2s;
    }
    .btn-primary-gradient:hover {
        color: #fff;
        opacity: 0.9;
    }
    .btn-primary-gradient i{
        font-size:18px;
    }
    
    .btn-outline-soft{
        border-radius:999px;
        font-size:13px;
        padding:.45rem 1.1rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        color: #6b7280;
        border: 1px solid #e5e7eb;
        background: white;
    }
    .btn-outline-soft:hover {
        background: #f9fafb;
        color: #374151;
    }

    .panel-card {
        background: #fff;
        border-radius: 12px; 
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 24px;
        border: 1px solid #f3f4f6;
    }

    .input-readonly-custom {
        background-color: #f8fafc !important;
        border-color: #e2e8f0;
        color: #64748b;
        font-family: 'Courier New', monospace;
        font-weight: 600;
    }
    
    .input-diff {
        font-weight: 800;
        text-align: left;
    }
    .diff-neutral { color: #9ca3af; }
    .diff-plus { color: #10b981; } 
    .diff-minus { color: #ef4444; } 

</style>
@endpush

@section('content')
<div class="container-fluid">

    {{-- HEADER TITLE --}}
    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Input Stok Opname</h1>
            <p class="page-subtitle">
                Formulir pencatatan hasil pemeriksaan fisik barang (Audit Stok).
            </p>
        </div>
    </div>

    {{-- PANEL FORM --}}
    <div class="panel-card">
        <form action="{{ route('stock-opnames.store') }}" method="POST">
            @csrf

            {{-- ERROR HANDLING --}}
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

                {{-- TANGGAL PEMERIKSAAN --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Tanggal Pemeriksaan <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           name="opname_date" 
                           class="form-control form-control-sm @error('opname_date') is-invalid @enderror"
                           value="{{ old('opname_date', date('Y-m-d')) }}" 
                           required>
                    @error('opname_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- PILIH BARANG --}}
                <div class="col-md-8">
                    <label class="form-label small fw-semibold">
                        Pilih Barang / Varian <span class="text-danger">*</span>
                    </label>
                    <select name="item_variant_id" 
                            id="variantSelect" 
                            class="form-select form-select-sm @error('item_variant_id') is-invalid @enderror" 
                            required>
                        <option value="" selected disabled>-- Cari Barang --</option>
                        @foreach($variants as $v)
                            <option value="{{ $v->id }}" 
                                    data-stock="{{ optional($v->stock)->current_quantity ?? 0 }}"
                                    {{ old('item_variant_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->itemMaster->item_name }} — {{ $v->variant_label }}
                            </option>
                        @endforeach
                    </select>
                    @error('item_variant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text" style="font-size: 11px;">
                        Pilih barang untuk memuat data stok sistem saat ini.
                    </div>
                </div>

                <div class="col-12"><hr class="my-1" style="border-top:1px dashed #e5e7eb;"></div>

                {{-- AREA PERHITUNGAN --}}
                
                {{-- Stok Sistem (Readonly) --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Stok Sistem</label>
                    <input type="text" 
                           id="systemStockDisplay" 
                           class="form-control form-control-sm input-readonly-custom" 
                           value="-" 
                           readonly>
                    {{-- Hidden input untuk kalkulasi JS --}}
                    <input type="hidden" id="systemStockInput" value="0">
                </div>

                {{-- Stok Fisik (Input) --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Stok Fisik (Nyata) <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           name="physical_stock" 
                           id="physicalStock" 
                           class="form-control form-control-sm @error('physical_stock') is-invalid @enderror"
                           placeholder="0"
                           value="{{ old('physical_stock') }}"
                           required>
                    @error('physical_stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Selisih (Otomatis) --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Selisih</label>
                    <input type="text" 
                           id="differenceDisplay" 
                           class="form-control form-control-sm input-diff diff-neutral" 
                           value="0" 
                           readonly 
                           style="background-color: #fff;">
                </div>

                {{-- CATATAN --}}
                <div class="col-12">
                    <label class="form-label small fw-semibold">
                        Catatan / Keterangan
                    </label>
                    <textarea name="notes" 
                              class="form-control form-control-sm" 
                              rows="3" 
                              placeholder="Berikan keterangan jika ada selisih (misal: barang pecah, kadaluarsa, atau tidak ditemukan).">{{ old('notes') }}</textarea>
                </div>

            </div>

            {{-- ACTION BUTTONS --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mt-4 gap-2">
                <a href="{{ route('stock-opnames.index') }}" class="btn-outline-soft">
                    <i class="ri-arrow-go-back-line me-1"></i> Batal
                </a>

                <button type="submit" class="btn-primary-gradient">
                    <i class="ri-save-3-line me-1"></i> Simpan Hasil Opname
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT CALCULATOR --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const variantSelect = document.getElementById('variantSelect');
        const systemStockDisplay = document.getElementById('systemStockDisplay');
        const systemStockInput = document.getElementById('systemStockInput');
        const physicalStockInput = document.getElementById('physicalStock');
        const diffDisplay = document.getElementById('differenceDisplay');

        variantSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock');
            systemStockInput.value = stock;
            systemStockDisplay.value = stock; 
            calculateDifference();
            physicalStockInput.focus();
        });

        physicalStockInput.addEventListener('input', calculateDifference);
        function calculateDifference() {
            if(variantSelect.value === "") return;
            const sys = parseInt(systemStockInput.value) || 0;
            const physVal = physicalStockInput.value;
            const phys = physVal === "" ? 0 : parseInt(physVal);
            const diff = phys - sys;
            const sign = diff > 0 ? "+" : "";
            diffDisplay.value = sign + diff;
            diffDisplay.classList.remove('diff-minus', 'diff-plus', 'diff-neutral');

            if(diff < 0) {
                diffDisplay.classList.add('diff-minus');
            } else if(diff > 0) {
                diffDisplay.classList.add('diff-plus');
            } else {
                diffDisplay.classList.add('diff-neutral');
            }
            
            if(physVal === "") {
                diffDisplay.value = "0";
                diffDisplay.className = 'form-control form-control-sm input-diff diff-neutral';
            }
        }
    });
</script>
@endsection