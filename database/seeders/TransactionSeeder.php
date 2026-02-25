<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\ItemVariant;
use App\Models\Unit;
use App\Models\Supplier;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * Asumsi:
         * - ItemMasterSeeder & ItemVariantSeeder sudah dijalankan.
         * - Tabel suppliers berisi:
         *   1 = PT. Global Medika Cipta  (supplier lab / medis / rumah tangga)
         *   2 = Toko ATK Jaya           (supplier ATK)
         */

        // Ambil supplier sesuai namanya (lebih aman daripada hardcode id)
        $supplierMedis = Supplier::where('supplier_name', 'like', '%Global Medika Cipta%')->first();
        $supplierAtk   = Supplier::where('supplier_name', 'like', '%ATK Jaya%')->first();

        $supplierMedisId = $supplierMedis?->id ?? 1;   // fallback 1
        $supplierAtkId   = $supplierAtk?->id   ?? 2;   // fallback 2

        // Referensi unit (boleh null, FK mengizinkan null)
        $unitLabKlinik    = Unit::find(1)?->id;
        $unitMikro        = Unit::find(2)?->id;
        $unitAdministrasi = Unit::find(3)?->id;

        // Untuk created_by, sementara pakai user id 1 (Super Admin)
        $createdBy = 1;

        /*
         * =========================
         * 1. TRANSAKSI MASUK
         * =========================
         */

        // --- Reagen ---
        $regGlu = ItemVariant::where('brand', 'Nesco Lab')
            ->where('lot_number', 'REG-GLU-01')
            ->first();

        if ($regGlu) {
            // dari PT. Global Medika Cipta
            $this->createTransaction([
                'type'        => 'MASUK',
                'date'        => '2025-01-15',
                'doc_no'      => 'INV-AWAL-001',
                'invoice'     => 'INV-AWAL-001',
                'variant'     => $regGlu,
                'qty'         => 40,
                'price'       => 5000,
                'supplier_id' => $supplierMedisId,
                'unit_id'     => null,
                'note'        => 'Stok awal untuk Reagen Glukosa',
                'payment'     => 'LUNAS',
                'createdBy'   => $createdBy,
            ]);
        }

        $regUrea = ItemVariant::where('brand', 'BioChem')
            ->where('lot_number', 'REG-UREA-02')
            ->first();

        if ($regUrea) {
            $this->createTransaction([
                'type'        => 'MASUK',
                'date'        => '2025-01-20',
                'doc_no'      => 'INV-AWAL-002',
                'invoice'     => 'INV-AWAL-002',
                'variant'     => $regUrea,
                'qty'         => 30,
                'price'       => 6500,
                'supplier_id' => $supplierMedisId,
                'unit_id'     => null,
                'note'        => 'Stok awal Reagen Urea',
                'payment'     => 'LUNAS',
                'createdBy'   => $createdBy,
            ]);
        }

        // --- BHP: Sarung tangan latex ---
        $gloves = ItemVariant::where('brand', 'Medipro')
            ->where('lot_number', 'BHP-23-01')
            ->first();

        if ($gloves) {
            $this->createTransaction([
                'type'        => 'MASUK',
                'date'        => '2025-01-18',
                'doc_no'      => 'INV-AWAL-003',
                'invoice'     => 'INV-AWAL-003',
                'variant'     => $gloves,
                'qty'         => 100,
                'price'       => 35000,
                'supplier_id' => $supplierMedisId,
                'unit_id'     => null,
                'note'        => 'Stok awal Sarung Tangan Latex',
                'payment'     => 'HUTANG',  // contoh: masih hutang
                'createdBy'   => $createdBy,
            ]);
        }

        // --- ATK ---
        $atk1 = ItemVariant::where('brand', 'Standard Office')->first();
        $atk2 = ItemVariant::where('brand', 'Office Max')->first();

        if ($atk1) {
            // dari Toko ATK Jaya
            $this->createTransaction([
                'type'        => 'MASUK',
                'date'        => '2025-01-10',
                'doc_no'      => 'INV-ATK-001',
                'invoice'     => 'INV-ATK-001',
                'variant'     => $atk1,
                'qty'         => 200,
                'price'       => 2500,
                'supplier_id' => $supplierAtkId,
                'unit_id'     => null,
                'note'        => 'Stok awal ATK (ballpoint / sejenis)',
                'payment'     => 'LUNAS',
                'createdBy'   => $createdBy,
            ]);
        }

        if ($atk2) {
            $this->createTransaction([
                'type'        => 'MASUK',
                'date'        => '2025-01-12',
                'doc_no'      => 'INV-ATK-002',
                'invoice'     => 'INV-ATK-002',
                'variant'     => $atk2,
                'qty'         => 150,
                'price'       => 3000,
                'supplier_id' => $supplierAtkId,
                'unit_id'     => null,
                'note'        => 'Stok tambahan ATK',
                'payment'     => 'LUNAS',
                'createdBy'   => $createdBy,
            ]);
        }

        /*
         * =========================
         * 2. TRANSAKSI KELUAR (DISTRIBUSI)
         * =========================
         */

        // Distribusi Reagen Glukosa ke Lab Klinik
        if ($regGlu && $unitLabKlinik) {
            $this->createTransaction([
                'type'        => 'KELUAR',
                'date'        => '2025-02-01',
                'doc_no'      => 'DIST-REG-001',
                'invoice'     => 'DIST-REG-001',
                'variant'     => $regGlu,
                'qty'         => 10,
                'price'       => 0,
                'supplier_id' => null, // distribusi internal
                'unit_id'     => $unitLabKlinik,
                'note'        => 'Distribusi Reagen Glukosa ke Lab Klinik',
                'payment'     => 'LUNAS',
                'createdBy'   => $createdBy,
            ]);
        }

        // Distribusi Sarung Tangan ke Lab Klinik & Mikrobiologi
        if ($gloves && $unitLabKlinik) {
            $this->createTransaction([
                'type'        => 'KELUAR',
                'date'        => '2025-02-05',
                'doc_no'      => 'DIST-BHP-001',
                'invoice'     => 'DIST-BHP-001',
                'variant'     => $gloves,
                'qty'         => 20,
                'price'       => 0,
                'supplier_id' => null,
                'unit_id'     => $unitLabKlinik,
                'note'        => 'Distribusi Sarung Tangan ke Lab Klinik',
                'payment'     => 'LUNAS',
                'createdBy'   => $createdBy,
            ]);
        }

        if ($gloves && $unitMikro) {
            $this->createTransaction([
                'type'        => 'KELUAR',
                'date'        => '2025-02-07',
                'doc_no'      => 'DIST-BHP-002',
                'invoice'     => 'DIST-BHP-002',
                'variant'     => $gloves,
                'qty'         => 15,
                'price'       => 0,
                'supplier_id' => null,
                'unit_id'     => $unitMikro,
                'note'        => 'Distribusi Sarung Tangan ke Lab Mikrobiologi',
                'payment'     => 'LUNAS',
                'createdBy'   => $createdBy,
            ]);
        }

        // Distribusi ATK ke Administrasi
        if ($atk1 && $unitAdministrasi) {
            $this->createTransaction([
                'type'        => 'KELUAR',
                'date'        => '2025-02-03',
                'doc_no'      => 'DIST-ATK-001',
                'invoice'     => 'DIST-ATK-001',
                'variant'     => $atk1,
                'qty'         => 50,
                'price'       => 0,
                'supplier_id' => null,
                'unit_id'     => $unitAdministrasi,
                'note'        => 'Distribusi ATK ke Administrasi',
                'payment'     => 'LUNAS',
                'createdBy'   => $createdBy,
            ]);
        }
    }

    /**
     * Helper insert transaksi ke tabel `transactions`.
     */
    private function createTransaction(array $data): void
    {
        $variant = $data['variant'];

        $price = $data['price'] ?? 0;
        $qty   = $data['qty']   ?? 0;

        // Hitung PPN 11% kalau ada harga
        $tax   = $price > 0 ? round($price * $qty * 0.11, 2) : 0;
        $total = $price > 0 ? ($price * $qty) + $tax : 0;

        Transaction::create([
            'trans_date'        => $data['date'],          // tipe DATE
            'type'              => $data['type'],          // 'MASUK' / 'KELUAR'
            'doc_no'            => $data['doc_no'],
            'invoice_no'        => $data['invoice'],
            'item_variant_id'   => $variant->id,
            'brand'             => $variant->brand,
            'lot_number'        => $variant->lot_number,
            'expiration_date'   => $variant->expiration_date,
            'quantity'          => $qty,
            'price'             => $price,
            'tax_amount'        => $tax,
            'total_amount'      => $total,
            'supplier_id'       => $data['supplier_id'] ?? null,
            'unit_id'           => $data['unit_id'] ?? null,
            'request_id'        => null,
            'note'              => $data['note'] ?? null,
            'payment_status'    => $data['payment'] ?? 'LUNAS',  // WAJIB 'LUNAS' / 'HUTANG'
            'storage_condition' => $variant->itemMaster->storage_temp ?? null,
            'created_by'        => $data['createdBy'],
        ]);
    }
}
