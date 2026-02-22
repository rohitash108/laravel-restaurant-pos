<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Reservation;
use App\Models\RestaurantTable;

class DashboardController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $user = auth()->user();
        $restaurantId = $this->currentRestaurantId();

        if ($user->isSuperAdmin()) {
            $ordersQuery = Order::query();
        } else {
            $ordersQuery = Order::where('restaurant_id', $restaurantId);
        }

        $totalOrders = (clone $ordersQuery)->count();
        $totalSales = (clone $ordersQuery)->sum('total');
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $reservationsCount = 0;
        $reservations = collect();
        if ($restaurantId && class_exists(Reservation::class)) {
            $reservationsCount = Reservation::where('restaurant_id', $restaurantId)
                ->where('reservation_date', '>=', now()->toDateString())
                ->count();
            $reservations = Reservation::where('restaurant_id', $restaurantId)
                ->with('table')
                ->where('reservation_date', '>=', now()->toDateString())
                ->orderBy('reservation_date')
                ->orderBy('reservation_time')
                ->take(5)
                ->get();
        }

        $ordersByType = (clone $ordersQuery)
            ->selectRaw('order_type, count(*) as count')
            ->groupBy('order_type')
            ->pluck('count', 'order_type')
            ->toArray();

        $recentOrders = (clone $ordersQuery)
            ->with(['table', 'items'])
            ->latest()
            ->take(5)
            ->get();

        $topSellingItems = collect();
        if ($restaurantId) {
            $topSellingItems = OrderItem::query()
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.restaurant_id', $restaurantId)
                ->selectRaw('order_items.item_name, SUM(order_items.quantity) as total_qty')
                ->groupBy('order_items.item_name')
                ->orderByDesc('total_qty')
                ->take(6)
                ->get();
        } elseif ($user->isSuperAdmin()) {
            $topSellingItems = OrderItem::query()
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->selectRaw('order_items.item_name, SUM(order_items.quantity) as total_qty')
                ->groupBy('order_items.item_name')
                ->orderByDesc('total_qty')
                ->take(6)
                ->get();
        }

        $tables = collect();
        $occupiedTableIds = [];
        if ($restaurantId) {
            $tables = RestaurantTable::where('restaurant_id', $restaurantId)->get();
            $occupiedTableIds = Order::where('restaurant_id', $restaurantId)
                ->active()
                ->whereNotNull('restaurant_table_id')
                ->pluck('restaurant_table_id')
                ->unique()
                ->values()
                ->all();
        }

        // Last 7 days revenue and order count for charts (dynamic)
        $revenueByDay = [];
        $ordersByDay = [];
        $dayNames = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayNames[] = $date->format('D');
            $dayQuery = (clone $ordersQuery)->whereDate('created_at', $date->toDateString());
            $revenueByDay[] = (float) (clone $dayQuery)->sum('total');
            $ordersByDay[] = (clone $dayQuery)->count();
        }

        // Order type counts for donut (already have $ordersByType); ensure labels/series order
        $typeOrder = ['dine_in' => 'Dine In', 'takeaway' => 'Take Away', 'delivery' => 'Delivery', 'qr_order' => 'QR Order'];
        $chartOrderTypeLabels = [];
        $chartOrderTypeSeries = [];
        foreach ($typeOrder as $key => $label) {
            if (isset($ordersByType[$key]) && $ordersByType[$key] > 0) {
                $chartOrderTypeLabels[] = $label;
                $chartOrderTypeSeries[] = (int) $ordersByType[$key];
            }
        }
        if (empty($chartOrderTypeSeries)) {
            $chartOrderTypeLabels = array_values($typeOrder);
            $chartOrderTypeSeries = [0, 0, 0, 0];
        }

        // Sales radial: percentage of this month vs last month (or simple growth placeholder)
        $thisMonth = (clone $ordersQuery)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->sum('total');
        $lastMonth = (clone $ordersQuery)->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year)->sum('total');
        $salesPercent = $lastMonth > 0 ? min(100, round(($thisMonth / $lastMonth) * 100, 0)) : ($thisMonth > 0 ? 100 : 0);

        return view('index', [
            'total_orders' => $totalOrders,
            'total_sales' => $totalSales,
            'average_order_value' => round($averageOrderValue, 2),
            'reservations_count' => $reservationsCount,
            'reservations' => $reservations,
            'orders_by_type' => $ordersByType,
            'recent_orders' => $recentOrders,
            'top_selling_items' => $topSellingItems,
            'tables' => $tables,
            'occupied_table_ids' => $occupiedTableIds,
            'chart_revenue_categories' => $dayNames,
            'chart_revenue_data' => $revenueByDay,
            'chart_order_type_labels' => $chartOrderTypeLabels,
            'chart_order_type_series' => $chartOrderTypeSeries,
            'chart_sales_percent' => (int) $salesPercent,
            'dashboard_chart_data' => [
                'revenue' => ['categories' => $dayNames, 'data' => $revenueByDay],
                'orderType' => ['labels' => $chartOrderTypeLabels, 'series' => $chartOrderTypeSeries],
                'salesPercent' => (int) $salesPercent,
                'statistic' => ['categories' => $dayNames, 'data' => $ordersByDay],
            ],
        ]);
    }
}
