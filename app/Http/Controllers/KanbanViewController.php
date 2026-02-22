<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Order;

class KanbanViewController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        $counts = ['pending' => 0, 'confirmed' => 0, 'preparing' => 0, 'ready' => 0, 'completed' => 0, 'cancelled' => 0];
        $ordersByStatus = [];

        if ($restaurantId) {
            $query = Order::where('restaurant_id', $restaurantId);
            foreach (['pending', 'confirmed', 'preparing', 'ready', 'completed', 'cancelled'] as $status) {
                $counts[$status] = (clone $query)->where('status', $status)->count();
            }
            $orders = Order::where('restaurant_id', $restaurantId)
                ->with(['table', 'items'])
                ->latest()
                ->get();
            $ordersByStatus = $orders->groupBy('status');
        }

        return view('kanban-view', compact('counts', 'ordersByStatus'));
    }
}
