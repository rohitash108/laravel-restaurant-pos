<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'permissions',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
        ];
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Check if this role has a specific permission.
     */
    public function hasPermission(string $module, string $action): bool
    {
        $perms = $this->permissions ?? [];
        return !empty($perms[$module][$action]);
    }

    /**
     * Default permission modules for the POS system.
     */
    public static function permissionModules(): array
    {
        return [
            'dashboard' => ['view'],
            'orders' => ['view', 'create', 'edit', 'delete'],
            'pos' => ['view', 'create'],
            'kitchen' => ['view', 'update_status'],
            'categories' => ['view', 'create', 'edit', 'delete'],
            'items' => ['view', 'create', 'edit', 'delete'],
            'addons' => ['view', 'create', 'edit', 'delete'],
            'tables' => ['view', 'create', 'edit', 'delete'],
            'customers' => ['view', 'create', 'edit', 'delete'],
            'coupons' => ['view', 'create', 'edit', 'delete'],
            'reservations' => ['view', 'create', 'edit', 'delete'],
            'invoices' => ['view'],
            'payments' => ['view'],
            'reports' => ['view'],
            'users' => ['view', 'create', 'edit', 'delete'],
            'settings' => ['view', 'edit'],
        ];
    }
}
