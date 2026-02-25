@extends('layouts.app')
@section('title','Tambah Unit')

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
            <h1 class="page-title">Tambah Unit</h1>
            <p class="page-subtitle">
                Registrasi unit / ruangan baru yang akan menggunakan stok gudang Labkesda.
            </p>
        </div>
    </div>

    <div class="panel-card">
        <form action="{{ route('units.store') }}" method="post">
            @csrf

            <div class="row g-3">
                <div class="col-md-8 col-lg-6">
                    <label class="form-label small fw-semibold">
                        Nama Unit / Ruangan <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="unit_name"
                           class="form-control form-control-sm @error('unit_name') is-invalid @enderror"
                           placeholder="Lab Kimia Klinik, Administrasi, dll."
                           value="{{ old('unit_name') }}">
                    @error('unit_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between mt-4 gap-2">
                <a href="{{ route('units.index') }}"
                   class="btn btn-outline-secondary btn-outline-soft">
                    <i class="ri-arrow-go-back-line me-1"></i> Batal
                </a>

                <button type="submit" class="btn btn-primary-gradient">
                    <i class="ri-save-3-line me-1"></i> Simpan Unit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
