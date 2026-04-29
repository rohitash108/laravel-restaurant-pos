<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'admin_level',
        'admin_modules',
        'restaurant_id',
        'created_by_super_admin_id',
        'phone',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'admin_modules' => 'array',
        ];
    }

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_super_admin_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'super_admin' && $this->admin_level === 'owner';
    }

    public function isRestaurantAdmin(): bool
    {
        return $this->role === 'restaurant_admin';
    }

    public function hasPermission(string $module, string $action): bool
    {
        if ($this->isSuperAdmin() || $this->isRestaurantAdmin()) {
            return true;
        }

        $role = Role::where('restaurant_id', $this->restaurant_id)
            ->where('name', $this->role)
            ->first();

        return $role ? $role->hasPermission($module, $action) : false;
    }

    public function getAdminModules(): array
    {
        if (! $this->isSuperAdmin()) {
            return [];
        }

        // Owner sees everything
        if ($this->isOwner()) {
            return ['categories', 'items', 'addons', 'restaurants', 'users', 'subscriptions'];
        }

        // Manager users only see their assigned modules (categories, items, addons)
        return $this->admin_modules ?? [];
    }

    public function canAccessAdminModule(string $module): bool
    {
        if (! $this->isSuperAdmin()) {
            return false;
        }

        // Owner can access everything
        if ($this->isOwner()) {
            return true;
        }

        // Manager users: check against their assigned modules list
        return in_array($module, $this->admin_modules ?? []);
    }

    /**
     * Returns the list of super-admin user IDs whose catalog data is visible
     * to this user.
     *
     * Owner  → null  (no filter — sees everything including null-creator rows)
     * Manager → [parent_owner_id, self_id, sibling_manager_ids]
     *
     * Pass the return value to a whereIn('created_by_super_admin_id', ...) clause.
     * When null is returned, skip the whereIn entirely.
     */
    public function visibleSuperAdminIds(): ?array
    {
        if ($this->isOwner()) {
            return null; // owner sees all
        }

        $rootId = $this->created_by_super_admin_id ?? $this->id;

        return self::where('id', $rootId)
            ->orWhere('created_by_super_admin_id', $rootId)
            ->pluck('id')
            ->all();
    }
}
