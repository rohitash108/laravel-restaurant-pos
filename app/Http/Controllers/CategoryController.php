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
        abort(403, 'Categories are managed by Super Admin.');
    }

    public function update(Request $request, Category $category)
    {
        abort(403, 'Categories are managed by Super Admin.');
    }

    public function destroy(Category $category)
    {
        abort(403, 'Categories are managed by Super Admin.');
    }
}
