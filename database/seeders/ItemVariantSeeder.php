<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemVariant;
use App\Models\ItemMaster;

class ItemVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * Konfigurasi 20 varian:
         * - Reagen (category_id = 1)                  -> 5 varian
         * - BHP (category_id = 2)                     -> 5 varian
         * - ATK (category_id = 3)                     -> 5 varian
         * - Kebutuhan Rumah Tangga (category_id = 4)  -> 5 varian
         *
         * Catatan:
         * - Untuk Reagen & sebagian BHP: isi lot_number + expiration_date.
         * - Untuk ATK & sebagian rumah tangga: expiration_date boleh null.
         * - Item dipilih dari items_master berdasarkan category_id
         *   lalu dibagi rata (round-robin) ke varian yang ada.
         */

        $config = [

            // ================= Reagen (category_id = 1) =================
            1 => [
                ['brand' => 'Nesco Lab',         'lot' => 'REG-GLU-01',  'exp' => '2025-08-31'],
                ['brand' => 'BioChem',           'lot' => 'REG-CHOL-02', 'exp' => '2026-02-28'],
                ['brand' => 'SigmaLab',          'lot' => 'REG-BUF-03',  'exp' => '2025-11-30'],
                ['brand' => 'UltraLab',          'lot' => 'REG-HEM-04',  'exp' => '2026-07-31'],
                ['brand' => 'DiagnosticPro',     'lot' => 'REG-URI-05',  'exp' => '2027-01-31'],
            ],

            // ================= BHP (category_id = 2) =================
            2 => [
                ['brand' => 'Medipro',           'lot' => 'BHP-GLV-23-01', 'exp' => '2026-01-31'],
                ['brand' => 'HealthCare',        'lot' => 'BHP-MSK-23-02', 'exp' => '2026-06-30'],
                ['brand' => 'SafeTouch',         'lot' => 'BHP-COT-23-03', 'exp' => null],
                ['brand' => 'OneMed Supplies',   'lot' => 'BHP-SWB-23-04', 'exp' => '2025-09-30'],
                ['brand' => 'LabProtect',        'lot' => 'BHP-VAC-23-05', 'exp' => '2027-03-31'],
            ],

            // ========== ATK (category_id = 3) =========================
            3 => [
                ['brand' => 'Standard Office',   'lot' => 'ATK-2025-A1',   'exp' => null],
                ['brand' => 'Premium Station',   'lot' => 'ATK-2025-B2',   'exp' => null],
                ['brand' => 'Office Max',        'lot' => 'ATK-2025-C3',   'exp' => null],
                ['brand' => 'QuickNote',         'lot' => 'ATK-2025-D4',   'exp' => null],
                ['brand' => 'Lab Office Series', 'lot' => 'ATK-2025-E5',   'exp' => null],
            ],

            // ========== Kebutuhan Rumah Tangga (category_id = 4) =====
            4 => [
                ['brand' => 'HomeCare',          'lot' => 'HOM-24-01',     'exp' => null],
                ['brand' => 'CleanLab',          'lot' => 'HOM-24-02',     'exp' => null],
                ['brand' => 'Daily Use',         'lot' => 'HOM-24-03',     'exp' => null],
                ['brand' => 'Comfort Series',    'lot' => 'HOM-24-04',     'exp' => null],
                ['brand' => 'LabHome',           'lot' => 'HOM-24-05',     'exp' => null],
            ],
        ];

        foreach ($config as $categoryId => $variants) {

            // Ambil semua items_master untuk kategori ini
            $items = ItemMaster::where('category_id', $categoryId)->orderBy('id')->get();

            if ($items->isEmpty()) {
                // Kalau belum ada item di kategori ini, lewati saja
                continue;
            }

            $itemCount = $items->count();
            $index     = 0;

            foreach ($variants as $v) {
                $item = $items[$index % $itemCount]; // round-robin ke item master

                ItemVariant::create([
                    'item_master_id'  => $item->id,
                    'brand'           => $v['brand'],
                    'lot_number'      => $v['lot'],
                    'expiration_date' => $v['exp'], // 'Y-m-d' atau null
                ]);

                $index++;
            }
        }
    }
}
