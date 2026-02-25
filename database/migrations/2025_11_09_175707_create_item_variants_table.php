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
        Schema::create('item_variants', function (Blueprint $table) {
            $table->id();
            $table->string('brand', 100)->comment('Merk barang');
            // Lot Number hanya relevan untuk Reagen, bisa NULL untuk ATK
            $table->string('lot_number', 100)->nullable()->comment('Nomor/Lot (wajib untuk reagen)');
            // Tanggal Kadaluwarsa hanya relevan untuk Reagen/BHP, bisa NULL untuk ATK
            $table->date('expiration_date')->nullable()->comment('Tanggal Kadaluwarsa (wajib untuk reagen)');
            $table->timestamps();

            // Foreign Key
            $table->foreignId('item_master_id')
                  ->constrained('items_master') // Terhubung ke tabel 'items_master'
                  ->onUpdate('cascade')
                  ->onDelete('cascade') // Jika master item dihapus, varian ikut terhapus
                  ->comment('FK ke items_master');

            // Unique Index untuk mencegah duplikasi Varian
            $table->unique(['item_master_id', 'brand', 'lot_number', 'expiration_date'], 'unique_item_variant');
        });
    }

    /**
     * Balikkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('item_variants');
    }
};