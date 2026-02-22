<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'restaurant_id',
        'group',
        'key',
        'value',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get a setting value for a restaurant.
     */
    public static function getValue(int $restaurantId, string $group, string $key, $default = null)
    {
        $setting = static::where('restaurant_id', $restaurantId)
            ->where('group', $group)
            ->where('key', $key)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value for a restaurant.
     */
    public static function setValue(int $restaurantId, string $group, string $key, $value): static
    {
        return static::updateOrCreate(
            ['restaurant_id' => $restaurantId, 'group' => $group, 'key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get all settings for a restaurant group.
     */
    public static function getGroup(int $restaurantId, string $group): array
    {
        return static::where('restaurant_id', $restaurantId)
            ->where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Set multiple settings for a restaurant group.
     */
    public static function setGroup(int $restaurantId, string $group, array $values): void
    {
        foreach ($values as $key => $value) {
            static::setValue($restaurantId, $group, $key, $value);
        }
    }
}
