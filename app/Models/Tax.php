<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'rate',
        'type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:4',
            'is_active' => 'boolean',
        ];
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
