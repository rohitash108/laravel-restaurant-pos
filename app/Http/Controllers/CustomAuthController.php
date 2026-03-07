<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
            $user = Auth::user();

            // Super admin: always allow
            if ($user->isSuperAdmin()) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }

            // Restaurant user: block if subscription expired (cannot use app until renewed)
            if ($user->restaurant_id) {
                $hasActive = $user->restaurant->subscriptions()
                    ->where('status', 'active')
                    ->where('ends_at', '>=', Carbon::today())
                    ->exists();

                if (! $hasActive) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')
                        ->with('error', 'Your subscription is expired. Please contact the administrator to renew.');
                }
            }

            $request->session()->regenerate();
            session(['current_restaurant_id' => $user->restaurant_id]);

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
