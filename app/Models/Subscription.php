<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'subscription_plan_id',
        'starts_at',
        'ends_at',
        'amount_paid',
        'balance',
        'payment_method',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'amount_paid' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    // ─── Relationships ───────────────────────────────

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function balanceTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SubscriptionBalanceTransaction::class)->orderByDesc('created_at');
    }

    // ─── Helpers ─────────────────────────────────────

    /**
     * Whether this subscription is currently valid.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->ends_at->gte(Carbon::today());
    }

    /**
     * Whether the subscription has passed its end date.
     */
    public function isExpired(): bool
    {
        return $this->ends_at->lt(Carbon::today());
    }

    /**
     * Number of days remaining (0 if expired).
     */
    public function daysRemaining(): int
    {
        $remaining = Carbon::today()->diffInDays($this->ends_at, false);
        return max(0, (int) $remaining);
    }

    // ─── Scopes ──────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('ends_at', '>=', Carbon::today());
    }

    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere(function ($q2) {
                  $q2->where('status', 'active')
                     ->where('ends_at', '<', Carbon::today());
              });
        });
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('status', 'active')
                     ->whereBetween('ends_at', [Carbon::today(), Carbon::today()->addDays($days)]);
    }

    /**
     * Scope: subscriptions whose period overlaps the given date range.
     * Two ranges overlap when: new_start < existing_end AND new_end > existing_start.
     */
    public function scopeOverlapping($query, $startsAt, $endsAt)
    {
        $startsAt = $startsAt instanceof \DateTimeInterface ? $startsAt : Carbon::parse($startsAt);
        $endsAt = $endsAt instanceof \DateTimeInterface ? $endsAt : Carbon::parse($endsAt);

        return $query->where('starts_at', '<', $endsAt)->where('ends_at', '>', $startsAt);
    }
}
