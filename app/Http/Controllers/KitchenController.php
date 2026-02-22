<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Order;

class KitchenController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        $counts = ['pending' => 0, 'confirmed' => 0, 'preparing' => 0, 'ready' => 0, 'completed' => 0];
        $orders = collect();

        if ($restaurantId) {
            $query = Order::where('restaurant_id', $restaurantId);
            $counts = [
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'confirmed' => (clone $query)->where('status', 'confirmed')->count(),
                'preparing' => (clone $query)->where('status', 'preparing')->count(),
                'ready' => (clone $query)->where('status', 'ready')->count(),
                'completed' => (clone $query)->where('status', 'completed')->count(),
            ];
            $orders = Order::where('restaurant_id', $restaurantId)
                ->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
                ->with(['table', 'items'])
                ->latest()
                ->get();
        }

        return view('kitchen', compact('orders', 'counts'));
    }
}
