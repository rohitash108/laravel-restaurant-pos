<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantItemAssignment extends Model
{
    protected $fillable = [
        'restaurant_id',
        'item_id',
        'category_id',
        'price_override',
        'is_available',
        'assigned_by',
    ];

    protected function casts(): array
    {
        return ['is_available' => 'boolean'];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
