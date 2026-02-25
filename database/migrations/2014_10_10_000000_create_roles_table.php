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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            // Nama role: Super Admin, Admin Gudang, Petugas Unit, Kepala Lab
            $table->string('role_name', 100)->unique()->comment('Nama role pengguna');
            $table->timestamps(); 
        });
    }

    /**
     * Balikkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};