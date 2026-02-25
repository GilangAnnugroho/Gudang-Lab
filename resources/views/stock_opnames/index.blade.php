@extends('layouts.app')
@section('title','Riwayat Stok Opname')

@push('styles')
<style>
    .page-title-wrap {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 24px;
    }
    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        margin: 0;
        line-height: 1.2;
    }
    .page-subtitle {
        font-size: 13px;
        color: #6b7280;
        margin: 4px 0 0;
    }

    .btn-add-item {
        border-radius: 999px;
        padding: 0 24px;
        height: 42px;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: linear-gradient(135deg, #4f46e5, #4338ca);
        border: none;
        color: #fff;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-add-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        color: #fff;
    }
    .btn-add-item i {
        font-size: 18px;
    }

    .item-filters {
        margin-bottom: 20px;
    }

    .form-control-pill {
        height: 42px;
        border-radius: 999px;
        border: 1px solid #e5e7eb;
        background-color: #f9fafb;
        color: #1f2937;
        font-size: 13px;
        padding: 0 20px;
        transition: all 0.2s;
    }
    .form-control-pill:focus {
        background-color: #fff;
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        outline: none;
    }

    .search-wrapper {
        position: relative;
        flex-grow: 1;
    }
    .search-wrapper input {
        padding-left: 42px;
        width: 100%;
    }
    .search-wrapper i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 18px;
        color: #9ca3af;
        pointer-events: none;
    }

    .btn-filter {
        height: 42px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #e5e7eb;
        color: #4b5563;
        font-weight: 600;
        font-size: 13px;
        padding: 0 20px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .btn-filter:hover {
        background: #f3f4f6;
        color: #111827;
        border-color: #d1d5db;
    }
    
    .btn-reset {
        height: 42px;
        border-radius: 999px;
        padding: 0 16px;
        font-size: 13px;
        color: #dc2626;
        border: 1px solid transparent;
        background: transparent;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    .btn-reset:hover {
        background: #fef2f2;
    }

    .btn-print {
        height: 42px;
        border-radius: 999px;
        padding: 0 20px;
        font-size: 13px;
        font-weight: 600;
        color: #fff;
        background: #1f2937; 
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-print:hover {
        background: #374151;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .panel-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 20px;
        border: 1px solid #f3f4f6;
    }
    .table-items {
        font-size: 14px;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-items thead th {
        border-bottom: 2px solid #f3f4f6;
        background: #fff;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #9ca3af;
        padding: 16px;
    }
    .table-items tbody td {
        padding: 16px;
        border-bottom: 1px solid #f9fafb;
        vertical-align: middle;
    }
    .table-items tbody tr:last-child td {
        border-bottom: none;
    }
    .table-items tbody tr:hover {
        background-color: #fcfcfd;
    }

    .item-name { font-weight: 600; color: #111827; font-size: 14px; margin-bottom: 2px; }
    .item-meta { font-size: 12px; color: #6b7280; }
    
    .badge-date {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        background: #f9fafb;
        color: #4b5563;
        border: 1px solid #e5e7eb;
    }
    .badge-date i { margin-right: 6px; color: #9ca3af; }
    .diff-value { font-weight: 700; font-size: 14px; }
    .diff-minus { color: #ef4444; }
    .diff-plus { color: #10b981; }

    .pagination-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-top: 20px;
    }

    @media (max-width: 992px) {
        .page-title-wrap { flex-direction: column; align-items: flex-start; }
        .btn-add-item { width: 100%; }
        .pagination-wrapper { justify-content: center; }
        .filter-group { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Riwayat Stok Opname</h1>
            <p class="page-subtitle">
                Daftar riwayat pemeriksaan fisik stok barang (Audit/Cek Fisik).
            </p>
        </div>
        @if(auth()->user()->role->role_name == 'Kepala Lab')
        <a href="{{ route('stock-opnames.create') }}" class="btn-add-item">
            <i class="ri-clipboard-line"></i>
            <span>Input Stok Opname</span>
        </a>
        @endif
    </div>

    <div class="panel-card">
        
        {{-- FORM FILTER & SEARCH --}}
        <form action="{{ route('stock-opnames.index') }}" method="GET" class="item-filters">
            <div class="row g-2 align-items-center">
                
                {{-- Search Bar (Menggunakan col-lg agar melebar mengisi sisa ruang) --}}
                <div class="col-12 col-lg">
                    <div class="search-wrapper">
                        <i class="ri-search-line"></i>
                        <input type="text"
                               name="q"
                               value="{{ request('q') }}"
                               placeholder="Cari nama barang, merk, atau lot..."
                               class="form-control-pill"
                               autocomplete="off">
                    </div>
                </div>

                {{-- Date Filter (col-auto agar mepet ke kanan) --}}
                <div class="col-6 col-lg-auto">
                    <input type="date" name="start_date" 
                           class="form-control-pill w-100" 
                           value="{{ request('start_date') }}"
                           title="Tanggal Mulai">
                </div>
                <div class="col-6 col-lg-auto">
                    <input type="date" name="end_date" 
                           class="form-control-pill w-100" 
                           value="{{ request('end_date') }}"
                           title="Tanggal Selesai">
                </div>

                {{-- Action Buttons --}}
                <div class="col-12 col-lg-auto d-flex gap-2">
                    <button type="submit" class="btn-filter flex-grow-1 flex-lg-grow-0 justify-content-center">
                        <i class="ri-filter-3-line"></i> Filter
                    </button>
                    
                    {{-- TOMBOL PRINT (Baru) --}}
                    {{-- Mengirim semua parameter GET (filter) ke route print --}}
                    <a href="{{ route('stock-opnames.print', request()->query()) }}" target="_blank" class="btn-print flex-grow-1 flex-lg-grow-0 justify-content-center">
                        <i class="ri-printer-line"></i> Print
                    </a>

                    @if(request('q') || request('start_date') || request('end_date'))
                        <a href="{{ route('stock-opnames.index') }}" class="btn-reset">
                            Reset
                        </a>
                    @endif
                </div>

            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-items">
                <thead>
                    <tr>
                        <th style="width:140px;">Tanggal</th>
                        <th>Barang / Varian</th>
                        <th style="width:160px;">Oleh</th>
                        <th style="width:100px;">Sistem</th>
                        <th style="width:100px;">Fisik</th>
                        <th style="width:100px;">Selisih</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($opnames as $opname)
                    <tr>
                        <td>
                            <span class="badge-date">
                                <i class="ri-calendar-line"></i>
                                {{ $opname->opname_date->format('d/m/Y') }}
                            </span>
                        </td>
                        <td>
                            <div class="item-name">{{ $opname->variant->itemMaster->item_name }}</div>
                            <div class="item-meta">{{ $opname->variant->variant_label }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:28px;height:28px;background:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#6b7280;font-size:14px;">
                                    <i class="ri-user-3-fill"></i>
                                </div>
                                <span class="item-name" style="font-size:13px; margin-bottom:0;">
                                    {{ $opname->user->name }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span style="font-family:'Courier New', monospace; font-size:14px; color:#6b7280;">
                                {{ $opname->system_stock }}
                            </span>
                        </td>
                        <td>
                            <span style="font-family:'Courier New', monospace; font-size:14px; font-weight:700; color:#111827;">
                                {{ $opname->physical_stock }}
                            </span>
                        </td>
                        <td>
                            @php $diff = (int)$opname->difference; @endphp
                            @if($diff < 0)
                                <span class="diff-value diff-minus">{{ $diff }}</span>
                            @elseif($diff > 0)
                                <span class="diff-value diff-plus">+{{ $diff }}</span>
                            @else
                                <span class="badge bg-light text-secondary border px-2 py-1 fw-normal">Sesuai</span>
                            @endif
                        </td>
                        <td>
                            @if($opname->notes)
                                <span class="text-muted fst-italic" style="font-size:13px; display:block; line-height:1.5;">
                                    "{{ $opname->notes }}"
                                </span>
                            @else
                                <span class="text-muted" style="font-size:12px;">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center opacity-50">
                                <i class="ri-folder-open-line" style="font-size: 32px; margin-bottom: 8px;"></i>
                                <span style="font-size: 14px;">Belum ada data stok opname.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if($opnames->hasPages())
            <div class="pagination-wrapper">
                {{ $opnames->links() }}
            </div>
        @endif
    </div>
</div>
@endsection