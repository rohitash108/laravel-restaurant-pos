<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('net_price', 10, 2)->nullable()->after('price');
            $table->foreignId('tax_id')->nullable()->after('food_type')->constrained('taxes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['tax_id']);
            $table->dropColumn(['net_price', 'tax_id']);
        });
    }
};
