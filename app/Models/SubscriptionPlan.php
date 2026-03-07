<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'duration_in_days',
        'price',
        'credit_amount',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'credit_amount' => 'decimal:2',
            'is_active' => 'boolean',
            'duration_in_days' => 'integer',
        ];
    }

    /**
     * Auto-generate slug from name if not provided.
     */
    protected static function booted(): void
    {
        static::creating(function (SubscriptionPlan $plan) {
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);
            }
        });
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Human-readable duration label.
     */
    public function getDurationLabelAttribute(): string
    {
        return match (true) {
            $this->duration_in_days <= 31  => 'Monthly',
            $this->duration_in_days <= 92  => 'Quarterly',
            $this->duration_in_days <= 183 => 'Half-Yearly',
            $this->duration_in_days <= 366 => 'Yearly',
            default                        => $this->duration_in_days . ' Days',
        };
    }
}
