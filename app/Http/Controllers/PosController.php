<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\Tax;
use App\Models\User;

class PosController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        $categories = collect();
        $tables = collect();
        $recentOrders = collect();

        $draftOrders = collect();
        $transactionOrders = collect();

        if ($restaurantId) {
            $categories = Category::where('restaurant_id', $restaurantId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->with(['items' => fn ($q) => $q->where('is_available', true)->orderBy('sort_order')->orderBy('name')->with(['addons' => fn ($aq) => $aq->where('status', 'active')->orderBy('id'), 'variations'])])
                ->get();
            $tables = RestaurantTable::where('restaurant_id', $restaurantId)->get();
            $recentOrders = Order::where('restaurant_id', $restaurantId)
                ->with(['table', 'items'])
                ->latest()
                ->take(20)
                ->get();
            $draftOrders = Order::where('restaurant_id', $restaurantId)
                ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_CONFIRMED, Order::STATUS_PREPARING, Order::STATUS_READY, Order::STATUS_SERVED])
                ->with(['table', 'items'])
                ->latest()
                ->take(50)
                ->get();
            $transactionOrders = Order::where('restaurant_id', $restaurantId)
                ->where('status', Order::STATUS_COMPLETED)
                ->with(['table', 'items'])
                ->latest()
                ->take(50)
                ->get();
        }

        $customers = $restaurantId
            ? Customer::where('restaurant_id', $restaurantId)->orderBy('name')->get(['id', 'name'])
            : collect();

        $waiters = $restaurantId
            ? User::where('restaurant_id', $restaurantId)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(['id', 'name'])
            : collect();

        $tax = $restaurantId ? Tax::where('restaurant_id', $restaurantId)->first() : null;
        $tax_rate = $tax ? (float) $tax->rate : 0;
        $tax_name = $tax ? $tax->name : 'Tax';

        return view('pos', compact('categories', 'tables', 'recentOrders', 'draftOrders', 'transactionOrders', 'customers', 'waiters', 'tax_rate', 'tax_name'));
    }

    /**
     * Open POS with an existing order loaded for editing (pending/confirmed/preparing/ready only).
     */
    public function editOrder(Order $order)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId || (int) $order->restaurant_id !== (int) $restaurantId) {
            abort(403);
        }
        if (in_array($order->status, [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED], true)) {
            return redirect()->route('orders')->with('error', 'Completed or cancelled orders cannot be edited.');
        }

        $order->load(['table', 'items']);

        $categories = Category::where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with(['items' => fn ($q) => $q->where('is_available', true)->orderBy('sort_order')->orderBy('name')->with(['addons' => fn ($aq) => $aq->where('status', 'active')->orderBy('id'), 'variations'])])
            ->get();
        $tables = RestaurantTable::where('restaurant_id', $restaurantId)->get();
        $recentOrders = Order::where('restaurant_id', $restaurantId)->with(['table', 'items'])->latest()->take(20)->get();
        $draftOrders = Order::where('restaurant_id', $restaurantId)
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_CONFIRMED, Order::STATUS_PREPARING, Order::STATUS_READY, Order::STATUS_SERVED])
            ->with(['table', 'items'])->latest()->take(50)->get();
        $transactionOrders = Order::where('restaurant_id', $restaurantId)
            ->where('status', Order::STATUS_COMPLETED)
            ->with(['table', 'items'])->latest()->take(50)->get();
        $customers = Customer::where('restaurant_id', $restaurantId)->orderBy('name')->get(['id', 'name']);
        $waiters = User::where('restaurant_id', $restaurantId)->where('status', 'active')->orderBy('name')->get(['id', 'name']);

        $tax = Tax::where('restaurant_id', $restaurantId)->first();
        $tax_rate = $tax ? (float) $tax->rate : 0;
        $tax_name = $tax ? $tax->name : 'Tax';

        $editOrder = $order;
        return view('pos', compact('categories', 'tables', 'recentOrders', 'draftOrders', 'transactionOrders', 'customers', 'waiters', 'editOrder', 'tax_rate', 'tax_name'));
    }
}
