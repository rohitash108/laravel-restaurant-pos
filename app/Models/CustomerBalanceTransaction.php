<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerBalanceTransaction extends Model
{
    const TYPE_ORDER_CHARGE = 'order_charge';
    const TYPE_PAYMENT      = 'payment';
    const TYPE_ADJUSTMENT   = 'adjustment';

    protected $fillable = [
        'customer_id',
        'restaurant_id',
        'type',
        'amount',
        'balance_after',
        'order_id',
        'user_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount'        => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
