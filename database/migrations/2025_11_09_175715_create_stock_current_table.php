<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('stock_current', function (Blueprint $table) {
            // Kita gunakan ID dari Varian sebagai Primary Key (1:1 relationship)
            $table->foreignId('item_variant_id')
                  ->primary()
                  ->constrained('item_variants') // Terhubung ke tabel 'item_variants'
                  ->onUpdate('cascade')
                  ->onDelete('cascade') // Jika varian dihapus, stok ikut terhapus
                  ->comment('FK/PK ke item_variants');

            // Saldo Stok
            $table->integer('current_quantity')->default(0)->comment('Jumlah stok saat ini');
            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_current');
    }
};