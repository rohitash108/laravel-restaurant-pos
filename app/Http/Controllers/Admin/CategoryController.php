<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $query = Category::whereNull('restaurant_id')
            ->withCount('items')
            ->orderBy('sort_order')
            ->orderBy('name');

        $visibleIds = auth()->user()->visibleSuperAdminIds();
        if ($visibleIds !== null) {
            $query->whereIn('created_by_super_admin_id', $visibleIds);
        }

        return view('admin.categories', ['categories' => $query->get()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
        }

        Category::create([
            'restaurant_id' => null,
            'created_by_super_admin_id' => auth()->id(),
            'is_master'     => true,
            'name'          => $request->name,
            'image'         => $path,
            'sort_order'    => (int) $request->input('sort_order', 0),
            'is_active'     => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.categories.index')
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

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->image) Storage::disk('public')->delete($category->image);
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted.');
    }
}
