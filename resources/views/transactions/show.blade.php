@extends('layouts.app')
@section('title','Detail Transaksi')

@push('styles')
<style>
    .page-title{
        font-size:22px;
        font-weight:700;
    }
    .page-subtitle{
        font-size:12px;
        color:var(--text-muted);
        line-height:1.4;
    }
    .panel-card{
        background:#ffffff;
        border-radius:18px;
        border:1px solid #e5e7eb;
        padding:16px 18px 18px;
        box-shadow:0 10px 30px rgba(15,23,42,.06);
    }
    .badge-type{
        border-radius:999px;
        padding:.25rem .7rem;
        font-size:11px;
        font-weight:600;
    }
    .label-inline{
        font-size:12px;
        font-weight:600;
        color:#6b7280;
        text-transform:uppercase;
        letter-spacing:.06em;
    }
    .btn-outline-soft{
        border-radius:999px;
        font-size:13px;
        padding:.45rem 1.1rem;
    }
    .btn-aksi-delete[disabled]{
        opacity:.55;
        cursor:not-allowed;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    @php
        use Carbon\Carbon;
        use Carbon\Exceptions\InvalidFormatException;

        $role      = strtolower(optional(auth()->user()->role)->role_name ?? '');
        $canCrud   = in_array($role, ['super admin','admin gudang']);
        $isKeluar  = $transaction->type === 'KELUAR';
        $hasReq    = !is_null($transaction->request_id);

        $batch      = optional($transaction)->batch;
        $variant    = optional($transaction)->variant;
        $itemMaster = optional($variant)->itemMaster;

        $itemCode   = $transaction->item_code ?? ($itemMaster->item_code ?? null);
        $itemName   = $transaction->item_name ?? ($itemMaster->item_name ?? null);
        $baseUnit   = optional($itemMaster)->base_unit;

        // LOT: urutan prioritas -> kolom transaksi -> batch -> varian
        $lotDisplay = $transaction->lot
            ?? $transaction->lot_number
            ?? optional($batch)->lot_number
            ?? optional($variant)->lot_number
            ?? '—';

        // EXP: urutan prioritas -> kolom transaksi -> batch -> varian
        $expSource = $transaction->exp
            ?? $transaction->expiration_date
            ?? optional($batch)->expiration_date
            ?? optional($variant)->expiration_date;

        if ($expSource instanceof Carbon) {
            $expDisplay = $expSource->format('d-m-Y');
        } elseif ($expSource && $expSource !== '—') {
            try {
                $expDisplay = Carbon::parse($expSource)->format('d-m-Y');
            } catch (InvalidFormatException $e) {
                // Kalau ada data lama yang aneh (misal "—"), tampilkan mentah
                $expDisplay = (string) $expSource;
            }
        } else {
            $expDisplay = null;
        }

        // Tanggal transaksi
        $transSource = $transaction->trans_date;

        if ($transSource instanceof Carbon) {
            $transDateDisplay = $transSource->format('d-m-Y');
        } elseif ($transSource && $transSource !== '—') {
            try {
                $transDateDisplay = Carbon::parse($transSource)->format('d-m-Y');
            } catch (InvalidFormatException $e) {
                $transDateDisplay = (string) $transSource;
            }
        } else {
            $transDateDisplay = '—';
        }
    @endphp

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
        <div>
            <h1 class="page-title mb-1">Detail Transaksi</h1>
            <p class="page-subtitle mb-0">
                {{ $itemCode }} — {{ $itemName }} <br>
                Merek: <strong>{{ $transaction->brand ?? '—' }}</strong>
                @if($lotDisplay && $lotDisplay !== '—')
                    · No. LOT: <strong>{{ $lotDisplay }}</strong>
                @endif
                · Exp: {{ $expDisplay ?? '—' }}
            </p>
        </div>

        <div class="text-end">
            <span class="badge-type {{ $isKeluar ? 'bg-danger text-white' : 'bg-success text-white' }}">
                {{ $transaction->type }}
            </span>
            <div class="mt-2 d-flex flex-wrap gap-2 justify-content-end">
                <a href="{{ route('transactions.index', ['type' => $transaction->type]) }}"
                   class="btn btn-outline-secondary btn-outline-soft btn-sm">
                    <i class="ri-arrow-left-line me-1"></i> Kembali
                </a>

                @if($canCrud)
                     <!-- <a href="{{ route('transactions.edit',$transaction) }}"
                       class="btn btn-outline-primary btn-outline-soft btn-sm">
                        <i class="ri-edit-2-line me-1"></i> Edit
                    </a> -->
                @endif
            </div>
        </div>
    </div>

    <div class="panel-card">
        <div class="row mb-2">
            {{-- KOLOM 1: Jenis, Tanggal, Nama Barang, Jumlah --}}
            <div class="col-md-3">
                <div class="mb-2">
                    <div class="label-inline">Jenis Transaksi</div>
                    <div>{{ $transaction->type }}</div>
                </div>
                <div class="mb-2">
                    <div class="label-inline">Tanggal Penerimaan</div>
                    <div>{{ $transDateDisplay }}</div>
                </div>
                <div class="mb-2">
                    <div class="label-inline">Nama Barang</div>
                    <div>{{ $itemName }}</div>
                </div>
                <div class="mb-2">
                    <div class="label-inline">Jumlah</div>
                    <div>{{ number_format($transaction->quantity,0,',','.') }}</div>
                </div>
            </div>

            {{-- KOLOM 2: Satuan, Merek, LOT, Exp --}}
            <div class="col-md-3">
                <div class="mb-2">
                    <div class="label-inline">Satuan</div>
                    <div>{{ $baseUnit ?? '—' }}</div>
                </div>
                <div class="mb-2">
                    <div class="label-inline">Merek</div>
                    <div>{{ $transaction->brand ?? '—' }}</div>
                </div>
                <div class="mb-2">
                    <div class="label-inline">Nomor LOT</div>
                    <div>{{ $lotDisplay ?? '—' }}</div>
                </div>
                <div class="mb-2">
                    <div class="label-inline">Tanggal Kadaluarsa</div>
                    <div>{{ $expDisplay ?? '—' }}</div>
                </div>
            </div>

            {{-- KOLOM 3: Ukuran, Suhu, Rekanan/Unit, No Faktur --}}
            <div class="col-md-3">
                <div class="mb-2">
                    <div class="label-inline">Ukuran Barang</div>
                    <div>{{ $transaction->package_size ?? '—' }}</div>
                </div>
                <div class="mb-2">
                    <div class="label-inline">Suhu Barang</div>
                    <div>{{ $transaction->storage_condition ?: '—' }}</div>
                </div>

                @if(!$isKeluar)
                    <div class="mb-2">
                        <div class="label-inline">Nama Rekanan</div>
                        <div>{{ $transaction->supplier_name ?? optional($transaction->supplier)->supplier_name ?? '—' }}</div>
                    </div>
                @else
                    <div class="mb-2">
                        <div class="label-inline">Nama Rekanan / Unit Tujuan</div>
                        <div>{{ $transaction->unit_name ?? optional($transaction->unit)->unit_name ?? '—' }}</div>
                    </div>
                @endif

                <div class="mb-2">
                    <div class="label-inline">Nomor Faktur</div>
                    <div>{{ $transaction->invoice_no ?: '—' }}</div>
                </div>
            </div>

            {{-- KOLOM 4: Harga, Pajak, Total, Ket Pembayaran --}}
            <div class="col-md-3">
                <div class="mb-2">
                    <div class="label-inline">Harga Barang</div>
                    <div>
                        @if($transaction->price !== null)
                            {{ number_format($transaction->price,2,',','.') }}
                        @else
                            —
                        @endif
                    </div>
                </div>
                <div class="mb-2">
                    <div class="label-inline">Pajak</div>
                    <div>
                        @if($transaction->tax_amount !== null)
                            {{ number_format($transaction->tax_amount,2,',','.') }}
                        @else
                            —
                        @endif
                    </div>
                </div>
                <div class="mb-2">
                    <div class="label-inline">Harga + Pajak</div>
                    <div>
                        @if($transaction->total_amount !== null)
                            {{ number_format($transaction->total_amount,2,',','.') }}
                        @else
                            —
                        @endif
                    </div>
                </div>
                <div class="mb-2">
                    <div class="label-inline">Keterangan Pembayaran</div>
                    <div>{{ $transaction->payment_status ?: '—' }}</div>
                </div>
            </div>
        </div>

        @if($hasReq)
            <hr>
            <div class="mb-2">
                <div class="label-inline">Permintaan Terkait</div>
                <div>
                    <a href="{{ route('requests.show', $transaction->request_id) }}">
                        #{{ $transaction->request_id }}
                    </a>
                    @if($transaction->request)
                        <div class="text-muted small">
                            Unit: {{ optional($transaction->request->unit)->unit_name ?? '—' }}
                            · Tanggal:
                            @php
                                $reqDate = $transaction->request->request_date;
                                if ($reqDate instanceof Carbon) {
                                    $reqDateDisplay = $reqDate->format('d-m-Y');
                                } elseif ($reqDate) {
                                    try {
                                        $reqDateDisplay = Carbon::parse($reqDate)->format('d-m-Y');
                                    } catch (InvalidFormatException $e) {
                                        $reqDateDisplay = (string) $reqDate;
                                    }
                                } else {
                                    $reqDateDisplay = '—';
                                }
                            @endphp
                            {{ $reqDateDisplay }}
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
