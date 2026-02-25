<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_variant_id')
                  ->constrained('item_variants')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
            $table->string('lot_number')->nullable();
            $table->date('expiration_date')->nullable();

            $table->integer('quantity_in')->default(0);
            $table->integer('quantity_out')->default(0);
            $table->integer('current_quantity')->default(0);

            $table->timestamps();

            $table->unique(
                ['item_variant_id', 'lot_number', 'expiration_date'],
                'uniq_variant_lot_exp'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_batches');
    }
};
