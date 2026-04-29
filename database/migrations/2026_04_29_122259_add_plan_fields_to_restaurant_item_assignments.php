<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('restaurant_item_assignments', function (Blueprint $table) {
            $table->foreignId('plan_id')
                  ->nullable()
                  ->after('assigned_by')
                  ->constrained('subscription_plans')
                  ->nullOnDelete();

            // 'manual' = super admin assigned directly; 'plan' = auto-assigned via subscription plan
            $table->string('source', 20)->default('manual')->after('plan_id');

            $table->index(['plan_id', 'restaurant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('restaurant_item_assignments', function (Blueprint $table) {
            $table->dropIndex(['plan_id', 'restaurant_id']);
            $table->dropForeign(['plan_id']);
            $table->dropColumn(['plan_id', 'source']);
        });
    }
};
