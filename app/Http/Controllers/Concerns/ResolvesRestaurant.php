<?php

namespace App\Http\Controllers\Concerns;

trait ResolvesRestaurant
{
    protected function currentRestaurantId(): ?int
    {
        return session('current_restaurant_id') ?? auth()->user()?->restaurant_id;
    }
}
