<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), (bool) $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->isSuperAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            session(['current_restaurant_id' => Auth::user()->restaurant_id]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->with('error', 'Invalid email or password');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
