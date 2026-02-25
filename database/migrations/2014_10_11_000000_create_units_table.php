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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            // Nama unit kerja/laboratorium (misal: Hematologi, Kimia Klinik, ATK & Umum)
            $table->string('unit_name', 100)->unique()->comment('Nama unit kerja atau divisi');
            $table->timestamps(); 
        });
    }

    /**
     * Balikkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};