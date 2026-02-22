<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        $taxes = $restaurantId ? Tax::where('restaurant_id', $restaurantId)->orderBy('name')->get() : collect();

        return view('tax-settings', compact('taxes'));
    }

    public function store(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId) {
            return redirect()->route('tax-settings')->with('error', 'Restaurant not selected.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:inclusive,exclusive',
        ]);

        Tax::create([
            'restaurant_id' => $restaurantId,
            'name' => $request->name,
            'rate' => $request->rate,
            'type' => $request->type,
        ]);

        return redirect()->route('tax-settings')->with('success', 'Tax added successfully.');
    }

    public function update(Request $request, Tax $tax)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $tax->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:inclusive,exclusive',
        ]);

        $tax->update([
            'name' => $request->name,
            'rate' => $request->rate,
            'type' => $request->type,
        ]);

        return redirect()->route('tax-settings')->with('success', 'Tax updated successfully.');
    }

    public function destroy(Tax $tax)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $tax->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $tax->delete();

        return redirect()->route('tax-settings')->with('success', 'Tax deleted successfully.');
    }
}
