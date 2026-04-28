<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $this->requirePermission('users', 'view');
        $restaurantId = $this->currentRestaurantId();
        $users = $restaurantId
            ? User::where('restaurant_id', $restaurantId)->orderBy('name')->get()
            : collect();

        return view('users', compact('users'));
    }

    public function store(Request $request)
    {
        $this->requirePermission('users', 'create');
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId) {
            return redirect()->route('users')->with('error', 'Restaurant not selected.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|max:50',
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|in:active,inactive',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // Model cast 'hashed' handles hashing
            'role' => $request->role,
            'restaurant_id' => $restaurantId,
            'phone' => $request->phone,
            'status' => $request->status ?? 'active',
        ]);

        return redirect()->route('users')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $this->requirePermission('users', 'edit');
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $user->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|string|max:50',
            'phone' => 'nullable|string|max:50',
            'status' => 'nullable|in:active,inactive',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => $request->status ?? 'active',
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password; // Model cast 'hashed' handles hashing
        }

        $user->update($data);

        return redirect()->route('users')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->requirePermission('users', 'delete');
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $user->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        // Don't allow deleting yourself
        if (auth()->id() === $user->id) {
            return redirect()->route('users')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users')->with('success', 'User deleted successfully.');
    }
}
