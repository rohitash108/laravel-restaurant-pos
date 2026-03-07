<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'payment_qr',
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

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'restaurant_id');
    }

    /**
     * Get the current active subscription (latest one that hasn't expired).
     */
    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'restaurant_id')
            ->where('status', 'active')
            ->where('ends_at', '>=', Carbon::today())
            ->latest('ends_at');
    }

    /**
     * Check whether the restaurant has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription()->exists();
    }
}
