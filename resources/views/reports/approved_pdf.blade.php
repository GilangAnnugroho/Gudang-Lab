<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Laporan Permintaan Disetujui</title>
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
  <h2>Laporan Permintaan Barang Disetujui</h2>

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
    <div class="small">
      Dicetak oleh: {{ $user->name }} ({{ optional($user->role)->role_name }})<br>
      Tanggal cetak: {{ \Illuminate\Support\Carbon::now()->format('d-m-Y') }}
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:18px;">No</th>
        <th style="width:70px;">Tgl Permintaan</th>
        <th style="width:60px;">No. Req</th>
        <th style="width:110px;">Unit</th>
        <th>Peminta</th>
        <th style="width:110px;">Disetujui Oleh</th>
        <th style="width:80px;">Status</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
      @forelse($rows as $i => $r)
        @php
          $unit     = optional($r->unit);
          $peminta  = optional($r->requester);
          $approver = optional($r->approver);
        @endphp
        <tr>
          <td class="text-center">{{ $i + 1 }}</td>
          <td class="text-center">
            @if(!empty($r->request_date))
              {{ \Illuminate\Support\Carbon::parse($r->request_date)->format('d-m-Y') }}
            @else
              -
            @endif
          </td>
          <td class="text-center">{{ $r->id }}</td>
          <td>{{ $unit->unit_name ?? '-' }}</td>
          <td>{{ $peminta->name ?? '-' }}</td>
          <td>{{ $approver->name ?? '-' }}</td>
          <td class="text-center">{{ $r->status_label ?? $r->status ?? 'Disetujui' }}</td>
          <td>{{ $r->note ?? '-' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="text-center">Tidak ada permintaan yang disetujui pada periode ini.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
