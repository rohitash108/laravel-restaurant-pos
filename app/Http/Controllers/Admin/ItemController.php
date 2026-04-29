<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Category;
use App\Models\Item;
use App\Models\Restaurant;
use App\Models\RestaurantItemAssignment;
use App\Models\SubscriptionPlan;
use App\Models\Tax;
use App\Services\PlanItemSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    // LIST — two tabs: Master Items  |  Restaurant Items
    // ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);

        $selectedRestaurantId = $request->input('restaurant_id')
            ? (int) $request->input('restaurant_id')
            : null;

        $visibleIds = auth()->user()->visibleSuperAdminIds(); // null = owner (no filter)

        // Master items — scoped to what this user is allowed to see
        $masterItemsQuery = Item::master()
            ->with(['category', 'variations', 'addons', 'assignedRestaurants', 'plans'])
            ->orderBy('name');

        if ($visibleIds !== null) {
            $masterItemsQuery->whereIn('created_by_super_admin_id', $visibleIds);
        }

        $masterItems = $masterItemsQuery->get();

        // Restaurant-specific items
        $restaurantItems = collect();
        $taxes           = collect();

        if ($selectedRestaurantId) {
            $restaurantItems = Item::forRestaurant($selectedRestaurantId)
                ->with(['category', 'variations', 'addons', 'tax'])
                ->orderBy('category_id')->orderBy('sort_order')->orderBy('name')
                ->get();

            $taxes = Tax::where('restaurant_id', $selectedRestaurantId)
                ->where('is_active', true)->get();
        }

        // Global categories — scoped to what this user is allowed to see
        $globalCategoriesQuery = Category::whereNull('restaurant_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($visibleIds !== null) {
            $globalCategoriesQuery->whereIn('created_by_super_admin_id', $visibleIds);
        }

        $globalCategories = $globalCategoriesQuery->get();

        $plans = SubscriptionPlan::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('admin.items', compact(
            'masterItems', 'restaurantItems', 'globalCategories',
            'taxes', 'restaurants', 'selectedRestaurantId', 'plans'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // CREATE — master item (no restaurant_id) OR restaurant item
    // ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $isMaster     = $request->boolean('is_master');
        $restaurantId = $isMaster ? null : (int) $request->input('restaurant_id');

        if (!$isMaster) {
            abort_unless($restaurantId && Restaurant::where('id', $restaurantId)->exists(), 422, 'Select a restaurant.');
        }

        $request->merge([
            'variations' => $this->normalise($request->input('variations', [])),
            'addons'     => $this->normalise($request->input('addons', [])),
        ]);

        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'net_price'   => 'nullable|numeric|min:0',
            'food_type'   => 'in:veg,non_veg,egg',
            'image'       => 'nullable|image|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
        }

        $item = Item::create([
            'restaurant_id' => $restaurantId,
            'created_by_super_admin_id' => auth()->id(),
            'is_master'     => $isMaster,
            'category_id'   => $request->category_id,
            'name'          => $request->name,
            'description'   => $request->description,
            'image'         => $path,
            'price'         => $request->price,
            'net_price'     => $request->net_price,
            'food_type'     => $request->food_type ?? 'veg',
            'tax_id'        => $request->tax_id ?: null,
        ]);

        $this->syncVariations($item, $request->variations ?? []);
        if (!$isMaster) {
            $this->syncAddons($item, $request->addons ?? [], $restaurantId);
        }

        // Assign item to selected plans and propagate to active-plan restaurants
        if ($isMaster) {
            $planIds = array_filter(array_map('intval', $request->input('plan_ids', [])));
            $item->plans()->sync($planIds);
            $syncService = app(PlanItemSyncService::class);
            foreach ($item->plans as $plan) {
                $syncService->propagateItemToActivePlanRestaurants($item, $plan);
            }
        }

        $redirect = $isMaster
            ? route('admin.items.index')
            : route('admin.items.index', ['restaurant_id' => $restaurantId]);

        return redirect($redirect)->with('success', 'Item created successfully.');
    }

    // ──────────────────────────────────────────────────────────────
    // UPDATE
    // ──────────────────────────────────────────────────────────────
    public function update(Request $request, Item $item)
    {
        $request->merge([
            'variations' => $this->normalise($request->input('variations', [])),
            'addons'     => $this->normalise($request->input('addons', [])),
        ]);

        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'net_price'   => 'nullable|numeric|min:0',
            'food_type'   => 'in:veg,non_veg,egg',
            'image'       => 'nullable|image|max:5120',
        ]);

        $path = $item->image;
        if ($request->hasFile('image')) {
            if ($item->image) Storage::disk('public')->delete($item->image);
            $path = $request->file('image')->store('items', 'public');
        }

        $item->update([
            'name'        => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'image'       => $path,
            'price'       => $request->price,
            'net_price'   => $request->net_price,
            'food_type'   => $request->food_type ?? 'veg',
            'tax_id'      => $request->tax_id ?: null,
        ]);

        $this->syncVariations($item, $request->variations ?? []);
        if (!$item->is_master) {
            $this->syncAddons($item, $request->addons ?? [], $item->restaurant_id);
        }

        // Sync plan assignments for master items
        if ($item->is_master) {
            $planIds     = array_filter(array_map('intval', $request->input('plan_ids', [])));
            $currentIds  = $item->plans()->pluck('subscription_plans.id')->toArray();
            $added       = array_diff($planIds, $currentIds);
            $removed     = array_diff($currentIds, $planIds);

            $item->plans()->sync($planIds);
            $syncService = app(PlanItemSyncService::class);

            foreach ($added as $planId) {
                $plan = SubscriptionPlan::find($planId);
                if ($plan) $syncService->propagateItemToActivePlanRestaurants($item, $plan);
            }
            foreach ($removed as $planId) {
                $plan = SubscriptionPlan::find($planId);
                if ($plan) $syncService->retractItemFromPlanRestaurants($item, $plan);
            }
        }

        $redirect = $item->is_master
            ? route('admin.items.index')
            : route('admin.items.index', ['restaurant_id' => $item->restaurant_id]);

        return redirect($redirect)->with('success', 'Item updated.');
    }

    // ──────────────────────────────────────────────────────────────
    // DELETE
    // ──────────────────────────────────────────────────────────────
    public function destroy(Item $item)
    {
        $isMaster     = $item->is_master;
        $restaurantId = $item->restaurant_id;

        if ($item->image) Storage::disk('public')->delete($item->image);
        $item->delete();

        $redirect = $isMaster
            ? route('admin.items.index')
            : route('admin.items.index', ['restaurant_id' => $restaurantId]);

        return redirect($redirect)->with('success', 'Item deleted.');
    }

    // ──────────────────────────────────────────────────────────────
    // ASSIGN PAGE — show checkboxes for all restaurants
    // The item's category is global so no per-restaurant category picker needed
    // ──────────────────────────────────────────────────────────────
    public function assignPage(Item $item)
    {
        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);

        // Current assignments keyed by restaurant_id
        $assignments = RestaurantItemAssignment::where('item_id', $item->id)
            ->get()
            ->keyBy('restaurant_id');

        $assignedIds = $assignments->keys()->toArray();

        return view('admin.item-assign', compact(
            'item', 'restaurants', 'assignedIds', 'assignments'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // ASSIGN SAVE — sync assignments
    // Category is inherited from the item's global category_id
    // ──────────────────────────────────────────────────────────────
    public function assignSave(Request $request, Item $item)
    {
        $request->validate([
            'restaurant_ids'     => 'nullable|array',
            'restaurant_ids.*'   => 'exists:restaurants,id',
        ]);

        $selectedIds  = $request->input('restaurant_ids', []);

        // Remove deselected
        RestaurantItemAssignment::where('item_id', $item->id)
            ->whereNotIn('restaurant_id', $selectedIds)
            ->delete();

        // Upsert selected — category_id comes from the item's global category
        foreach ($selectedIds as $rid) {
            RestaurantItemAssignment::updateOrCreate(
                ['restaurant_id' => $rid, 'item_id' => $item->id],
                [
                    'category_id'  => $item->category_id,
                    'is_available' => true,
                    'assigned_by'  => auth()->id(),
                ]
            );
        }

        return redirect()->route('admin.items.index')
            ->with('success', "Item \"{$item->name}\" assigned to " . count($selectedIds) . " restaurant(s).");
    }

    // ──────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────
    private function normalise(array $arr): array
    {
        if (empty($arr)) return [];
        if (isset($arr['name'], $arr['price']) && is_array($arr['name'])) {
            $out = [];
            $len = max(count($arr['name']), count($arr['price']));
            for ($i = 0; $i < $len; $i++) {
                $out[] = ['name' => $arr['name'][$i] ?? '', 'price' => $arr['price'][$i] ?? ''];
            }
            return $out;
        }
        return $arr;
    }

    private function syncVariations(Item $item, array $rows): void
    {
        $item->variations()->delete();
        foreach ($rows as $i => $row) {
            if (empty(trim((string) ($row['name'] ?? '')))) continue;
            $item->variations()->create([
                'name'       => trim($row['name']),
                'price'      => (float) ($row['price'] ?? 0),
                'sort_order' => $i,
            ]);
        }
    }

    private function syncAddons(Item $item, array $rows, int $restaurantId): void
    {
        $item->addons()->delete();
        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? $row['addon_name'] ?? ''));
            if (!$name) continue;
            Addon::create([
                'restaurant_id' => $restaurantId ?: null,
                'created_by_super_admin_id' => auth()->id(),
                'item_id'       => $item->id,
                'addon_name'    => $name,
                'price'         => (float) ($row['price'] ?? 0),
                'status'        => 'active',
            ]);
        }
    }
}
