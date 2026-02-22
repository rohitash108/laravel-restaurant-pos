<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            $categories = collect();
        } else {
            $categories = Category::where('restaurant_id', $restaurantId)
                ->withCount('items')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        return view('categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('categories')->with('error', 'No restaurant selected.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create([
            'restaurant_id' => $restaurantId,
            'name' => $request->name,
            'sort_order' => Category::where('restaurant_id', $restaurantId)->max('sort_order') + 1,
        ]);

        return redirect()->route('categories')->with('success', 'Category added successfully.');
    }

    public function update(Request $request, Category $category)
    {
        if ($category->restaurant_id !== $this->currentRestaurantId()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update(['name' => $request->name]);

        return redirect()->route('categories')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->restaurant_id !== $this->currentRestaurantId()) {
            abort(403);
        }
        if ($category->items()->count() > 0) {
            return redirect()->route('categories')->with('error', 'Cannot delete category with items. Move or delete items first.');
        }
        $category->delete();
        return redirect()->route('categories')->with('success', 'Category deleted successfully.');
    }
}
