<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Item;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    public function index(Request $request)
    {
        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);

        $selectedRestaurantId = $request->input('restaurant_id')
            ? (int) $request->input('restaurant_id')
            : $restaurants->first()?->id;

        $addons = collect();
        $items  = collect();

        if ($selectedRestaurantId) {
            $addons = Addon::where('restaurant_id', $selectedRestaurantId)
                ->with('item')
                ->orderBy('addon_name')
                ->get();

            $items = Item::forRestaurant($selectedRestaurantId)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        return view('admin.addons', compact('addons', 'items', 'restaurants', 'selectedRestaurantId'));
    }

    public function store(Request $request)
    {
        $restaurantId = (int) $request->input('restaurant_id');
        abort_unless($restaurantId && Restaurant::where('id', $restaurantId)->exists(), 422, 'Select a restaurant.');

        $request->validate([
            'item_id'    => ['required', 'exists:items,id', function ($attr, $val, $fail) use ($restaurantId) {
                if (Item::where('id', $val)->where('restaurant_id', $restaurantId)->doesntExist()) {
                    $fail('The selected item does not belong to this restaurant.');
                }
            }],
            'addon_name' => 'required|string|max:255',
            'price'      => 'required|numeric|min:0',
            'description'=> 'nullable|string|max:1000',
            'status'     => 'nullable|in:active,inactive',
        ]);

        Addon::create([
            'restaurant_id' => $restaurantId,
            'item_id'       => $request->item_id,
            'addon_name'    => $request->addon_name,
            'price'         => $request->price,
            'description'   => $request->description,
            'status'        => $request->input('status', 'active'),
        ]);

        return redirect()->route('admin.addons.index', ['restaurant_id' => $restaurantId])
            ->with('success', 'Addon added successfully.');
    }

    public function update(Request $request, Addon $addon)
    {
        $request->validate([
            'item_id'    => 'required|exists:items,id',
            'addon_name' => 'required|string|max:255',
            'price'      => 'required|numeric|min:0',
            'description'=> 'nullable|string|max:1000',
            'status'     => 'nullable|in:active,inactive',
        ]);

        $addon->update([
            'item_id'     => $request->item_id,
            'addon_name'  => $request->addon_name,
            'price'       => $request->price,
            'description' => $request->description,
            'status'      => $request->input('status', 'active'),
        ]);

        return redirect()->route('admin.addons.index', ['restaurant_id' => $addon->restaurant_id])
            ->with('success', 'Addon updated.');
    }

    public function destroy(Addon $addon)
    {
        $restaurantId = $addon->restaurant_id;
        $addon->delete();

        return redirect()->route('admin.addons.index', ['restaurant_id' => $restaurantId])
            ->with('success', 'Addon deleted.');
    }
}
