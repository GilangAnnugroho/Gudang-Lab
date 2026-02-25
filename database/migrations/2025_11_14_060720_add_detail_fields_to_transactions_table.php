<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Info dokumen & pembayaran
            $table->string('invoice_no')->nullable()->after('doc_no');
            // Relasi ke permintaan (optional)
            $table->foreignId('request_id')->nullable()->after('unit_id')
                  ->constrained('requests')->nullOnDelete();

            // Detail barang batch
            $table->string('lot_number')->nullable()->after('item_variant_id');
            $table->date('expiration_date')->nullable()->after('lot_number');

            // Harga
            $table->decimal('price', 15, 2)->nullable()->after('quantity');
            $table->decimal('tax_amount', 15, 2)->nullable()->after('price');
            $table->decimal('total_amount', 15, 2)->nullable()->after('tax_amount');

            // Pembayaran & kondisi simpan
            $table->enum('payment_status', ['LUNAS', 'HUTANG'])
                  ->nullable()
                  ->after('note');
            $table->string('storage_condition')->nullable()->after('payment_status');

            // Audit
            $table->foreignId('created_by')->nullable()->after('storage_condition')
                  ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // drop FK dulu
            $table->dropForeign(['request_id']);
            $table->dropForeign(['created_by']);

            // lalu kolomnya
            $table->dropColumn([
                'invoice_no',
                'request_id',
                'lot_number',
                'expiration_date',
                'price',
                'tax_amount',
                'total_amount',
                'payment_status',
                'storage_condition',
                'created_by',
            ]);
        });
    }
};
