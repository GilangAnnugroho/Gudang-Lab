<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockCurrentSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil seluruh variant
        $variantIds = DB::table('item_variants')->pluck('id');

        foreach ($variantIds as $vid) {
            // Hitung stok dari transaksi
            $tot = DB::table('transactions')
                ->selectRaw("SUM(CASE WHEN type='MASUK'  THEN quantity ELSE 0 END) AS total_in,
                             SUM(CASE WHEN type='KELUAR' THEN quantity ELSE 0 END) AS total_out")
                ->where('item_variant_id', $vid)
                ->first();

            $qty = max(0, (int)($tot->total_in ?? 0) - (int)($tot->total_out ?? 0));

            DB::table('stock_current')->updateOrInsert(
                ['item_variant_id' => $vid],
                ['current_quantity'=> $qty]
            );
        }
    }
}
