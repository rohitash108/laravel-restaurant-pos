<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            if (!Schema::hasColumn('restaurants', 'address2')) {
                $table->string('address2')->nullable()->after('address');
            }
            if (!Schema::hasColumn('restaurants', 'country')) {
                $table->string('country', 100)->nullable()->after('address2');
            }
            if (!Schema::hasColumn('restaurants', 'state')) {
                $table->string('state', 100)->nullable()->after('country');
            }
            if (!Schema::hasColumn('restaurants', 'city')) {
                $table->string('city', 100)->nullable()->after('state');
            }
            if (!Schema::hasColumn('restaurants', 'pincode')) {
                $table->string('pincode', 20)->nullable()->after('city');
            }
        });
    }

    public function down(): void
    {
        Schema::table('restaurants', function (Blueprint $table) {
            $table->dropColumn(['address2', 'country', 'state', 'city', 'pincode']);
        });
    }
};
