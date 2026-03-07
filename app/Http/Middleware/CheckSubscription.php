<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Block restaurant staff/admin if their restaurant's subscription has expired.
     * Super admins are always allowed through.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // No user or super admin — skip check
        if (! $user || $user->isSuperAdmin()) {
            return $next($request);
        }

        // User must belong to a restaurant
        if (! $user->restaurant_id) {
            return $next($request);
        }

        $restaurant = $user->restaurant;

        if (! $restaurant) {
            return $next($request);
        }

        // Check for an active subscription
        $hasActive = $restaurant->subscriptions()
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

        return $next($request);
    }
}
