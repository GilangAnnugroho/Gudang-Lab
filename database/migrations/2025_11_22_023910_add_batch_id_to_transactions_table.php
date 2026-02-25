<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Kolom batch_id boleh null karena transaksi lama belum punya batch
            $table->unsignedBigInteger('batch_id')->nullable()->after('item_variant_id');

            // Foreign key ke item_batches
            $table->foreign('batch_id')
                  ->references('id')
                  ->on('item_batches')
                  ->nullOnDelete(); // kalau batch dihapus, batch_id = null
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropColumn('batch_id');
        });
    }
};
