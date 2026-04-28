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
        'restaurant_id',
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
        ];
    }

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
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
}
