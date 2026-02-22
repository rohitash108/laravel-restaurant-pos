<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Role;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        $roles = $restaurantId
            ? Role::where('restaurant_id', $restaurantId)->orderBy('name')->get()
            : collect();

        $modules = Role::permissionModules();

        // Compute unique actions across all modules for the table header
        $allActions = [];
        foreach ($modules as $actions) {
            foreach ($actions as $a) {
                if (!in_array($a, $allActions)) {
                    $allActions[] = $a;
                }
            }
        }

        return view('role-permission', compact('roles', 'modules', 'allActions'));
    }

    public function store(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId) {
            return redirect()->route('role-permission')->with('error', 'Restaurant not selected.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,NULL,id,restaurant_id,' . $restaurantId,
        ], [
            'name.unique' => 'A role with this name already exists.',
        ]);

        // Build empty permissions
        $permissions = [];
        foreach (Role::permissionModules() as $module => $actions) {
            $permissions[$module] = [];
            foreach ($actions as $action) {
                $permissions[$module][$action] = false;
            }
        }

        Role::create([
            'restaurant_id' => $restaurantId,
            'name' => $request->name,
            'permissions' => $permissions,
        ]);

        return redirect()->route('role-permission')->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $role->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
        ]);

        // Build permissions from form data
        $modules = Role::permissionModules();
        $permissions = [];
        $inputPerms = $request->input('permissions', []);

        foreach ($modules as $module => $actions) {
            $permissions[$module] = [];
            foreach ($actions as $action) {
                $permissions[$module][$action] = !empty($inputPerms[$module][$action]);
            }
        }

        $role->update([
            'name' => $request->name,
            'permissions' => $permissions,
        ]);

        return redirect()->route('role-permission')->with('success', 'Role permissions updated successfully.');
    }

    public function destroy(Role $role)
    {
        $restaurantId = $this->currentRestaurantId();
        if (!$restaurantId || (int) $role->restaurant_id !== (int) $restaurantId) {
            abort(403, 'Unauthorized.');
        }

        $role->delete();

        return redirect()->route('role-permission')->with('success', 'Role deleted successfully.');
    }
}
