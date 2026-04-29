<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Addon;
use App\Models\Category;
use App\Models\Item;
use App\Models\ItemVariation;
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
            $items = collect();
            $categories = collect();
            $taxes = collect();
        } else {
            $items = Item::forRestaurant($restaurantId)
                ->with(['category', 'variations', 'addons', 'tax'])
                ->orderBy('category_id')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
            $categories = collect();
            $taxes = collect();
        }

        return view('items', compact('items', 'categories', 'taxes'));
    }

    public function store(Request $request)
    {
        abort(403, 'Items are managed by Super Admin.');
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('items')->with('error', 'No restaurant selected.');
        }

        $request->merge([
            'variations' => $this->filterFilledVariations($this->normalizeVariationsAddons($request->input('variations', []))),
            'addons' => $this->filterFilledAddons($this->normalizeVariationsAddons($request->input('addons', []))),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'net_price' => 'nullable|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
            'food_type' => 'in:veg,non_veg,egg',
            'image' => 'nullable|image|max:5120', // 5MB
            'variations' => 'nullable|array',
            'variations.*.name' => 'required_with:variations|string|max:100',
            'variations.*.price' => 'nullable|numeric|min:0',
            'addons' => 'nullable|array',
            'addons.*.name' => 'required_with:addons|string|max:255',
            'addons.*.price' => 'nullable|numeric|min:0',
        ]);

        $category = Category::where('restaurant_id', $restaurantId)->findOrFail($request->category_id);
        if ($request->tax_id && Tax::where('restaurant_id', $restaurantId)->where('id', $request->tax_id)->doesntExist()) {
            return redirect()->route('items')->with('error', 'Invalid tax.')->withInput();
        }

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
        }

        $item = Item::create([
            'restaurant_id' => $restaurantId,
            'category_id' => $category->id,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $path,
            'price' => $request->price,
            'net_price' => $request->net_price,
            'food_type' => $request->food_type ?? 'veg',
            'tax_id' => $request->tax_id,
        ]);

        $this->syncVariations($item, $request->variations ?? []);
        $this->syncAddons($item, $request->addons ?? [], $restaurantId);

        return redirect()->route('items')->with('success', 'Item added successfully.');
    }

    public function update(Request $request, Item $item)
    {
        abort(403, 'Items are managed by Super Admin.');

        $request->merge([
            'variations' => $this->filterFilledVariations($this->normalizeVariationsAddons($request->input('variations', []))),
            'addons' => $this->filterFilledAddons($this->normalizeVariationsAddons($request->input('addons', []))),
        ]);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => ['required', 'exists:categories,id', function ($attr, $value, $fail) use ($item) {
                $cat = Category::find($value);
                if ($cat && $cat->restaurant_id !== $item->restaurant_id) {
                    $fail('The selected category does not belong to your restaurant.');
                }
            }],
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0',
            'net_price' => 'nullable|numeric|min:0',
            'tax_id' => 'nullable|exists:taxes,id',
            'food_type' => 'in:veg,non_veg,egg',
            'image' => 'nullable|image|max:5120',
            'variations' => 'nullable|array',
            'variations.*.name' => 'required_with:variations|string|max:100',
            'variations.*.price' => 'nullable|numeric|min:0',
            'addons' => 'nullable|array',
            'addons.*.name' => 'required_with:addons|string|max:255',
            'addons.*.price' => 'nullable|numeric|min:0',
        ]);

        if ($request->tax_id && Tax::where('restaurant_id', $item->restaurant_id)->where('id', $request->tax_id)->doesntExist()) {
            return redirect()->route('items')->with('error', 'Invalid tax.')->withInput();
        }

        $path = $item->image;
        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $path = $request->file('image')->store('items', 'public');
        }

        $item->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'image' => $path,
            'price' => $request->price,
            'net_price' => $request->net_price,
            'tax_id' => $request->tax_id,
            'food_type' => $request->food_type ?? 'veg',
        ]);

        $this->syncVariations($item, $request->variations ?? []);
        $this->syncAddons($item, $request->addons ?? [], $item->restaurant_id);

        return redirect()->route('items')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        abort(403, 'Items are managed by Super Admin.');
    }

    public function hide(Item $item)
    {
        abort(403, 'Items are managed by Super Admin.');
    }

    /**
     * Normalize variations/addons from request: if PHP parsed as transposed
     * (e.g. ['name' => [a,b], 'price' => [x,y]]), convert to row-based
     * [0 => ['name'=>a,'price'=>x], 1 => ['name'=>b,'price'=>y]].
     */
    private function normalizeVariationsAddons(array $arr): array
    {
        if (empty($arr)) {
            return [];
        }
        if (isset($arr['name']) && isset($arr['price']) && is_array($arr['name']) && is_array($arr['price'])) {
            $names = $arr['name'];
            $prices = $arr['price'];
            $out = [];
            $len = max(count($names), count($prices));
            for ($i = 0; $i < $len; $i++) {
                $out[] = [
                    'name' => $names[$i] ?? '',
                    'price' => $prices[$i] ?? '',
                ];
            }
            return $out;
        }
        return $arr;
    }

    private function filterFilledVariations(array $variations): array
    {
        return array_values(array_filter($variations, function ($row) {
            return ! empty(trim((string) ($row['name'] ?? '')));
        }));
    }

    private function filterFilledAddons(array $addons): array
    {
        return array_values(array_filter($addons, function ($row) {
            return ! empty(trim((string) ($row['name'] ?? '')));
        }));
    }

    private function syncVariations(Item $item, array $variations): void
    {
        $item->variations()->delete();
        foreach ($variations as $i => $row) {
            if (empty(trim((string) ($row['name'] ?? '')))) {
                continue;
            }
            $item->variations()->create([
                'name' => trim($row['name']),
                'price' => isset($row['price']) ? (float) $row['price'] : 0,
                'sort_order' => $i,
            ]);
        }
    }

    private function syncAddons(Item $item, array $addons, int $restaurantId): void
    {
        $item->addons()->delete();
        foreach ($addons as $row) {
            if (empty(trim((string) ($row['name'] ?? '')))) {
                continue;
            }
            Addon::create([
                'restaurant_id' => $restaurantId,
                'item_id' => $item->id,
                'addon_name' => trim($row['name']),
                'price' => isset($row['price']) ? (float) $row['price'] : 0,
                'status' => 'active',
            ]);
        }
    }
}
