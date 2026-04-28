<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Password reset link sent to your email.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => $password])
                     ->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Password reset successfully. Please log in.')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
