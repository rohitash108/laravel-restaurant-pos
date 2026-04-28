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

    /**
     * Thermal-friendly receipt print (58mm) for an order.
     */
    public function receipt(Order $order)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId || (int) $order->restaurant_id !== (int) $restaurantId) {
            abort(404);
        }

        $order->load(['restaurant', 'table', 'items']);

        return view('receipt-print', compact('order'));
    }

    /**
     * Kitchen Order Ticket (KOT) — items and quantities only, no pricing.
     */
    public function kot(Order $order)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId || (int) $order->restaurant_id !== (int) $restaurantId) {
            abort(404);
        }

        $order->load(['restaurant', 'table', 'items']);

        return view('kot-print', compact('order'));
    }
}
