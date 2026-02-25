<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $rows = [
            ['category_name' => 'Reagen', 'description' => 'Bahan kimia habis pakai untuk pemeriksaan lab.'],
            ['category_name' => 'Bahan Habis Pakai (BHP)', 'description' => 'BHP non-kimia (misal: jarum, sarung tangan, dll).'],
            ['category_name' => 'ATK', 'description' => 'Alat tulis kantor dan kebutuhan administrasi.'],
            ['category_name' => 'Kebutuhan Rumah Tangga', 'description' => 'Kebutuhan kebersihan dan umum.'],
        ];

        foreach ($rows as $r) {
            DB::table('categories')->updateOrInsert(
                ['category_name' => $r['category_name']],   // UNIQUE key
                [
                    'description' => $r['description'],
                    'updated_at'  => $now,
                    'created_at'  => DB::raw('COALESCE(created_at, NOW())')
                ]
            );
        }
    }
}
