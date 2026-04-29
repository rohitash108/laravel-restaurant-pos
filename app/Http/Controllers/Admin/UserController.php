<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List all users (super_admin users + optionally filter by restaurant).
     */
    public function index(Request $request)
    {
        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);

        $selectedRestaurantId = $request->filled('restaurant_id')
            ? (int) $request->input('restaurant_id')
            : null;

        $query = User::with('restaurant')
            ->where('role', 'super_admin')
            ->where('created_by_super_admin_id', auth()->id())
            ->orderBy('name');

        $users = $query->get();

        // Available modules that can be granted to sub-users
        $availableModules = [
            'categories'    => 'Categories',
            'items'         => 'Items',
            'addons'        => 'Addons',
            'restaurants'   => 'Restaurants',
            'subscriptions' => 'Subscriptions',
        ];

        return view('admin.users', compact('users', 'restaurants', 'selectedRestaurantId', 'availableModules'));
    }

    /**
     * Create a new super admin user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6',
            'role'          => 'required|in:super_admin',
            'admin_level'   => 'nullable|string|in:owner,manager',
            'restaurant_id' => 'nullable|exists:restaurants,id',
            'phone'         => 'nullable|string|max:50',
            'status'        => 'nullable|in:active,inactive',
            'modules'       => 'nullable|array',
            'modules.*'     => 'in:categories,items,addons,restaurants,subscriptions',
        ]);

        // If role is super_admin, restaurant_id should be null
        $restaurantId = $request->role === 'super_admin' ? null : $request->restaurant_id;

        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => $request->password,
            'role'          => $request->role,
            'admin_level'   => $request->role === 'super_admin' ? ($request->admin_level ?? 'manager') : null,
            'admin_modules' => $request->role === 'super_admin'
                ? array_values(array_intersect($request->input('modules', []), ['categories', 'items', 'addons', 'restaurants', 'subscriptions']))
                : null,
            'restaurant_id' => $restaurantId,
            'created_by_super_admin_id' => auth()->id(),
            'phone'         => $request->phone,
            'status'        => $request->status ?? 'active',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" created successfully.");
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user)
    {
        if ((int) $user->created_by_super_admin_id !== (int) auth()->id()) {
            abort(403, 'You can only manage users created by you.');
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'role'          => 'required|in:super_admin',
            'admin_level'   => 'nullable|string|in:owner,manager',
            'restaurant_id' => 'nullable|exists:restaurants,id',
            'phone'         => 'nullable|string|max:50',
            'status'        => 'nullable|in:active,inactive',
            'modules'       => 'nullable|array',
            'modules.*'     => 'in:categories,items,addons,restaurants,subscriptions',
        ]);

        // Don't allow editing yourself to a non-super_admin role
        if (auth()->id() === $user->id && $request->role !== 'super_admin') {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot change your own role away from Super Admin.');
        }

        $restaurantId = $request->role === 'super_admin' ? null : $request->restaurant_id;

        $data = [
            'name'          => $request->name,
            'email'         => $request->email,
            'role'          => $request->role,
            'admin_level'   => $request->role === 'super_admin' ? ($request->admin_level ?? 'manager') : null,
            'admin_modules' => $request->role === 'super_admin'
                ? array_values(array_intersect($request->input('modules', []), ['categories', 'items', 'addons', 'restaurants', 'subscriptions']))
                : null,
            'restaurant_id' => $restaurantId,
            'phone'         => $request->phone,
            'status'        => $request->status ?? 'active',
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$user->name}\" updated successfully.");
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        if ((int) $user->created_by_super_admin_id !== (int) auth()->id()) {
            abort(403, 'You can only manage users created by you.');
        }

        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$name}\" deleted.");
    }

    /**
     * Get allowed modules for a super_admin user.
     */
    public static function getUserModules(User $user): array
    {
        return $user->getAdminModules();
    }
}
