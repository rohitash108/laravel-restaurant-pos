<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('restaurant_table_id')->constrained()->nullOnDelete();
            $table->decimal('received_amount', 12, 2)->nullable()->after('total');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->decimal('balance', 12, 2)->default(0)->after('image'); // positive = credit, negative = owes
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropColumn('received_amount');
        });
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
};
