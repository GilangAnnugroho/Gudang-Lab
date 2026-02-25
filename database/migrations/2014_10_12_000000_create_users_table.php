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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            // Foreign Keys yang ditambahkan
            $table->foreignId('role_id')
                  ->constrained('roles') // Terhubung ke tabel 'roles'
                  ->onUpdate('cascade')
                  ->onDelete('restrict')
                  ->comment('FK ke roles: level akses pengguna');

            $table->foreignId('unit_id')
                  ->constrained('units') // Terhubung ke tabel 'units'
                  ->onUpdate('cascade')
                  ->onDelete('restrict')
                  ->comment('FK ke units: unit kerja pengguna');
        });
    }

    /**
     * Balikkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};