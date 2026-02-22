<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Order;

class InvoicesController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        $invoices = $restaurantId
            ? Order::where('restaurant_id', $restaurantId)->with(['table', 'items'])->latest()->paginate(15)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

        return view('invoices', compact('invoices'));
    }

    public function show(Order $order)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId || (int) $order->restaurant_id !== (int) $restaurantId) {
            abort(404);
        }
        $order->load(['restaurant', 'table', 'items']);

        return view('invoice-details', compact('order'));
    }
}
