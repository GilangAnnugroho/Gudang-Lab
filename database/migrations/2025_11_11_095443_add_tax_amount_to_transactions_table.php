<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('transactions', function (Blueprint $t) {
            $t->decimal('tax_amount', 10, 2)->nullable()->after('unit_price');
            // Kalau ingin otomatis total:
            // $t->decimal('gross_amount', 10, 2)->storedAs('(COALESCE(unit_price,0) + COALESCE(tax_amount,0))');
        });
    }

    public function down(): void {
        Schema::table('transactions', function (Blueprint $t) {
            // $t->dropColumn('gross_amount');
            $t->dropColumn('tax_amount');
        });
    }
};