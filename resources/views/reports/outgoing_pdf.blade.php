<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Barang Keluar</title>
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
  <h2>Laporan Barang Keluar</h2>

  <div class="meta">
    <div>
      Periode:
      @if($dateFrom)
        {{ \Illuminate\Support\Carbon::parse($dateFrom)->format('d-m-Y') }}
      @else
        -
      @endif
      s/d
      @if($dateTo)
        {{ \Illuminate\Support\Carbon::parse($dateTo)->format('d-m-Y') }}
      @else
        -
      @endif
    </div>

    @if(!empty($unit))
      <div>Unit Tujuan: {{ $unit->unit_name ?? '-' }}</div>
    @endif

    <div class="small">
      Dicetak oleh: {{ $user->name }} ({{ optional($user->role)->role_name }})<br>
      Tanggal cetak: {{ \Illuminate\Support\Carbon::now()->format('d-m-Y H:i') }}
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:18px;">No</th>
        <th style="width:70px;">Tgl</th>
        <th style="width:80px;">No. Dok</th>
        <th>Item</th>
        <th style="width:70px;">Merek</th>
        <th style="width:70px;">Lot</th>
        <th style="width:40px;">Sat</th>
        <th style="width:60px;" class="text-right">Qty</th>
        <th style="width:90px;">Unit Tujuan</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $i => $t)
        @php
          $item  = optional($t->variant->itemMaster);
          $unitR = optional($t->unit);
        @endphp
        <tr>
          <td class="text-center">{{ $i + 1 }}</td>
          <td class="text-center">
            {{ $t->trans_date ? \Illuminate\Support\Carbon::parse($t->trans_date)->format('d-m-Y') : '-' }}
          </td>
          <td>{{ $t->doc_no ?: '-' }}</td>
          <td>
            <strong>{{ $item->item_code ?? '-' }}</strong><br>
            {{ $item->item_name ?? '-' }}
          </td>
          <td>{{ $t->brand ?? $t->variant->brand ?? '-' }}</td>
          <td>{{ $t->lot ?? $t->lot_number ?? $t->variant->lot_number ?? '-' }}</td>
          <td class="text-center">{{ $item->base_unit ?? '-' }}</td>
          <td class="text-right">{{ number_format($t->quantity ?? 0, 0, ',', '.') }}</td>
          <td>{{ $unitR->unit_name ?? '-' }}</td>
          <td>{{ $t->note ?: '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="10" class="text-center">Tidak ada data barang keluar pada periode ini.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
