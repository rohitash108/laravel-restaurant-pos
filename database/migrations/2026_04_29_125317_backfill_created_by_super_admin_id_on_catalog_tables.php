<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Find the platform owner (admin_level = 'owner', no created_by)
        $owner = DB::table('users')
            ->where('role', 'super_admin')
            ->where('admin_level', 'owner')
            ->whereNull('created_by_super_admin_id')
            ->orderBy('id')
            ->first();

        if (! $owner) {
            return; // No owner found — skip (fresh install with no data)
        }

        // Assign all orphaned global categories to the owner
        DB::table('categories')
            ->whereNull('restaurant_id')
            ->whereNull('created_by_super_admin_id')
            ->update([
                'created_by_super_admin_id' => $owner->id,
                'updated_at' => now(),
            ]);

        // Assign all orphaned master items to the owner
        DB::table('items')
            ->where('is_master', true)
            ->whereNull('restaurant_id')
            ->whereNull('created_by_super_admin_id')
            ->update([
                'created_by_super_admin_id' => $owner->id,
                'updated_at' => now(),
            ]);

        // Assign all orphaned master addons (restaurant_id IS NULL) to the owner
        DB::table('addons')
            ->whereNull('restaurant_id')
            ->whereNull('created_by_super_admin_id')
            ->update([
                'created_by_super_admin_id' => $owner->id,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        // Non-reversible data backfill
    }
};
