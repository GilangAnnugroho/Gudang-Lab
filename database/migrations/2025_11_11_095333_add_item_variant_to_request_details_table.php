<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('request_details', function (Blueprint $t) {
            $t->foreignId('item_variant_id')->nullable()->after('item_master_id')
              ->constrained('item_variants')
              ->cascadeOnUpdate()
              ->restrictOnDelete();
        });
    }

    public function down(): void {
        Schema::table('request_details', function (Blueprint $t) {
            $t->dropForeign(['item_variant_id']);
            $t->dropColumn('item_variant_id');
        });
    }
};