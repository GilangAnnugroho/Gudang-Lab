<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // MASUK | KELUAR
            $table->enum('type', ['MASUK','KELUAR'])->index();
            $table->date('trans_date')->index();
            $table->string('doc_no', 100)->nullable()->index();

            // Relasi ke variant (wajib)
            $table->foreignId('item_variant_id')->constrained('item_variants')->cascadeOnUpdate()->restrictOnDelete();

            // Kuantitas (>=1)
            $table->unsignedInteger('quantity');

            // Opsional metadata
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();

            $table->string('note', 255)->nullable();

            $table->timestamps();

            // Hindari duplikat dokumen (opsional)
            $table->unique(['type','doc_no','item_variant_id','trans_date'], 'uniq_doc_line');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
