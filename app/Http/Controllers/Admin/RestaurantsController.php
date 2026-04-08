<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RestaurantsController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::withCount('tables')->with('activeSubscription.plan')->latest()->paginate(12);
        return response()
            ->view('admin.restaurants.index', compact('restaurants'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    public function create()
    {
        return view('admin.restaurants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:restaurants,slug',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'create_admin' => 'boolean',
            'admin_name' => 'required_if:create_admin,1|string|max:255',
            'admin_email' => 'required_if:create_admin,1|email|unique:users,email',
            'admin_password' => 'required_if:create_admin,1|string|min:8|confirmed',
        ]);

        $slug = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->name);
        if (Restaurant::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . substr(uniqid(), -4);
        }

        DB::transaction(function () use ($request, $slug) {
            $restaurant = Restaurant::create([
                'name' => $request->name,
                'slug' => $slug,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
                'is_active' => true,
            ]);

            if ($request->boolean('create_admin')) {
                User::create([
                    'name' => $request->admin_name,
                    'email' => $request->admin_email,
                    'password' => $request->admin_password,
                    'role' => 'restaurant_admin',
                    'restaurant_id' => $restaurant->id,
                ]);
            }
        });

        return redirect()->route('admin.restaurants.index')
            ->with('success', 'Restaurant created successfully.');
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant->loadCount('tables', 'orders');
        return view('admin.restaurants.show', compact('restaurant'));
    }

    public function edit(Restaurant $restaurant)
    {
        return view('admin.restaurants.edit', compact('restaurant'));
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:restaurants,slug,' . $restaurant->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'is_active' => 'boolean',
            'admin_password' => 'nullable|string|min:8|confirmed',
        ]);

        $restaurant->update([
            'name' => $request->name,
            'slug' => $request->filled('slug') ? $request->slug : $restaurant->slug,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Optional: update the restaurant admin user's password (super admin only route)
        if ($request->filled('admin_password')) {
            $adminUser = User::where('restaurant_id', $restaurant->id)
                ->where('role', 'restaurant_admin')
                ->orderBy('id')
                ->first();

            if ($adminUser) {
                $adminUser->password = $request->admin_password; // hashed via User cast
                $adminUser->save();
            }
        }

        return redirect()->route('admin.restaurants.index')->with('success', 'Restaurant updated successfully.');
    }

    public function destroy(Restaurant $restaurant)
    {
        $restaurant->delete();
        return redirect()->route('admin.restaurants.index')->with('success', 'Restaurant deleted successfully.');
    }
}
