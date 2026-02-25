<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UnitSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,
            ItemMasterSeeder::class,
            ItemVariantSeeder::class,
            StockCurrentSeeder::class, 
            TransactionSeeder::class,
        ]);
    }
}