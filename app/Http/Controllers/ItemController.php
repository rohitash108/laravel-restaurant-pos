<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Addon;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemVariation;
use App\Models\RestaurantItemAssignment;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return view('items', ['items' => collect(), 'categories' => collect(), 'taxes' => collect()]);
        }

        // Restaurant-owned items (not master)
        $ownItems = Item::where('restaurant_id', $restaurantId)
            ->where('is_master', false)
            ->with(['category', 'variations', 'addons', 'tax'])
            ->orderBy('category_id')->orderBy('sort_order')->orderBy('name')
            ->get();

        // All master items assigned to this restaurant (including hidden ones so admin can re-enable)
        $masterItems = Item::where('is_master', true)
            ->whereHas('assignments', fn ($q) => $q->where('restaurant_id', $restaurantId))
            ->with(['category', 'variations', 'addons', 'tax',
                    'assignments' => fn ($q) => $q->where('restaurant_id', $restaurantId)])
            ->orderBy('category_id')->orderBy('name')
            ->get();

        $items = $ownItems->merge($masterItems);

        $categories = Category::whereNull('restaurant_id')
            ->where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')
            ->get();

        $taxes = Tax::where('restaurant_id', $restaurantId)
            ->where('is_active', true)->get();

        return view('items', compact('items', 'categories', 'taxes'));
    }

    public function store(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('items')->with('error', 'No restaurant selected.');
        }

        $request->merge([
            'variations' => $this->filterFilledVariations($this->normalizeVariationsAddons($request->input('variations', []))),
            'addons'     => $this->filterFilledAddons($this->normalizeVariationsAddons($request->input('addons', []))),
        ]);

        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:2000',
            'price'       => 'required|numeric|min:0',
            'net_price'   => 'nullable|numeric|min:0',
            'tax_id'      => 'nullable|exists:taxes,id',
            'food_type'   => 'in:veg,non_veg,egg',
            'image'       => 'nullable|image|max:5120',
        ]);

        // Ensure the category is global
        Category::whereNull('restaurant_id')->findOrFail($request->category_id);

        if ($request->tax_id && Tax::where('restaurant_id', $restaurantId)->where('id', $request->tax_id)->doesntExist()) {
            return redirect()->route('items')->with('error', 'Invalid tax.')->withInput();
        }

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
        }

        $item = Item::create([
            'restaurant_id' => $restaurantId,
            'is_master'     => false,
            'category_id'   => $request->category_id,
            'name'          => $request->name,
            'description'   => $request->description,
            'image'         => $path,
            'price'         => $request->price,
            'net_price'     => $request->net_price,
            'food_type'     => $request->food_type ?? 'veg',
            'tax_id'        => $request->tax_id ?: null,
            'is_available'  => true,
        ]);

        $this->syncVariations($item, $request->variations ?? []);
        $this->syncAddons($item, $request->addons ?? [], $restaurantId);

        return redirect()->route('items')->with('success', 'Item added successfully.');
    }

    public function update(Request $request, Item $item)
    {
        $restaurantId = $this->currentRestaurantId();

        if ($item->is_master || $item->restaurant_id !== $restaurantId) {
            return redirect()->route('items')->with('error', 'Master items cannot be edited here. Contact Super Admin.');
        }

        $request->merge([
            'variations' => $this->filterFilledVariations($this->normalizeVariationsAddons($request->input('variations', []))),
            'addons'     => $this->filterFilledAddons($this->normalizeVariationsAddons($request->input('addons', []))),
        ]);

        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:2000',
            'price'       => 'required|numeric|min:0',
            'net_price'   => 'nullable|numeric|min:0',
            'tax_id'      => 'nullable|exists:taxes,id',
            'food_type'   => 'in:veg,non_veg,egg',
            'image'       => 'nullable|image|max:5120',
        ]);

        // Ensure the category is global
        Category::whereNull('restaurant_id')->findOrFail($request->category_id);

        if ($request->tax_id && Tax::where('restaurant_id', $restaurantId)->where('id', $request->tax_id)->doesntExist()) {
            return redirect()->route('items')->with('error', 'Invalid tax.')->withInput();
        }

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
            'tax_id'      => $request->tax_id ?: null,
            'food_type'   => $request->food_type ?? 'veg',
        ]);

        $this->syncVariations($item, $request->variations ?? []);
        $this->syncAddons($item, $request->addons ?? [], $item->restaurant_id);

        return redirect()->route('items')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        $restaurantId = $this->currentRestaurantId();

        if ($item->is_master || $item->restaurant_id !== $restaurantId) {
            return redirect()->route('items')->with('error', 'Only restaurant-specific items can be deleted.');
        }

        if ($item->image) Storage::disk('public')->delete($item->image);
        $item->delete();

        return redirect()->route('items')->with('success', 'Item deleted.');
    }

    public function hide(Item $item)
    {
        $restaurantId = $this->currentRestaurantId();

        if ($item->is_master) {
            $assignment = RestaurantItemAssignment::where('item_id', $item->id)
                ->where('restaurant_id', $restaurantId)
                ->first();
            if (! $assignment) {
                return redirect()->route('items')->with('error', 'Item not assigned to your restaurant.');
            }
            $assignment->update(['is_available' => ! $assignment->is_available]);
            $msg = $assignment->is_available ? 'Item is now visible.' : 'Item hidden.';
        } else {
            if ($item->restaurant_id !== $restaurantId) {
                return redirect()->route('items')->with('error', 'Unauthorized.');
            }
            $item->update(['is_available' => ! $item->is_available]);
            $msg = $item->is_available ? 'Item is now visible.' : 'Item hidden.';
        }

        return redirect()->route('items')->with('success', $msg);
    }

    private function normalizeVariationsAddons(array $arr): array
    {
        if (empty($arr)) return [];
        if (isset($arr['name'], $arr['price']) && is_array($arr['name']) && is_array($arr['price'])) {
            $out = [];
            $len = max(count($arr['name']), count($arr['price']));
            for ($i = 0; $i < $len; $i++) {
                $out[] = ['name' => $arr['name'][$i] ?? '', 'price' => $arr['price'][$i] ?? ''];
            }
            return $out;
        }
        return $arr;
    }

    private function filterFilledVariations(array $variations): array
    {
        return array_values(array_filter($variations, fn ($r) => ! empty(trim((string) ($r['name'] ?? '')))));
    }

    private function filterFilledAddons(array $addons): array
    {
        return array_values(array_filter($addons, fn ($r) => ! empty(trim((string) ($r['name'] ?? '')))));
    }

    private function syncVariations(Item $item, array $variations): void
    {
        $item->variations()->delete();
        foreach ($variations as $i => $row) {
            if (empty(trim((string) ($row['name'] ?? '')))) continue;
            $item->variations()->create([
                'name'       => trim($row['name']),
                'price'      => (float) ($row['price'] ?? 0),
                'sort_order' => $i,
            ]);
        }
    }

    private function syncAddons(Item $item, array $addons, int $restaurantId): void
    {
        $item->addons()->delete();
        foreach ($addons as $row) {
            $name = trim((string) ($row['name'] ?? $row['addon_name'] ?? ''));
            if (! $name) continue;
            Addon::create([
                'restaurant_id' => $restaurantId,
                'item_id'       => $item->id,
                'addon_name'    => $name,
                'price'         => (float) ($row['price'] ?? 0),
                'status'        => 'active',
            ]);
        }
    }
}
