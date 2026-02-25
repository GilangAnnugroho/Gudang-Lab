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
        Schema::create('items_master', function (Blueprint $table) {
            $table->id();
            // Kode/No. Item (HM-01, AT-01, dll. dari Excel)
            $table->string('item_code', 50)->unique()->comment('Kode item (misal: HM-01)');
            $table->string('item_name', 255)->comment('Nama/Jenis BHP');
            $table->string('base_unit', 50)->comment('Satuan dasar (misal: Botol, Pcs, Pak)');
            $table->text('warnings')->nullable()->comment('Tanda peringatan (misal: Iritasi, Korosif)');
            $table->text('storage_temp')->nullable()->comment('Suhu Penyimpanan (misal: 2° - 30° C)');
            $table->string('size', 100)->nullable()->comment('Ukuran (misal: 1 L, 210x30 mm)');
            $table->timestamps();

            // Foreign Key
            $table->foreignId('category_id')
                  ->constrained('categories') // Terhubung ke tabel 'categories'
                  ->onUpdate('cascade')
                  ->onDelete('restrict')
                  ->comment('FK ke categories: Reagen/ATK/RT');
        });
    }

    /**
     * Balikkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('items_master');
    }
};