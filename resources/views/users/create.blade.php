@extends('layouts.app')
@section('title','Tambah User')

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
      <h1 class="page-title">Tambah User</h1>
      <p class="page-subtitle mb-0">
        Registrasi akun baru untuk akses ke sistem gudang Labkesda.
      </p>
    </div>
  </div>

  <div class="panel-card">
    <form action="{{ route('users.store') }}" method="post">
      @csrf

      <div class="row g-3">

        {{-- NAMA --}}
        <div class="col-md-6">
          <label class="form-label small fw-semibold">
            Nama Lengkap <span class="text-danger">*</span>
          </label>
          <input type="text"
                 name="name"
                 class="form-control form-control-sm rounded-3 @error('name') is-invalid @enderror"
                 value="{{ old('name') }}"
                 placeholder="misal: Kepala Lab, Petugas Gudang">
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- EMAIL --}}
        <div class="col-md-6">
          <label class="form-label small fw-semibold">
            Email <span class="text-danger">*</span>
          </label>
          <input type="email"
                 name="email"
                 class="form-control form-control-sm rounded-3 @error('email') is-invalid @enderror"
                 value="{{ old('email') }}"
                 placeholder="misal: kepala@labkesda.com">
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- ROLE --}}
        <div class="col-md-4">
          <label class="form-label small fw-semibold">
            Role Akses <span class="text-danger">*</span>
          </label>
          <select name="role_id"
                  class="form-select form-select-sm rounded-3 @error('role_id') is-invalid @enderror">
            <option value="">– pilih role –</option>
            @foreach($roles as $id => $name)
              <option value="{{ $id }}" {{ (string)old('role_id') === (string)$id ? 'selected' : '' }}>
                {{ $name }}
              </option>
            @endforeach
          </select>
          @error('role_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- UNIT --}}
        <div class="col-md-4">
          <label class="form-label small fw-semibold">
            Unit / Ruangan <span class="text-danger">*</span>
          </label>
          <select name="unit_id"
                  class="form-select form-select-sm rounded-3 @error('unit_id') is-invalid @enderror">
            <option value="">– pilih unit –</option>
            @foreach($units as $id => $name)
              <option value="{{ $id }}" {{ (string)old('unit_id') === (string)$id ? 'selected' : '' }}>
                {{ $name }}
              </option>
            @endforeach
          </select>
          @error('unit_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- PASSWORD --}}
        <div class="col-md-4">
          <label class="form-label small fw-semibold">
            Password <span class="text-danger">*</span>
          </label>
          <input type="password"
                 name="password"
                 class="form-control form-control-sm rounded-3 @error('password') is-invalid @enderror"
                 placeholder="minimal 6 karakter">
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        {{-- KONFIRMASI PASSWORD --}}
        <div class="col-md-4">
          <label class="form-label small fw-semibold">
            Konfirmasi Password <span class="text-danger">*</span>
          </label>
          <input type="password"
                 name="password_confirmation"
                 class="form-control form-control-sm rounded-3"
                 placeholder="ulangi password">
        </div>

      </div>

      <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center mt-4 gap-2">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-outline-soft">
          <i class="ri-arrow-go-back-line me-1"></i> Batal
        </a>

        <button type="submit" class="btn btn-primary-gradient">
          <i class="ri-save-3-line me-1"></i> Simpan User
        </button>
      </div>

    </form>
  </div>
</div>
@endsection
