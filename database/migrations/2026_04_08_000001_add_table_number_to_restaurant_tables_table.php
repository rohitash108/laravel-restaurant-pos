<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->unsignedSmallInteger('table_number')->nullable()->after('name');
            $table->index(['restaurant_id', 'table_number']);
        });
    }

    public function down(): void
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->dropIndex(['restaurant_id', 'table_number']);
            $table->dropColumn('table_number');
        });
    }
};

