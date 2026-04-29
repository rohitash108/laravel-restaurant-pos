<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminModuleAccess
{
    public function handle(Request $request, Closure $next, string $module): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isSuperAdmin()) {
            abort(403, 'Unauthorized.');
        }

        if (! $user->canAccessAdminModule($module)) {
            abort(403, 'You do not have access to this module.');
        }

        return $next($request);
    }
}
