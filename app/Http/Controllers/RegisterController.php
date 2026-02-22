<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function show()
    {
        return view('register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.unique' => 'This email is already registered.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.confirmed' => 'Passwords do not match.',
        ]);

        // Auto-create a restaurant for the new user
        $restaurantName = $validated['name'] . "'s Restaurant";
        $restaurant = Restaurant::create([
            'name' => $restaurantName,
            'slug' => Str::slug($restaurantName) . '-' . Str::random(4),
            'is_active' => true,
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'restaurant_admin',
            'restaurant_id' => $restaurant->id,
            'status' => 'active',
        ]);

        Auth::login($user);

        // Set the restaurant context in session
        session(['current_restaurant_id' => $restaurant->id]);

        return redirect()->route('pos')->with('success', 'Welcome! Your restaurant has been created. Start by adding categories and items.');
    }
}
