<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom warning_stock & storage_temp ke tabel items_master
     * hanya jika kolom belum ada (mencegah duplicate migration error).
     */
    public function up(): void
    {
        Schema::table('items_master', function (Blueprint $table) {

            // Tambah kolom warning_stock jika belum ada
            if (!Schema::hasColumn('items_master', 'warning_stock')) {
                $table->integer('warning_stock')
                    ->default(0)
                    ->after('base_unit'); // sesuaikan jika kolom base_unit tidak ada
            }

            // Tambah kolom storage_temp jika belum ada
            if (!Schema::hasColumn('items_master', 'storage_temp')) {
                $table->string('storage_temp', 100)
                    ->nullable()
                    ->after('warning_stock');
            }
        });
    }

    /**
     * Hapus kolom ketika rollback migration
     */
    public function down(): void
    {
        Schema::table('items_master', function (Blueprint $table) {

            if (Schema::hasColumn('items_master', 'warning_stock')) {
                $table->dropColumn('warning_stock');
            }

            if (Schema::hasColumn('items_master', 'storage_temp')) {
                $table->dropColumn('storage_temp');
            }
        });
    }
};
