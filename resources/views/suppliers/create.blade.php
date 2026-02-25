@extends('layouts.app')
@section('title','Tambah Rekanan')

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
            <h1 class="page-title">Tambah Rekanan</h1>
            <p class="page-subtitle mb-0">
                Daftarkan pemasok reagen, BHP, dan ATK yang bekerja sama dengan Labkesda.
            </p>
        </div>

        <!-- <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-outline-soft">
            <i class="ri-arrow-left-line me-1"></i> Kembali ke daftar
        </a> -->
    </div>

    <div class="panel-card">
        <form method="post" action="{{ route('suppliers.store') }}">
            @csrf

            <div class="row g-3">
                {{-- NAMA SUPPLIER --}}
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">
                        Nama Rekanan <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="supplier_name"
                           class="form-control form-control-sm rounded-3 @error('supplier_name') is-invalid @enderror"
                           value="{{ old('supplier_name') }}"
                           placeholder="misal: PT Reagen Medika Sejahtera">
                    @error('supplier_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- CONTACT PERSON --}}
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">
                        Contact Person
                    </label>
                    <input type="text"
                           name="contact_person"
                           class="form-control form-control-sm rounded-3 @error('contact_person') is-invalid @enderror"
                           value="{{ old('contact_person') }}"
                           placeholder="misal: Bapak Andi, Ibu Sari">
                    @error('contact_person')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- TELEPON --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Telepon / WhatsApp
                    </label>
                    <input type="text"
                           name="phone"
                           class="form-control form-control-sm rounded-3 @error('phone') is-invalid @enderror"
                           value="{{ old('phone') }}"
                           placeholder="misal: 0812-3456-7890">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- EMAIL (opsional, kalau di DB tidak ada juga aman, cukup diabaikan controller) --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Email
                    </label>
                    <input type="email"
                           name="email"
                           class="form-control form-control-sm rounded-3 @error('email') is-invalid @enderror"
                           value="{{ old('email') }}"
                           placeholder="misal: sales@rekanan.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ALAMAT --}}
                <div class="col-md-12">
                    <label class="form-label small fw-semibold">
                        Alamat
                    </label>
                    <textarea
                        name="address"
                        rows="3"
                        class="form-control form-control-sm rounded-3 @error('address') is-invalid @enderror"
                        placeholder="Alamat lengkap kantor / gudang rekanan">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mt-4 gap-2">
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-outline-soft">
                    <i class="ri-arrow-go-back-line me-1"></i> Batal
                </a>

                <button type="submit" class="btn btn-primary-gradient">
                    <i class="ri-save-3-line me-1"></i> Simpan Rekanan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
