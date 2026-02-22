<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'address',
        'address2',
        'country',
        'state',
        'city',
        'pincode',
        'phone',
        'email',
        'currency',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'restaurant_id');
    }

    public function tables(): HasMany
    {
        return $this->hasMany(RestaurantTable::class, 'restaurant_id');
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'restaurant_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'restaurant_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'restaurant_id');
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class, 'restaurant_id');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class, 'restaurant_id');
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class, 'restaurant_id');
    }
}
