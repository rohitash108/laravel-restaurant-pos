<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'created_by_super_admin_id',
        'category_id',
        'is_master',
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
            'price'     => 'decimal:2',
            'net_price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_master'    => 'boolean',
        ];
    }

    // Items owned directly by a restaurant OR master items assigned to it
    public function scopeForRestaurant(Builder $query, int $restaurantId): Builder
    {
        return $query->where(function ($q) use ($restaurantId) {
            $q->where('restaurant_id', $restaurantId)
              ->orWhere(function ($q2) use ($restaurantId) {
                  $q2->where('is_master', true)
                     ->whereHas('assignments', fn ($a) =>
                         $a->where('restaurant_id', $restaurantId)
                           ->where('is_available', true)
                     );
              });
        });
    }

    public function scopeMaster(Builder $query): Builder
    {
        return $query->where('is_master', true)->whereNull('restaurant_id');
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

    public function assignments(): HasMany
    {
        return $this->hasMany(RestaurantItemAssignment::class);
    }

    public function plans(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'plan_items')->withTimestamps();
    }

    public function assignedRestaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'restaurant_item_assignments')
                    ->withPivot(['price_override', 'is_available', 'assigned_by'])
                    ->withTimestamps();
    }

    public function recipeIngredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }
}
