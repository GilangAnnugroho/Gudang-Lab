@extends('layouts.app')
@section('title','Edit Kategori: '.$category->category_name)

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

    {{-- HEADER TITLE + BACK BUTTON --}}
    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Edit Kategori</h1>
            <p class="page-subtitle">
                Perbarui informasi kategori
                <strong>{{ $category->category_name }}</strong>.
            </p>
        </div>
    </div>

    {{-- FORM PANEL --}}
    <div class="panel-card">

        {{-- BADGE INFO KATEGORI --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <span class="badge bg-light text-muted border" style="border-radius:999px;">
                    Nama Kategori:
                    <span class="fw-semibold ms-1">{{ $category->category_name }}</span>
                </span>
            </div>
        </div>

        <form method="post" action="{{ route('categories.update',$category) }}">
            @csrf
            @method('PUT')

            {{-- VALIDATION MESSAGE --}}
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
                {{-- NAMA KATEGORI --}}
                <div class="col-md-6 col-lg-4">
                    <label class="form-label small fw-semibold">
                        Nama Kategori <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="category_name"
                        class="form-control form-control-sm @error('category_name') is-invalid @enderror"
                        value="{{ old('category_name', $category->category_name) }}"
                        maxlength="150"
                        placeholder="misal: Reagen, Bahan Habis Pakai (BHP), ATK">
                    @error('category_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- DESKRIPSI --}}
                <div class="col-12">
                    <label class="form-label small fw-semibold">
                        Deskripsi
                    </label>
                    <textarea
                        name="description"
                        rows="3"
                        class="form-control form-control-sm @error('description') is-invalid @enderror"
                        placeholder="Contoh: Bahan kimia habis pakai untuk pemeriksaan lab.">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mt-4 gap-2">
                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-outline-soft">
                    <i class="ri-arrow-go-back-line me-1"></i> Batal
                </a>

                <button type="submit" class="btn btn-primary-gradient">
                    <i class="ri-save-3-line me-1"></i> Update Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
