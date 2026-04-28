<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ResolvesRestaurant;

    public function sales(Request $request)
    {
        $this->requirePermission('reports', 'view');
        $restaurantId = $this->currentRestaurantId();
        $from = $request->get('from', Carbon::now()->startOfMonth()->toDateString());
        $to = $request->get('to', Carbon::now()->toDateString());

        $orders = collect();
        $totalSales = 0;
        $totalOrders = 0;

        if ($restaurantId) {
            $query = Order::where('restaurant_id', $restaurantId)->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            $orders = (clone $query)->with(['table', 'items'])->latest()->paginate(20);
            $totalSales = (clone $query)->sum('total');
            $totalOrders = (clone $query)->count();
        }

        return view('sales-report', compact('orders', 'totalSales', 'totalOrders', 'from', 'to'));
    }

    public function earning(Request $request)
    {
        $this->requirePermission('reports', 'view');
        $restaurantId = $this->currentRestaurantId();
        $from = $request->get('from', Carbon::now()->startOfMonth()->toDateString());
        $to = $request->get('to', Carbon::now()->toDateString());

        $totalSales = 0;
        $totalOrders = 0;
        $orders = collect();

        if ($restaurantId) {
            $query = Order::where('restaurant_id', $restaurantId)->where('status', 'completed')->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()]);
            $totalSales = (clone $query)->sum('total');
            $totalOrders = (clone $query)->count();
            $orders = (clone $query)->with(['table'])->latest()->paginate(20);
        }

        return view('earning-report', compact('totalSales', 'totalOrders', 'orders', 'from', 'to'));
    }

    public function order(Request $request)
    {
        $this->requirePermission('reports', 'view');
        $restaurantId = $this->currentRestaurantId();
        $orders = $restaurantId
            ? Order::where('restaurant_id', $restaurantId)->with(['table', 'items'])->latest()->paginate(20)
            : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);

        return view('order-report', compact('orders'));
    }

    public function customer()
    {
        $this->requirePermission('reports', 'view');
        $restaurantId = $this->currentRestaurantId();
        $customers = collect();

        if ($restaurantId) {
            $customers = Order::where('restaurant_id', $restaurantId)
                ->selectRaw('customer_name, customer_phone, count(*) as orders_count, sum(total) as total_spent, max(created_at) as last_order_at')
                ->groupBy('customer_name', 'customer_phone')
                ->havingRaw('(customer_name IS NOT NULL AND customer_name != "") OR (customer_phone IS NOT NULL AND customer_phone != "")')
                ->orderByDesc('orders_count')
                ->get();
        }

        return view('customer-report', compact('customers'));
    }

    public function audit()
    {
        $this->requirePermission('reports', 'view');
        $restaurantId = $this->currentRestaurantId();
        $orders = $restaurantId
            ? Order::where('restaurant_id', $restaurantId)->with(['table'])->latest()->take(50)->get()
            : collect();

        return view('audit-report', compact('orders'));
    }
}
