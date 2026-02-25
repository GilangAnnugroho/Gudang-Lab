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
        // Tabel Induk Permintaan
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->date('request_date')->comment('Tanggal permintaan diajukan');
            // Status: PENDING, APPROVED, REJECTED, DISTRIBUTED (sesuai alur)
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'DISTRIBUTED'])->default('PENDING');
            $table->timestamps();

            // Foreign Keys
            $table->foreignId('request_user_id')
                  ->constrained('users') // Pengaju (Petugas Unit)
                  ->onUpdate('cascade')
                  ->onDelete('restrict')
                  ->comment('FK ke users: Pengaju permintaan');
            
            $table->foreignId('approver_user_id')
                  ->nullable() // NULL jika belum di-approve
                  ->constrained('users') // Kepala Lab / Kasubag TU
                  ->onUpdate('cascade')
                  ->onDelete('restrict')
                  ->comment('FK ke users: Petugas yang meng-approve');
            
            $table->foreignId('unit_id')
                  ->constrained('units') // Unit yang meminta
                  ->onUpdate('cascade')
                  ->onDelete('restrict')
                  ->comment('FK ke units: Unit tujuan permintaan');
        });

        // Tabel Detail Permintaan (Item apa saja yang diminta)
        Schema::create('request_details', function (Blueprint $table) {
            $table->id();
            // Item yang diminta (belum per Lot/Varian, hanya Master Item)
            $table->foreignId('item_master_id')
                  ->constrained('items_master')
                  ->onUpdate('cascade')
                  ->onDelete('restrict')
                  ->comment('FK ke items_master');

            $table->integer('requested_quantity')->comment('Jumlah yang diminta oleh unit');
            $table->integer('distributed_quantity')->default(0)->comment('Jumlah yang didistribusikan (diisi oleh Admin Gudang)');
            $table->text('notes')->nullable()->comment('Catatan jika jumlah didistribusikan berbeda dengan diminta');
            $table->timestamps();

            // Foreign Key
            $table->foreignId('request_id')
                  ->constrained('requests') // Terhubung ke tabel induk requests
                  ->onUpdate('cascade')
                  ->onDelete('cascade') // Jika permintaan induk dihapus, detail ikut terhapus
                  ->comment('FK ke requests: ID permintaan induk');
        });
    }

    /**
     * Balikkan migrasi (rollback).
     */
    public function down(): void
    {
        Schema::dropIfExists('request_details');
        Schema::dropIfExists('requests');
    }
};