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
        'gst_number',
        'is_active',
        // ─── Razorpay Route ──────────────────────────────────────────
        'razorpay_linked_account_id',
        'razorpay_account_status',
        'razorpay_status_reason',
        'razorpay_stakeholder_id',
        'razorpay_settlement_account_id',
        'razorpay_business_type',
        'razorpay_platform_fee_percent',
        'razorpay_kyc_data',
        'razorpay_activated_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'razorpay_kyc_data' => 'array',
            'razorpay_activated_at' => 'datetime',
            'razorpay_platform_fee_percent' => 'decimal:2',
        ];
    }

    public function isRazorpayRouteActive(): bool
    {
        return $this->razorpay_account_status === 'activated'
            && ! empty($this->razorpay_linked_account_id);
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

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
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

    /**
     * Single source of truth for the currency symbol.
     */
    public function currencySymbol(): string
    {
        return match ($this->currency ?? 'INR') {
            'INR' => '₹',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'AED' => 'AED ',
            default => $this->currency ?? '₹',
        };
    }
}
