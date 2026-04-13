<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'sku',
        'unit',
        'low_stock_threshold',
        'reorder_point',
        'track_expiry',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'low_stock_threshold' => 'decimal:6',
            'reorder_point' => 'decimal:6',
            'track_expiry' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function recipeRows(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'recipe_ingredients')->withPivot('quantity')->withTimestamps();
    }

    public function batches(): HasMany
    {
        return $this->hasMany(IngredientBatch::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function quantityOnHandForBranch(int $branchId): string
    {
        return (string) $this->batches()
            ->where('branch_id', $branchId)
            ->sum('quantity_remaining');
    }
}
