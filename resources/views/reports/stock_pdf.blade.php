<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $title ?? 'Laporan Stok Akhir' }}</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; }
    h2 { margin: 0 0 4px 0; }
    .meta { margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    th, td { border: 1px solid #000; padding: 4px 6px; }
    th { background: #f3f4f6; font-weight: bold; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .small { font-size: 10px; }
  </style>
</head>
<body>
  @php
      $mode = $mode ?? 'simple';
  @endphp

  <h2>{{ $title ?? 'Laporan Stok Akhir' }}</h2>

  <div class="meta">
    @if(!empty($onlyReagen) && $onlyReagen)
      <div>Filter: Hanya item kategori <strong>Reagen</strong></div>
    @else
      <div>Filter: Semua kategori</div>
    @endif

    @if(!empty($asOf))
      <div>Posisi stok per: {{ \Illuminate\Support\Carbon::parse($asOf)->format('d-m-Y') }}</div>
    @else
      <div>Posisi stok: terkini (s/d semua transaksi)</div>
    @endif

    <div>Mode tampilan:
        @if($mode === 'opname')
            Format Stok Opname (Saldo–Masuk–Keluar–Sisa)
        @else
            Sederhana (Qty Akhir per Lot)
        @endif
    </div>

    @if(!empty($search))
      <div>Pencarian: "{{ $search }}"</div>
    @endif

    <div class="small">
      Dicetak oleh: {{ $user->name }} ({{ optional($user->role)->role_name }})<br>
      Tanggal cetak: {{ \Illuminate\Support\Carbon::now()->format('d-m-Y H:i') }}
    </div>
  </div>

  @if($mode === 'simple')
    {{-- MODE SEDERHANA: Qty Akhir per Lot --}}
    <table>
      <thead>
        <tr>
          <th style="width:18px;">No</th>
          <th style="width:70px;">Kode</th>
          <th>Nama Item</th>
          <th style="width:70px;">Merek</th>
          <th style="width:70px;">Lot</th>
          <th style="width:70px;">Exp</th>
          <th style="width:60px;">FEFO</th>
          <th style="width:40px;">Sat</th>
          <th style="width:60px;" class="text-right">Qty Akhir</th>
        </tr>
      </thead>
      <tbody>
        @forelse($variants as $i => $v)
          @php
            $item = optional($v->itemMaster);
            $qty  = (int) ($qtyByVariant[$v->id] ?? 0);
          @endphp
          <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $item->item_code ?? '-' }}</td>
            <td>{{ $item->item_name ?? '-' }}</td>
            <td>{{ $v->brand ?: '-' }}</td>
            <td>{{ $v->lot_number ?: '-' }}</td>
            <td class="text-center">
              @if($v->expiration_date)
                {{ \Illuminate\Support\Carbon::parse($v->expiration_date)->format('d-m-Y') }}
              @else
                -
              @endif
            </td>
            <td class="text-center">
              {{ $v->fefo_label_text ?? '-' }}
            </td>
            <td class="text-center">{{ $item->base_unit ?? '-' }}</td>
            <td class="text-right">{{ number_format($qty, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center">Tidak ada data stok.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

  @else
    {{-- MODE STOK OPNAME: Saldo–Masuk–Keluar–Sisa --}}
    <table>
      <thead>
        <tr>
          <th style="width:18px;">No</th>
          <th style="width:70px;">Kode</th>
          <th>Nama Item</th>
          <th style="width:70px;">Merek</th>
          <th style="width:70px;">Lot</th>
          <th style="width:70px;">Exp</th>
          <th style="width:40px;">Sat</th>
          <th style="width:60px;" class="text-right">Saldo Awal</th>
          <th style="width:60px;" class="text-right">Masuk</th>
          <th style="width:60px;" class="text-right">Keluar</th>
          <th style="width:60px;" class="text-right">Sisa Akhir</th>
        </tr>
      </thead>
      <tbody>
        @forelse($variants as $i => $v)
          @php
            $item = optional($v->itemMaster);
            $id   = $v->id;
            $open = (int) ($saldoAwal[$id] ?? 0);
            $in   = (int) ($qtyInPeriod[$id] ?? 0);
            $out  = (int) ($qtyOutPeriod[$id] ?? 0);
            $end  = (int) ($saldoAkhirOpname[$id] ?? 0);
          @endphp
          <tr>
            <td class="text-center">{{ $i + 1 }}</td>
            <td>{{ $item->item_code ?? '-' }}</td>
            <td>{{ $item->item_name ?? '-' }}</td>
            <td>{{ $v->brand ?: '-' }}</td>
            <td>{{ $v->lot_number ?: '-' }}</td>
            <td class="text-center">
              @if($v->expiration_date)
                {{ \Illuminate\Support\Carbon::parse($v->expiration_date)->format('d-m-Y') }}
              @else
                -
              @endif
            </td>
            <td class="text-center">{{ $item->base_unit ?? '-' }}</td>
            <td class="text-right">{{ number_format($open, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($in, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($out, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($end, 0, ',', '.') }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="11" class="text-center">Tidak ada data stok.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  @endif

</body>
</html>
