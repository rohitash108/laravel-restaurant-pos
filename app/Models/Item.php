<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'category_id',
        'name',
        'description',
        'image',
        'price',
        'net_price',
        'food_type',
        'tax_id',
        'is_available',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'net_price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function addons(): HasMany
    {
        return $this->hasMany(Addon::class)->orderBy('id');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(ItemVariation::class)->orderBy('sort_order')->orderBy('name');
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }
}
