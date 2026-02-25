<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $rows = [
            [
                'supplier_name'  => 'PT. Global Medika Cipta',
                'contact_person' => 'Bapak Adi',
                'phone'          => '081234567890',
                'address'        => 'Jl. Kesehatan No. 1, Jakarta',
            ],
            [
                'supplier_name'  => 'Toko ATK Jaya',
                'contact_person' => 'Ibu Siti',
                'phone'          => '085000999888',
                'address'        => 'Jl. Raya Cirebon No. 50',
            ],
        ];

        foreach ($rows as $s) {
            DB::table('suppliers')->updateOrInsert(
                ['supplier_name' => $s['supplier_name']],   // UNIQUE key
                [
                    'contact_person' => $s['contact_person'],
                    'phone'          => $s['phone'],
                    'address'        => $s['address'],
                    'updated_at'     => $now,
                    'created_at'     => DB::raw('COALESCE(created_at, NOW())')
                ]
            );
        }
    }
}
