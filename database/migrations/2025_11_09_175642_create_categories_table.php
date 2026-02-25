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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            // Kategori barang (misal: Reagen, ATK, Kebutuhan Rumah Tangga)
            $table->string('category_name', 100)->unique()->comment('Kategori barang (Reagen/ATK/RT)');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Balikkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};