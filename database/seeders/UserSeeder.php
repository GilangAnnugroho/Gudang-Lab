<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $roleSuper   = DB::table('roles')->where('role_name', 'Super Admin')->value('id');
        $roleAdmin   = DB::table('roles')->where('role_name', 'Admin Gudang')->value('id');
        $rolePetugas = DB::table('roles')->where('role_name', 'Petugas Unit')->value('id');
        $roleKepala  = DB::table('roles')->where('role_name', 'Kepala Lab')->value('id');
        $unitSistem  = DB::table('units')->where('unit_name', 'Sistem')->value('id') ?? DB::table('units')->value('id');
        $unitGudang  = DB::table('units')->where('unit_name', 'Gudang')->value('id') ?? DB::table('units')->value('id');

        if (!$roleSuper || !$roleAdmin || !$rolePetugas || !$roleKepala) {
            throw new \RuntimeException('Roles belum lengkap. Jalankan RoleSeeder dulu.');
        }
        if (!$unitSistem || !$unitGudang) {
            throw new \RuntimeException('Units belum tersedia. Jalankan UnitSeeder dulu.');
        }

        $rows = [
            [
                'name'     => 'Super Admin PKL',
                'email'    => 'admin@labkesda.com',
                'password' => Hash::make('password'), 
                'role_id'  => $roleSuper,
                'unit_id'  => $unitSistem,
            ],
            [
                'name'     => 'Petugas Gudang',
                'email'    => 'gudang@labkesda.com',
                'password' => Hash::make('password'),
                'role_id'  => $roleAdmin,
                'unit_id'  => $unitGudang,
            ],
            [
                'name'     => 'Kepala Lab',
                'email'    => 'kepala@labkesda.com',
                'password' => Hash::make('password'),
                'role_id'  => $roleKepala,
                'unit_id'  => $unitGudang, 
            ],
            [
                'name'     => 'Petugas Unit',
                'email'    => 'petugas@labkesda.com',
                'password' => Hash::make('password'),
                'role_id'  => $rolePetugas,
                'unit_id'  => $unitGudang, 
            ],
        ];

        foreach ($rows as $u) {
            DB::table('users')->updateOrInsert(
                ['email' => $u['email']], 
                [
                    'name'          => $u['name'],
                    'password'      => $u['password'],
                    'role_id'       => $u['role_id'],
                    'unit_id'       => $u['unit_id'],
                    'remember_token'=> Str::random(60),
                    'updated_at'    => $now,
                    'created_at'    => DB::raw('COALESCE(created_at, NOW())'),
                ]
            );
        }
    }
}
