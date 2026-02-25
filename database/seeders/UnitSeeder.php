<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $rows = [
            ['unit_name' => 'Sistem'],
            ['unit_name' => 'Gudang'],
            ['unit_name' => 'ATK & Umum'],
            ['unit_name' => 'Hematologi'],
            ['unit_name' => 'Kimia Klinik'],
            ['unit_name' => 'Imunologi'],
            ['unit_name' => 'Rontgen'],
        ];

        foreach ($rows as $u) {
            DB::table('units')->updateOrInsert(
                ['unit_name' => $u['unit_name']],   // UNIQUE key
                ['updated_at' => $now, 'created_at' => DB::raw('COALESCE(created_at, NOW())')]
            );
        }
    }
}
