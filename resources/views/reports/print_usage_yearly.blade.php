<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rekap Pemakaian Tahunan {{ $year }}</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 9px;
      margin: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      table-layout: fixed; 
    }
    th, td {
      border: 1px solid #000;
      padding: 2px 3px;
      word-wrap: break-word;
    }
    th {
      background: #f0f0f0;
    }
    .text-center { text-align: center; }
    .text-right  { text-align: right; }
  </style>
</head>
<body>

  <h3 style="text-align:center; margin-bottom:3px;">
    Rekap Pemakaian Barang Keluar Tahunan
  </h3>
  <p style="text-align:center; margin:0 0 6px 0;">
    Tahun {{ $year }} –
    Mode:
    @if($mode === 'variant')
      Per Item + Varian (Merek/Lot)
    @else
      Per Item
    @endif
  </p>

  <p style="font-size:8px; margin-bottom:6px;">
    Dicetak oleh: <strong>{{ $user->name ?? '-' }}</strong><br>
    Tanggal cetak: {{ now()->format('d-m-Y H:i') }}
  </p>

  @php
      $monthNames = [
          1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
          5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
          9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
      ];
      // nonMonthCols = No + Kode + Nama + Sat (+ Brand + Lot + Exp kalau mode variant)
      $nonMonthCols = ($mode === 'variant') ? 7 : 4;
      $totalColumns = $nonMonthCols + 12 + 1; // + 12 bulan + kolom Total
  @endphp

  <table>
    <thead>
      <tr>
        <th class="text-center" style="width:18px;">No</th>
        <th class="text-center" style="width:40px;">Kode</th>
        <th>Nama Item</th>
        <th class="text-center" style="width:30px;">Sat</th>

        @if($mode === 'variant')
          <th style="width:70px;">Brand</th>
          <th style="width:60px;">Lot</th>
          <th style="width:55px;">Exp</th>
        @endif

        @foreach($monthNames as $label)
          <th class="text-right" style="width:32px;">{{ $label }}</th>
        @endforeach

        <th class="text-right" style="width:45px;">Total</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $i => $row)
        @php
            // qty_by_month: [1 => x, 2 => y, ...]
            $qtyByMonth = $row->qty_by_month ?? [];
        @endphp
        <tr>
          <td class="text-center">{{ $i+1 }}</td>
          <td class="text-center">{{ $row->item_code }}</td>
          <td>{{ $row->item_name }}</td>
          <td class="text-center">{{ $row->base_unit ?? '-' }}</td>

          @if($mode === 'variant')
            <td>{{ $row->brand ?: '-' }}</td>
            <td>{{ $row->lot_number ?: '-' }}</td>
            <td class="text-center">
              @if(!empty($row->expiration_date))
                {{ \Illuminate\Support\Carbon::parse($row->expiration_date)->format('d-m-Y') }}
              @else
                -
              @endif
            </td>
          @endif

          @for($m = 1; $m <= 12; $m++)
            @php $val = $qtyByMonth[$m] ?? 0; @endphp
            <td class="text-right">
              {{ $val ? number_format($val, 0, ',', '.') : '0' }}
            </td>
          @endfor

          <td class="text-right">{{ number_format($row->total_qty, 0, ',', '.') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="{{ $totalColumns }}" class="text-center">
            Tidak ada data pemakaian barang keluar untuk tahun ini.
          </td>
        </tr>
      @endforelse
    </tbody>

    @if(!empty($rows) && isset($monthlyTotals, $grandTotal))
      <tfoot>
        <tr>
          <th colspan="{{ $nonMonthCols }}" class="text-right">
            TOTAL
          </th>
          @for($m = 1; $m <= 12; $m++)
            @php $val = $monthlyTotals[$m] ?? 0; @endphp
            <th class="text-right">
              {{ $val ? number_format($val, 0, ',', '.') : '0' }}
            </th>
          @endfor
          <th class="text-right">
            {{ number_format($grandTotal ?? 0, 0, ',', '.') }}
          </th>
        </tr>
      </tfoot>
    @endif
  </table>

</body>
</html>
