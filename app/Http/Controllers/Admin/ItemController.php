<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Category;
use App\Models\Item;
use App\Models\Restaurant;
use App\Models\RestaurantItemAssignment;
use App\Models\Tax;
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

        // Master items (is_master = true, no restaurant)
        $masterItems = Item::master()
            ->with(['category', 'variations', 'addons', 'assignedRestaurants'])
            ->orderBy('name')
            ->get();

        // Restaurant-specific items
        $restaurantItems = collect();
        $categories      = collect();
        $taxes           = collect();

        if ($selectedRestaurantId) {
            $restaurantItems = Item::forRestaurant($selectedRestaurantId)
                ->with(['category', 'variations', 'addons', 'tax'])
                ->orderBy('category_id')->orderBy('sort_order')->orderBy('name')
                ->get();

            $categories = Category::where('restaurant_id', $selectedRestaurantId)
                ->where('is_master', true)
                ->orderBy('sort_order')->orderBy('name')->get();

            $taxes = Tax::where('restaurant_id', $selectedRestaurantId)
                ->where('is_active', true)->get();
        }

        // Only super-admin-managed categories (is_master = true), grouped by restaurant
        $allCategories = Category::with('restaurant')
            ->where('is_master', true)
            ->whereNotNull('restaurant_id')
            ->orderBy('restaurant_id')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($c) => $c->restaurant->name);

        return view('admin.items', compact(
            'masterItems', 'restaurantItems', 'categories', 'allCategories',
            'taxes', 'restaurants', 'selectedRestaurantId'
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
            'category_id' => 'nullable|exists:categories,id',
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
            'is_master'     => $isMaster,
            'category_id'   => $request->category_id ?: null,
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
            'category_id' => 'nullable|exists:categories,id',
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
            'category_id' => $request->category_id ?: null,
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
    // ──────────────────────────────────────────────────────────────
    public function assignPage(Item $item)
    {
        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);

        // Current assignments keyed by restaurant_id
        $assignments = RestaurantItemAssignment::where('item_id', $item->id)
            ->get()
            ->keyBy('restaurant_id');

        $assignedIds = $assignments->keys()->toArray();

        // Categories per restaurant (admin-managed only)
        $categoriesByRestaurant = Category::where('is_master', true)
            ->whereIn('restaurant_id', $restaurants->pluck('id'))
            ->where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')
            ->get()
            ->groupBy('restaurant_id');

        return view('admin.item-assign', compact(
            'item', 'restaurants', 'assignedIds', 'assignments', 'categoriesByRestaurant'
        ));
    }

    // ──────────────────────────────────────────────────────────────
    // ASSIGN SAVE — sync assignments with category_id per restaurant
    // ──────────────────────────────────────────────────────────────
    public function assignSave(Request $request, Item $item)
    {
        $request->validate([
            'restaurant_ids'     => 'nullable|array',
            'restaurant_ids.*'   => 'exists:restaurants,id',
            'category_ids'       => 'nullable|array',
            'category_ids.*'     => 'nullable|exists:categories,id',
        ]);

        $selectedIds  = $request->input('restaurant_ids', []);
        $categoryIds  = $request->input('category_ids', []);   // keyed by restaurant_id

        // Remove deselected
        RestaurantItemAssignment::where('item_id', $item->id)
            ->whereNotIn('restaurant_id', $selectedIds)
            ->delete();

        // Upsert selected — store category_id chosen for each restaurant
        // If admin didn't pick a category, auto-find or auto-create one using the item's category name
        foreach ($selectedIds as $rid) {
            $categoryId = $categoryIds[$rid] ?? null;

            if (! $categoryId && $item->category) {
                $cat = Category::firstOrCreate(
                    ['restaurant_id' => $rid, 'name' => $item->category->name],
                    ['is_master' => true, 'is_active' => true, 'sort_order' => 0]
                );
                $categoryId = $cat->id;
            }

            RestaurantItemAssignment::updateOrCreate(
                ['restaurant_id' => $rid, 'item_id' => $item->id],
                [
                    'category_id'  => $categoryId,
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
                'item_id'       => $item->id,
                'addon_name'    => $name,
                'price'         => (float) ($row['price'] ?? 0),
                'status'        => 'active',
            ]);
        }
    }
}
