@extends('layouts.app')

@section('title', 'Pencarian')

@push('styles')
<style>
    .search-section-title{
        font-size:13px;
        font-weight:600;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:var(--text-muted);
        margin-bottom:6px;
    }
    .search-item{
        font-size:12px;
        padding:4px 0;
        border-bottom:1px dashed #e5e7eb;
    }
    .search-item:last-child{
        border-bottom:none;
    }
    .search-item small{
        color:#6b7280;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">

    <div class="page-title-wrap">
        <div>
            <h1 class="page-title">Pencarian</h1>
            <p class="page-subtitle">
                Hasil pencarian global untuk kata kunci:
                <strong>{{ $term ?: '–' }}</strong>
            </p>
        </div>
    </div>

    @if($term === '')
        <div class="panel-card">
            <p class="page-subtitle mb-0">
                Ketik kata kunci di kotak pencarian pada bagian atas untuk mulai mencari
                <strong>item, permintaan, atau transaksi</strong>.
            </p>
        </div>
    @else
        {{-- ================= ITEM ================= --}}
        @if($items->count())
            <div class="panel-card mb-3">
                <div class="search-section-title">Item</div>
                @foreach($items as $item)
                    <div class="search-item">
                        <strong>{{ $item->item_code }}</strong> – {{ $item->item_name }}
                        <br>
                        <small>Satuan: {{ $item->base_unit ?? '-' }}</small>
                        <a href="{{ route('items.edit', $item) }}" class="ms-1 small">[detail]</a>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ================= VARIAN ================= --}}
        @if($variants->count())
            <div class="panel-card mb-3">
                <div class="search-section-title">Varian (Brand / Lot)</div>
                @foreach($variants as $v)
                    <div class="search-item">
                        <strong>{{ $v->itemMaster->item_code ?? '-' }}</strong>
                        – {{ $v->itemMaster->item_name ?? '-' }}
                        <br>
                        <small>Brand: {{ $v->brand }} · Lot: {{ $v->lot_number }} · Exp: {{ $v->expiration_date ?? '-' }}</small>
                        <a href="{{ route('variants.edit', $v) }}" class="ms-1 small">[detail]</a>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ================= PERMINTAAN ================= --}}
        <div class="panel-card mb-3">
            <div class="search-section-title">Permintaan Barang</div>
            @forelse($requests as $r)
                <div class="search-item">
                    <strong>No. Req #{{ $r->id }}</strong>
                    <br>
                    <small>
                        Unit: {{ optional($r->unit)->unit_name ?? '-' }}
                        · Status: {{ $r->status ?? '-' }}
                        @if(!empty($r->request_date))
                            · Tgl: {{ \Illuminate\Support\Carbon::parse($r->request_date)->format('d-m-Y') }}
                        @endif
                    </small>
                    <a href="{{ route('requests.show', $r->id) }}" class="ms-1 small">[detail]</a>
                </div>
            @empty
                <div class="text-muted small">
                    Tidak ada permintaan yang cocok dengan kata kunci ini.
                </div>
            @endforelse
        </div>

        {{-- ================= TRANSAKSI ================= --}}
        <div class="panel-card mb-3">
            <div class="search-section-title">Transaksi</div>
            @forelse($transactions as $t)
                <div class="search-item">
                    <strong>{{ $t->type ?? '-' }}</strong>
                    – {{ $t->trans_date ? \Illuminate\Support\Carbon::parse($t->trans_date)->format('d-m-Y') : '-' }}
                    <br>
                    <small>
                        {{ optional(optional($t->variant)->itemMaster)->item_code ?? '-' }}
                        – {{ optional(optional($t->variant)->itemMaster)->item_name ?? '-' }}
                        @if($t->unit)
                            · Unit: {{ $t->unit->unit_name }}
                        @endif
                        @if($t->supplier)
                            · Rekanan: {{ $t->supplier->supplier_name }}
                        @endif
                        @if(!empty($t->invoice_number))
                            · No Faktur: {{ $t->invoice_number }}
                        @endif
                    </small>
                    <a href="{{ route('transactions.show', $t) }}" class="ms-1 small">[detail]</a>
                </div>
            @empty
                <div class="text-muted small">
                    Tidak ada transaksi yang cocok dengan kata kunci ini.
                </div>
            @endforelse
        </div>
    @endif

</div>
@endsection
