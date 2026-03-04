<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Order;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;

class RestaurantTableController extends Controller
{
    use ResolvesRestaurant;

    public function index(Request $request)
    {
        $user = auth()->user();
        $restaurantId = $this->currentRestaurantId();

        if ($user->isSuperAdmin() && $request->filled('restaurant_id')) {
            session(['current_restaurant_id' => $request->restaurant_id]);
            return redirect()->route('table');
        }

        if (! $restaurantId) {
            if ($user->isSuperAdmin()) {
                $restaurants = \App\Models\Restaurant::orderBy('name')->get();
                return view('table-select-restaurant', compact('restaurants'));
            }
            abort(403, 'No restaurant context.');
        }

        $tables = RestaurantTable::with('restaurant')->where('restaurant_id', $restaurantId)
            ->orderByRaw("CASE WHEN floor IS NULL OR floor = '' THEN 1 ELSE 0 END")
            ->orderBy('floor')
            ->orderBy('name')
            ->get();

        $occupiedTableIds = Order::where('restaurant_id', $restaurantId)
            ->active()
            ->whereNotNull('restaurant_table_id')
            ->pluck('restaurant_table_id')
            ->unique()
            ->values()
            ->all();

        $restaurant = \App\Models\Restaurant::find($restaurantId);

        return view('table', compact('tables', 'restaurant', 'occupiedTableIds'));
    }

    public function store(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            abort(403, 'No restaurant context.');
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($restaurantId, $request) {
                    $floor = $request->filled('floor') ? trim((string) $request->floor) : null;
                    $query = RestaurantTable::where('restaurant_id', $restaurantId)->where('name', $value);
                    if ($floor === null || $floor === '') {
                        $query->where(function ($q) {
                            $q->whereNull('floor')->orWhere('floor', '');
                        });
                    } else {
                        $query->where('floor', $floor);
                    }
                    if ($query->exists()) {
                        $fail('A table with this name already exists on this floor. Use a different name or floor.');
                    }
                },
            ],
            'floor' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:1|max:50',
            'status' => 'in:available,occupied,reserved',
        ]);

        $baseSlug = \Illuminate\Support\Str::slug($request->name) ?: 'table';
        $slug = $baseSlug;
        $n = 1;
        while (RestaurantTable::where('restaurant_id', $restaurantId)->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . (++$n);
        }

        try {
            RestaurantTable::create([
                'restaurant_id' => $restaurantId,
                'name' => $request->name,
                'slug' => $slug,
                'floor' => $request->floor,
                'capacity' => (int) ($request->capacity ?? 4),
                'status' => $request->status ?? 'available',
            ]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return redirect()->route('table')->with('error', 'A table with this name already exists. Please use a different name.')->withInput();
        }

        return redirect()->route('table')->with('success', 'Table added successfully.');
    }

    public function update(Request $request, RestaurantTable $table)
    {
        if ($table->restaurant_id !== $this->currentRestaurantId()) {
            abort(403);
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($table, $request) {
                    $floor = $request->filled('floor') ? trim((string) $request->floor) : null;
                    $query = RestaurantTable::where('restaurant_id', $table->restaurant_id)
                        ->where('name', $value)
                        ->where('id', '!=', $table->id);
                    if ($floor === null || $floor === '') {
                        $query->where(function ($q) {
                            $q->whereNull('floor')->orWhere('floor', '');
                        });
                    } else {
                        $query->where('floor', $floor);
                    }
                    if ($query->exists()) {
                        $fail('A table with this name already exists on this floor. Use a different name or floor.');
                    }
                },
            ],
            'floor' => 'nullable|string|max:50',
            'capacity' => 'nullable|integer|min:1|max:50',
            'status' => 'in:available,occupied,reserved',
        ]);

        $table->update([
            'name' => $request->name,
            'floor' => $request->floor,
            'capacity' => (int) ($request->capacity ?? 4),
            'status' => $request->status ?? $table->status,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('table')->with('success', 'Table updated successfully.');
    }

    public function destroy(RestaurantTable $table)
    {
        if ($table->restaurant_id !== $this->currentRestaurantId()) {
            abort(403);
        }
        $table->delete();
        return redirect()->route('table')->with('success', 'Table deleted successfully.');
    }

    /**
     * Printable QR standee for a single table.
     */
    public function printCard(RestaurantTable $table)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId || (int) $table->restaurant_id !== (int) $restaurantId) {
            abort(403);
        }

        $restaurant = $table->restaurant;

        return view('table-qr-print', compact('table', 'restaurant'));
    }
}
