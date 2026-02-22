<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectSuperAdminToAdmin
{
    /**
     * Super admin should only access admin (restaurants). Redirect to restaurant list if they hit app routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isSuperAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
