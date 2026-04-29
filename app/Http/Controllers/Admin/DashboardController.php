<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Subscription;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Users with restaurants access see the full dashboard; catalog-only managers see limited stats
        if (! auth()->user()->canAccessAdminModule('restaurants')) {
            return view('admin.dashboard', [
                'is_limited_admin'   => true,
                'total_categories'   => Category::whereNull('restaurant_id')->count(),
                'total_products'     => Item::master()->count(),
                'total_addons'       => \App\Models\Addon::whereNull('restaurant_id')->count(),
                'total_my_users'     => User::where('created_by_super_admin_id', auth()->id())->count(),
                // Required by the chart script block in the view
                'admin_chart_data'   => ['categories' => [], 'data' => []],
                'recent_restaurants' => collect(),
            ]);
        }

        $totalRestaurants = Restaurant::count();
        $activeRestaurants = Restaurant::where('is_active', true)->count();
        $totalOrders = Order::count();
        $totalSales = (float) Order::sum('total');
        $totalStaff = User::whereNotNull('restaurant_id')->where('role', '!=', 'super_admin')->count();

        // Last 7 days: orders and revenue per day
        $revenueByDay = [];
        $ordersByDay = [];
        $dayNames = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayNames[] = $date->format('D');
            $revenueByDay[] = (float) Order::whereDate('created_at', $date->toDateString())->sum('total');
            $ordersByDay[] = Order::whereDate('created_at', $date->toDateString())->count();
        }

        // Recent restaurants
        $recentRestaurants = Restaurant::withCount(['tables', 'orders'])
            ->latest()
            ->take(6)
            ->get();

        // This month vs last month
        $thisMonthSales = (float) Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');
        $lastMonthSales = (float) Order::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total');
        $salesGrowthPercent = $lastMonthSales > 0
            ? round((($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 1)
            : ($thisMonthSales > 0 ? 100 : 0);

        $currencySymbol = '₹'; // Platform billing currency

        // Subscription stats
        $activeSubscriptions = Subscription::active()->count();
        $expiringSoon = Subscription::expiringSoon(7)->count();

        $adminChartData = [
            'categories' => $dayNames,
            'data' => $ordersByDay,
        ];

        return view('admin.dashboard', [
            'is_limited_admin' => false,
            'total_restaurants' => $totalRestaurants,
            'active_restaurants' => $activeRestaurants,
            'total_orders' => $totalOrders,
            'total_sales' => round($totalSales, 2),
            'total_staff' => $totalStaff,
            'recent_restaurants' => $recentRestaurants,
            'day_names' => $dayNames,
            'revenue_by_day' => $revenueByDay,
            'orders_by_day' => $ordersByDay,
            'admin_chart_data' => $adminChartData,
            'sales_growth_percent' => $salesGrowthPercent,
            'this_month_sales' => $thisMonthSales,
            'currency_symbol' => $currencySymbol,
            'active_subscriptions' => $activeSubscriptions,
            'expiring_soon' => $expiringSoon,
        ]);
    }
}
