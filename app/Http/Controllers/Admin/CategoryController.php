<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);
        $selectedRestaurantId = $request->input('restaurant_id')
            ? (int) $request->input('restaurant_id')
            : $restaurants->first()?->id;

        $categories = collect();

        if ($selectedRestaurantId) {
            $categories = Category::where('restaurant_id', $selectedRestaurantId)
                ->where('is_master', true)
                ->withCount('items')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        return view('admin.categories', compact('categories', 'restaurants', 'selectedRestaurantId'));
    }

    public function store(Request $request)
    {
        $restaurantId = (int) $request->input('restaurant_id');
        abort_unless($restaurantId && Restaurant::where('id', $restaurantId)->exists(), 422, 'Select a restaurant.');

        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
        }

        Category::create([
            'restaurant_id' => $restaurantId,
            'is_master'     => true,
            'name'          => $request->name,
            'image'         => $path,
            'sort_order'    => (int) $request->input('sort_order', 0),
            'is_active'     => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.categories.index', ['restaurant_id' => $restaurantId])
            ->with('success', 'Category added successfully.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
        ]);

        $path = $category->image;
        if ($request->hasFile('image')) {
            if ($category->image) Storage::disk('public')->delete($category->image);
            $path = $request->file('image')->store('categories', 'public');
        }

        $category->update([
            'name'       => $request->name,
            'image'      => $path,
            'sort_order' => (int) $request->input('sort_order', $category->sort_order),
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.categories.index', ['restaurant_id' => $category->restaurant_id])
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $restaurantId = $category->restaurant_id;
        if ($category->image) Storage::disk('public')->delete($category->image);
        $category->delete();

        return redirect()->route('admin.categories.index', ['restaurant_id' => $restaurantId])
            ->with('success', 'Category deleted.');
    }
}
