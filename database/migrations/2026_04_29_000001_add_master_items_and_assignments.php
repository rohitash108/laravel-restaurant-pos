<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Allow items to be "master" (not tied to a restaurant)
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->nullable()->change();
            $table->boolean('is_master')->default(false)->after('restaurant_id');
        });

        // 2. Allow categories to be "master" too
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('restaurant_id')->nullable()->change();
            $table->boolean('is_master')->default(false)->after('restaurant_id');
        });

        // 3. Pivot: which master items are assigned to which restaurants
        Schema::create('restaurant_item_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->decimal('price_override', 10, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['restaurant_id', 'item_id']);
            $table->index(['restaurant_id', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_item_assignments');

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_master');
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('is_master');
        });
    }
};
