<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Collect all per-restaurant is_master categories
        $masterCats = DB::table('categories')
            ->where('is_master', true)
            ->whereNotNull('restaurant_id')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        $nameToGlobalId = [];

        foreach ($masterCats as $cat) {
            $key = strtolower(trim($cat->name));
            if (isset($nameToGlobalId[$key])) {
                continue;
            }

            // Reuse an existing global category with the same name if present
            $existing = DB::table('categories')
                ->whereNull('restaurant_id')
                ->whereRaw('LOWER(TRIM(name)) = ?', [$key])
                ->whereNull('deleted_at')
                ->first();

            if ($existing) {
                $nameToGlobalId[$key] = $existing->id;
            } else {
                $globalId = DB::table('categories')->insertGetId([
                    'restaurant_id' => null,
                    'is_master'     => true,
                    'name'          => $cat->name,
                    'image'         => $cat->image,
                    'sort_order'    => $cat->sort_order,
                    'is_active'     => $cat->is_active,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                $nameToGlobalId[$key] = $globalId;
            }
        }

        // 2. Remap items.category_id and assignment category_id to global categories
        foreach ($masterCats as $cat) {
            $key      = strtolower(trim($cat->name));
            $globalId = $nameToGlobalId[$key] ?? null;
            if (! $globalId) continue;

            DB::table('items')
                ->where('category_id', $cat->id)
                ->update(['category_id' => $globalId, 'updated_at' => now()]);

            DB::table('restaurant_item_assignments')
                ->where('category_id', $cat->id)
                ->update(['category_id' => $globalId]);
        }

        // 3. Soft-delete the old per-restaurant is_master categories (replaced by globals)
        $oldIds = DB::table('categories')
            ->where('is_master', true)
            ->whereNotNull('restaurant_id')
            ->whereNull('deleted_at')
            ->pluck('id');

        if ($oldIds->isNotEmpty()) {
            DB::table('categories')
                ->whereIn('id', $oldIds)
                ->update(['deleted_at' => now()]);
        }
    }

    public function down(): void
    {
        // Non-reversible data migration
    }
};
