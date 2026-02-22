<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'restaurant_id',
        'customer_name',
        'customer_phone',
        'reservation_date',
        'reservation_time',
        'restaurant_table_id',
        'guests',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'restaurant_table_id');
    }
}
