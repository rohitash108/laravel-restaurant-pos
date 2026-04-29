<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'created_by_super_admin_id')) {
                $table->foreignId('created_by_super_admin_id')
                    ->nullable()
                    ->after('restaurant_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'created_by_super_admin_id')) {
                $table->foreignId('created_by_super_admin_id')
                    ->nullable()
                    ->after('restaurant_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        Schema::table('addons', function (Blueprint $table) {
            if (! Schema::hasColumn('addons', 'created_by_super_admin_id')) {
                $table->foreignId('created_by_super_admin_id')
                    ->nullable()
                    ->after('restaurant_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('addons', function (Blueprint $table) {
            if (Schema::hasColumn('addons', 'created_by_super_admin_id')) {
                $table->dropForeign(['created_by_super_admin_id']);
                $table->dropColumn('created_by_super_admin_id');
            }
        });

        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'created_by_super_admin_id')) {
                $table->dropForeign(['created_by_super_admin_id']);
                $table->dropColumn('created_by_super_admin_id');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'created_by_super_admin_id')) {
                $table->dropForeign(['created_by_super_admin_id']);
                $table->dropColumn('created_by_super_admin_id');
            }
        });
    }
};
