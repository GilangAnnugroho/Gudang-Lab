<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Kepala Lab
            $table->foreignId('item_variant_id')->constrained('item_variants')->onDelete('cascade');
            $table->date('opname_date');
            $table->integer('system_stock');   // Stok di aplikasi sebelum diubah
            $table->integer('physical_stock'); // Stok hasil hitungan
            $table->integer('difference');     // Selisih (Fisik - Sistem)
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};