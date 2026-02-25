<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok Opname</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 12px; }
        
        .meta-info { margin-bottom: 15px; width: 100%; }
        .meta-info td { padding: 2px; }

        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #999; padding: 6px; text-align: left; }
        table.data th { background-color: #eee; font-weight: bold; text-align: center; }
        
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-red { color: red; font-weight: bold; }
        .text-green { color: green; font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: right; font-size: 10px; color: #777; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Laporan Riwayat Stok Opname</h1>
        <p>Gudang Farmasi & Logistik - Labkesda</p>
    </div>

    <table class="meta-info">
        <tr>
            <td width="100">Dicetak Oleh</td>
            <td>: {{ $meta['user'] }}</td>
            <td width="100" class="text-right">Tanggal Cetak</td>
            <td width="120" class="text-right">: {{ $meta['print_date'] }}</td>
        </tr>
        <tr>
            <td>Periode Filter</td>
            <td colspan="3">: 
                @if($meta['start_date'] && $meta['end_date'])
                    {{ date('d/m/Y', strtotime($meta['start_date'])) }} s/d {{ date('d/m/Y', strtotime($meta['end_date'])) }}
                @else
                    Semua Riwayat
                @endif
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">Tanggal</th>
                <th>Nama Barang / Varian</th>
                <th width="90">Merk / Lot</th>
                <th width="80">Petugas</th>
                <th width="50">Sistem</th>
                <th width="50">Fisik</th>
                <th width="50">Selisih</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($opnames as $index => $opname)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ $opname->opname_date->format('d/m/Y') }}</td>
                <td>
                    {{ $opname->variant->itemMaster->item_name }}
                </td>
                <td>
                    {{ $opname->variant->brand }}<br>
                    <small>Lot: {{ $opname->variant->lot_number }}</small>
                </td>
                <td>{{ $opname->user->name }}</td>
                <td class="text-center">{{ $opname->system_stock }}</td>
                <td class="text-center"><strong>{{ $opname->physical_stock }}</strong></td>
                <td class="text-center">
                    @php $diff = $opname->difference; @endphp
                    @if($diff < 0)
                        <span class="text-red">{{ $diff }}</span>
                    @elseif($diff > 0)
                        <span class="text-green">+{{ $diff }}</span>
                    @else
                        0
                    @endif
                </td>
                <td>{{ $opname->notes ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Halaman ini dicetak dari Sistem Informasi Gudang Labkesda.
    </div>

</body>
</html>