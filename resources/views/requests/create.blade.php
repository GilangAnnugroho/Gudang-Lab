@extends('layouts.app')
@section('title','Buat Permintaan Barang')

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
        color:#ffffff; 
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
        box-shadow:0 14px 30px rgba(15,23,42,.28);
    }

    .btn-primary-gradient:active,
    .btn-primary-gradient:focus:active{
        background:linear-gradient(90deg,#3d38ca,#0a8ab0);
        color:#000000 !important; 
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

    {{-- HEADER --}}
    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Buat Permintaan Barang</h1>
            <p class="page-subtitle mb-0">
                Isi data <strong>header permintaan</strong> dan daftar barang yang diminta dari unit Anda.
            </p>
        </div>
        <!-- <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary btn-outline-soft">
            <i class="ri-arrow-left-line me-1"></i> Kembali 
        </a> -->
    </div>

    <div class="panel-card">
        <form action="{{ route('requests.store') }}" method="post">
            @csrf

            {{-- VALIDASI (bukan flash) --}}
            @if($errors->any())
                <div class="alert alert-warning mb-3">
                    <strong>Periksa kembali input Anda:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- HEADER PERMINTAAN --}}
            <div class="section-title">Header Permintaan</div>
            <div class="row g-3 mb-2">

                {{-- TANGGAL --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Tanggal Permintaan <span class="text-danger">*</span>
                    </label>
                    <input type="date"
                           name="request_date"
                           class="form-control form-control-sm @error('request_date') is-invalid @enderror"
                           value="{{ old('request_date', now()->format('Y-m-d')) }}">
                    @error('request_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- UNIT --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Unit Peminta <span class="text-danger">*</span>
                    </label>
                    <select name="unit_id"
                            class="form-select form-select-sm @error('unit_id') is-invalid @enderror">
                        <option value="">– pilih unit –</option>
                        @foreach($units as $id => $name)
                            <option value="{{ $id }}"
                                {{ (string)old('unit_id', $selectedUnitId) === (string)$id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- PEMINTA --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">
                        Peminta
                    </label>
                    <input type="text"
                           class="form-control form-control-sm"
                           value="{{ auth()->user()->name }}"
                           readonly>
                </div>

            </div>

            {{-- DETAIL ITEM --}}
            <hr class="my-3">

            <div class="section-title">Rincian Barang yang Diminta</div>
            <p class="text-muted small mb-2">
                Tambahkan baris barang yang diminta. Untuk reagen penting, isi catatan agar admin gudang mudah memilih varian/batch yang tepat.
            </p>

            @include('requests._details_table', ['items' => $items])

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mt-4 gap-2">
                <a href="{{ route('requests.index') }}" class="btn btn-outline-secondary btn-outline-soft">
                    <i class="ri-arrow-go-back-line me-1"></i> Batal
                </a>

                <button type="submit" class="btn btn-primary-gradient">
                    <i class="ri-save-3-line me-1"></i> Simpan Permintaan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
