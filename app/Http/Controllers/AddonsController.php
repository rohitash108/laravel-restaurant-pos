<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Addon;
use App\Models\Item;
use Illuminate\Http\Request;

class AddonsController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard');
        }

        $addons = Addon::where('restaurant_id', $restaurantId)
            ->with('item')
            ->orderBy('addon_name')
            ->get();

        $items = Item::where('restaurant_id', $restaurantId)->orderBy('name')->get();

        return view('addons', compact('addons', 'items'));
    }

    public function store(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard')->with('error', 'Restaurant not selected.');
        }

        $validated = $request->validate(
            $this->addonRules($restaurantId),
            $this->addonMessages()
        );

        $validated['restaurant_id'] = $restaurantId;
        $validated['status'] = $validated['status'] ?? 'active';

        Addon::create($validated);

        return redirect()->route('addons')->with('success', 'Addon created successfully.');
    }

    public function update(Request $request, Addon $addon)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $addon->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate(
            $this->addonRules($restaurantId),
            $this->addonMessages()
        );

        $validated['status'] = $validated['status'] ?? 'active';
        $addon->update($validated);

        return redirect()->route('addons')->with('success', 'Addon updated successfully.');
    }

    public function destroy(Addon $addon)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $addon->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $addon->delete();

        return redirect()->route('addons')->with('success', 'Addon deleted successfully.');
    }

    /**
     * Shared validation rules for store/update.
     */
    private function addonRules(int $restaurantId): array
    {
        return [
            'item_id' => ['required', 'exists:items,id', function ($attr, $value, $fail) use ($restaurantId) {
                if (Item::where('id', $value)->where('restaurant_id', $restaurantId)->doesntExist()) {
                    $fail('The selected item does not belong to your restaurant.');
                }
            }],
            'addon_name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ];
    }

    /**
     * Shared custom validation messages.
     */
    private function addonMessages(): array
    {
        return [
            'item_id.required' => 'Item is required.',
            'addon_name.required' => 'Addon name is required.',
            'price.required' => 'Price is required.',
        ];
    }
}

