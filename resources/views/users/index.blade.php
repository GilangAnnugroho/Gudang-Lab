@extends('layouts.app')
@section('title','Kelola User')

@php
  $roleLogin = strtolower(optional(auth()->user()->role)->role_name ?? '');
@endphp

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

  .btn-add-item{
      border-radius:999px;
      padding:.5rem 1.2rem;
      font-size:13px;
      display:inline-flex;
      align-items:center;
      gap:6px;
      background:linear-gradient(90deg,#4f46e5,#06b6d4);
      border:none;
      color:#fff;
      box-shadow:0 10px 25px rgba(15,23,42,.25);
  }
  .btn-add-item i{
      font-size:16px;
  }

  .item-filters{
      display:flex;
      flex-wrap:wrap;
      gap:.65rem;
      margin-bottom:12px;
      align-items:center;
  }

  .item-search-box{
      position:relative;
      flex:1 1 260px;
  }
  .item-search-box input{
      width:100%;
      border-radius:999px;
      padding:.55rem 2.4rem .55rem 2.2rem;
      font-size:13px;
      border:1px solid #e5e7eb;
      box-shadow:0 6px 18px rgba(15,23,42,.06);
  }
  .item-search-box i{
      position:absolute;
      left:12px;
      top:50%;
      transform:translateY(-50%);
      font-size:18px;
      color:var(--text-muted);
  }
  .item-search-badge{
      position:absolute;
      right:12px;
      top:50%;
      transform:translateY(-50%);
      font-size:11px;
      color:var(--text-muted);
      display:flex;
      align-items:center;
      gap:4px;
  }

  /* SELECT PILL (Role & Unit) */
  .filter-pill{
      position:relative;
      flex:0 0 220px;
  }
  .filter-pill select{
      width:100%;
      border-radius:999px;
      padding:.55rem 2.0rem .55rem 1.1rem;
      font-size:13px;
      border:1px solid #e5e7eb;
      background:#ffffff;
      box-shadow:0 6px 18px rgba(15,23,42,.06);
      appearance:none;
      -webkit-appearance:none;
      -moz-appearance:none;
  }
  .filter-pill::after{
      content:"\25BE"; /* panah ▼ */
      position:absolute;
      right:14px;
      top:50%;
      transform:translateY(-50%);
      font-size:10px;
      color:#6b7280;
      pointer-events:none;
  }

  /* TOMBOL RESET BULAT */
  .btn-reset-filter{
      display:flex;
      flex-direction:column;
      align-items:center;
      justify-content:center;
      gap:2px;
      min-width:64px;
      height:64px;
      padding:0;
      border-radius:999px;

      background:#ffffff;
      border:1px solid #e5e7eb;
      color:#6b7280;

      font-size:12px;
      font-weight:500;

      box-shadow:0 6px 18px rgba(15,23,42,.06);
      white-space:nowrap;

      transition:
          background-color .18s ease,
          color .18s ease,
          border-color .18s ease,
          box-shadow .18s ease,
          transform .1s ease;
  }
  .btn-reset-filter i{
      font-size:16px;
      line-height:1;
  }
  .btn-reset-filter:hover{
      background:#d1d5db;
      border-color:#cbd5e1;
      color:#374151;
      box-shadow:0 8px 22px rgba(15,23,42,.12);
  }
  .btn-reset-filter:active{
      background:#9ca3af;
      border-color:#9ca3af;
      color:#ffffff;
      transform:translateY(1px);
      box-shadow:0 6px 16px rgba(15,23,42,.22);
  }

  .table-users{
      font-size:13px;
      border-collapse:separate;
      border-spacing:0;
  }
  .table-users thead th{
      border-top:none;
      border-bottom:1px solid #e5e7eb;
      background:#f9fafb;
      font-size:11px;
      text-transform:uppercase;
      letter-spacing:.06em;
      color:var(--text-muted);
  }
  .table-users tbody tr:last-child td{
      border-bottom:0;
  }
  .table-users tbody tr:hover{
      background:#f9fafb;
  }

  .badge-role{
      display:inline-flex;
      align-items:center;
      padding:.2rem .55rem;
      border-radius:999px;
      font-size:11px;
      font-weight:500;
      background:#eef2ff;
      color:#4f46e5;
  }
  .badge-unit{
      display:inline-flex;
      align-items:center;
      padding:.2rem .55rem;
      border-radius:999px;
      font-size:11px;
      font-weight:500;
      background:#ecfdf3;
      color:#15803d;
  }

  .aksi-gap{
      display:flex;
      justify-content:flex-end;
      gap:.35rem;
  }
  .btn-aksi-edit{
      background:rgba(234,179,8,.08);
      color:#ca8a04;
      border:1px solid rgba(234,179,8,.45);
  }
  .btn-aksi-edit:hover{ background:rgba(234,179,8,.16); }

  .btn-aksi-delete{
      background:rgba(248,113,113,.08);
      color:#dc2626;
      border:1px solid rgba(248,113,113,.45);
  }
  .btn-aksi-delete:hover{ background:rgba(248,113,113,.16); }

  .pagination-wrapper{
      display:flex;
      justify-content:flex-end;
      align-items:center;
      margin-top:10px;
  }

  @media (max-width: 767.98px){
      .page-title-wrap{
          flex-direction:column;
          align-items:flex-start;
      }
      .btn-add-item{
          width:100%;
          justify-content:center;
      }
      .item-filters{
          align-items:stretch;
      }
      .filter-pill{
          flex:1 1 100%;
      }
      .btn-reset-filter{
          width:100%;
          height:44px;
          flex-direction:row;
          gap:6px;
          margin-top:4px;
      }
      .pagination-wrapper{
          justify-content:center;
      }
  }
</style>
@endpush

@section('content')
<div class="container-fluid">

  {{-- HEADER TITLE + BUTTON --}}
  <div class="page-title-wrap">
    <div>
      <h1 class="page-title">User & Role</h1>
      <p class="page-subtitle">
        Manajemen akun pengguna, role, dan unit akses sistem gudang Labkesda.
      </p>
    </div>

    @if($roleLogin === 'super admin')
      <a href="{{ route('users.create') }}" class="btn-add-item">
        <i class="ri-add-circle-line"></i>
        <span>Tambah User</span>
      </a>
    @endif
  </div>

  <div class="panel-card">

    {{-- FILTER BAR --}}
    <form method="get" class="mb-2">
      <div class="item-filters">
        {{-- Search --}}
        <div class="item-search-box">
          <i class="ri-search-line"></i>
          <input type="text"
                 name="q"
                 value="{{ $search }}"
                 class="item-search-input form-control"
                 placeholder="Cari nama atau email user…">
          @if($search)
            <span class="item-search-badge">
              <span>Pencarian:</span>
              <span class="text-xs">“{{ \Illuminate\Support\Str::limit($search,18) }}”</span>
            </span>
          @endif
        </div>

        {{-- Role Filter --}}
        <div class="filter-pill">
          <select name="role_id" onchange="this.form.submit()">
            <option value="">Semua Role</option>
            @foreach($roles as $id => $name)
              <option value="{{ $id }}" {{ (string)$roleId === (string)$id ? 'selected' : '' }}>
                {{ $name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Unit Filter --}}
        <div class="filter-pill">
          <select name="unit_id" onchange="this.form.submit()">
            <option value="">Semua Unit</option>
            @foreach($units as $id => $name)
              <option value="{{ $id }}" {{ (string)$unitId === (string)$id ? 'selected' : '' }}>
                {{ $name }}
              </option>
            @endforeach
          </select>
        </div>

        @if($search || $roleId || $unitId)
          <button type="button"
                  onclick="window.location='{{ route('users.index') }}'"
                  class="btn-reset-filter">
            <i class="ri-refresh-line"></i>
            <span>Reset</span>
          </button>
        @endif
      </div>
    </form>

    {{-- TABEL --}}
    <div class="table-responsive">
      <table class="table table-users align-middle mb-0">
        <thead>
          <tr>
            <th style="width:40px" class="text-center">#</th>
            <th>Nama</th>
            <th>Email</th>
            <th style="width:140px">Role</th>
            <th style="width:180px">Unit</th>
            <th style="width:160px">Dibuat</th>
            <th style="width:160px" class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $i => $u)
            <tr>
              <td class="align-middle text-center">
                {{ $users->firstItem() + $i }}
              </td>

              <td class="align-middle">
                <div class="fw-semibold">{{ $u->name }}</div>
              </td>

              <td class="align-middle">
                {{ $u->email }}
              </td>

              <td class="align-middle">
                @if(optional($u->role)->role_name)
                  <span class="badge-role">
                    {{ $u->role->role_name }}
                  </span>
                @else
                  <span class="text-muted" style="font-size:11px;">-</span>
                @endif
              </td>

              <td class="align-middle">
                @if(optional($u->unit)->unit_name)
                  <span class="badge-unit">
                    {{ $u->unit->unit_name }}
                  </span>
                @else
                  <span class="text-muted" style="font-size:11px;">-</span>
                @endif
              </td>

              <td class="align-middle">
                {{ $u->created_at?->format('d-m-Y H:i') ?? '—' }}
              </td>

              <td class="align-middle">
                <div class="aksi-gap">
                  <a href="{{ route('users.edit', $u) }}"
                     class="btn-aksi btn-aksi--sq btn-aksi-edit"
                     title="Edit user">
                    <i class="ri-pencil-line"></i>
                  </a>

                  {{-- Jangan tampilkan tombol hapus untuk akun sendiri --}}
                  @if(auth()->id() !== $u->id)
                    <form action="{{ route('users.destroy', $u) }}"
                          method="post"
                          class="form-delete-user"
                          data-user-name="{{ $u->name }}">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="btn-aksi btn-aksi--sq btn-aksi-delete"
                              title="Hapus user">
                        <i class="ri-delete-bin-line"></i>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">
                Belum ada user.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- PAGINATION --}}
    @if($users->hasPages())
      <div class="pagination-wrapper">
        {{ $users->links() }}
      </div>
    @endif

  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.form-delete-user').forEach(function (form) {
          form.addEventListener('submit', function (e) {
              const name = this.dataset.userName || 'user ini';
              const ok = confirm('Yakin ingin menghapus user "' + name + '" ?\nAksi ini tidak dapat dibatalkan.');
              if (!ok) {
                  e.preventDefault();
              }
          });
      });
  });
</script>
@endpush
