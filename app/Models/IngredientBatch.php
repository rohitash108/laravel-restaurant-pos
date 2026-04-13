<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngredientBatch extends Model
{
    protected $fillable = [
        'ingredient_id',
        'branch_id',
        'quantity_remaining',
        'expiry_date',
        'received_at',
        'reference',
        'unit_cost',
    ];

    protected function casts(): array
    {
        return [
            'quantity_remaining' => 'decimal:6',
            'expiry_date' => 'date',
            'received_at' => 'date',
            'unit_cost' => 'decimal:6',
        ];
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
