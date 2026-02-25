<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Admin Gudang',
            'Kepala Lab',
            'Petugas Unit',
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['role_name' => $r]);
        }
    }
}
