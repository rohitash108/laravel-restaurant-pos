<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function requirePermission(string $module, string $action): void
    {
        $user = auth()->user();
        if ($user && ! $user->hasPermission($module, $action)) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }
}
