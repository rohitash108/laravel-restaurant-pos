<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\PrintJob;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\Tax;
use App\Models\User;
use App\Support\ReceiptPayload;
use Illuminate\Http\Request;

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

        $coupons = collect();
        if ($restaurantId) {
            $today = now()->toDateString();
            $coupons = Coupon::where('restaurant_id', $restaurantId)
                ->where('is_active', true)
                ->where(function ($q) use ($today) {
                    $q->whereNull('valid_from')->orWhere('valid_from', '<=', $today);
                })
                ->where(function ($q) use ($today) {
                    $q->whereNull('valid_to')->orWhere('valid_to', '>=', $today);
                })
                ->get(['id', 'code', 'discount_type', 'discount_amount', 'category_id']);
        }

        return view('pos', compact('categories', 'tables', 'recentOrders', 'draftOrders', 'transactionOrders', 'customers', 'waiters', 'tax_rate', 'tax_name', 'coupons'));
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

        $today = now()->toDateString();
        $coupons = Coupon::where('restaurant_id', $restaurantId)
            ->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('valid_to')->orWhere('valid_to', '>=', $today);
            })
            ->get(['id', 'code', 'discount_type', 'discount_amount', 'category_id']);

        $editOrder = $order;
        return view('pos', compact('categories', 'tables', 'recentOrders', 'draftOrders', 'transactionOrders', 'customers', 'waiters', 'editOrder', 'tax_rate', 'tax_name', 'coupons'));
    }

    /**
     * Server-rendered cart receipt (same layout as receipt-print) for POS Print button — works on Android Chrome.
     */
    public function cartReceiptPrint(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            abort(403);
        }

        $validated = $request->validate([
            'order_type' => ['required', 'string', 'in:dine_in,takeaway,delivery'],
            'restaurant_table_id' => ['nullable', 'integer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $restaurant = Restaurant::where('id', $restaurantId)->firstOrFail();

        $tableLabel = null;
        if (! empty($validated['restaurant_table_id'])) {
            $table = RestaurantTable::where('restaurant_id', $restaurantId)
                ->where('id', (int) $validated['restaurant_table_id'])
                ->first();
            if ($table) {
                $tableLabel = $table->table_number ?? $table->name;
            }
        }

        $lines = [];
        $subtotal = 0.0;

        foreach ($validated['items'] as $row) {
            $item = Item::where('restaurant_id', $restaurantId)
                ->where('id', (int) $row['item_id'])
                ->where('is_available', true)
                ->first();
            if (! $item) {
                continue;
            }
            $qty = (int) $row['quantity'];
            $unit = isset($row['unit_price']) && is_numeric($row['unit_price'])
                ? (float) $row['unit_price']
                : (float) $item->price;
            $lineTotal = round($unit * $qty, 2);
            $subtotal += $lineTotal;
            $lines[] = [
                'name' => $item->name,
                'qty' => $qty,
                'line_total' => $lineTotal,
            ];
        }

        if ($lines === []) {
            abort(422, 'No valid items in cart.');
        }

        $tax = Tax::where('restaurant_id', $restaurantId)->first();
        $taxRateFraction = $tax ? ((float) $tax->rate / 100) : 0.18;
        $taxAmount = round($subtotal * $taxRateFraction, 2);
        $total = round($subtotal + $taxAmount, 2);
        $taxName = $tax ? $tax->name : 'Tax';

        $payload = ReceiptPayload::fromCartPreview(
            $restaurant,
            $lines,
            (float) $subtotal,
            (float) $taxAmount,
            (float) $total,
            $validated['order_type'],
            $tableLabel
        );

        PrintJob::create([
            'restaurant_id' => $restaurantId,
            'order_id' => null,
            'type' => 'receipt',
            'status' => 'pending',
            'payload' => $payload,
        ]);

        $currency_symbol = config('app.currency_symbol', '₹');

        return response()
            ->view('pos-cart-receipt', [
                'restaurant' => $restaurant,
                'lines' => $lines,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'tax_name' => $taxName,
                'total' => $total,
                'order_type' => $validated['order_type'],
                'table_label' => $tableLabel,
                'currency_symbol' => $currency_symbol,
            ])
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }
}
