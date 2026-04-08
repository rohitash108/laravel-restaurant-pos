<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RestaurantTable extends Model
{
    use HasFactory;

    protected $table = 'restaurant_tables';

    protected $fillable = [
        'restaurant_id',
        'name',
        'table_number',
        'slug',
        'floor',
        'capacity',
        'status',
    ];

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_OCCUPIED = 'occupied';
    public const STATUS_RESERVED = 'reserved';

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'restaurant_table_id');
    }

    public function getOrderByQrUrlAttribute(): string
    {
        return route('order.by-qr', [
            'restaurant' => $this->restaurant->slug,
            'table' => $this->slug ?? (string) $this->id,
        ]);
    }

    protected static function booted(): void
    {
        static::saving(function (RestaurantTable $model) {
            if (empty($model->slug)) {
                $base = Str::slug($model->name) ?: 'table';
                $model->slug = $base;
                $exists = static::where('restaurant_id', $model->restaurant_id)
                    ->where('slug', $model->slug)
                    ->when($model->exists, fn ($q) => $q->where('id', '!=', $model->id))
                    ->exists();
                if ($exists) {
                    $model->slug = $base . '-' . ($model->id ?: substr(uniqid(), -4));
                }
            }
        });
    }
}
