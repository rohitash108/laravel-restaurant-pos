<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvesRestaurant;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\RestaurantTable;
use App\Models\PrintJob;
use App\Support\ReceiptPayload;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ResolvesRestaurant;

    public function index()
    {
        $restaurantId = $this->currentRestaurantId();
        $from = request()->input('from');
        $to = request()->input('to');

        if (! $restaurantId) {
            $orders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
            $counts = ['pending' => 0, 'confirmed' => 0, 'preparing' => 0, 'ready' => 0, 'completed' => 0, 'cancelled' => 0];
            $pendingOrders = collect();
            $inProgressOrders = collect();
            $completedOrders = collect();
            $cancelledOrders = collect();
        } else {
            $query = Order::where('restaurant_id', $restaurantId);

            if ($from) {
                $query->whereDate('created_at', '>=', $from);
            }
            if ($to) {
                $query->whereDate('created_at', '<=', $to);
            }

            $orders = (clone $query)->with(['table', 'items'])->latest()->paginate(20)->withQueryString();
            $counts = [
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'confirmed' => (clone $query)->where('status', 'confirmed')->count(),
                'preparing' => (clone $query)->where('status', 'preparing')->count(),
                'ready' => (clone $query)->where('status', 'ready')->count(),
                'completed' => (clone $query)->where('status', 'completed')->count(),
                'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            ];
            $pendingOrders = (clone $query)->where('status', 'pending')->with(['table', 'items'])->latest()->get();
            $inProgressOrders = (clone $query)->whereIn('status', ['confirmed', 'preparing', 'ready'])->with(['table', 'items'])->latest()->get();
            $completedOrders = (clone $query)->where('status', 'completed')->with(['table', 'items'])->latest()->get();
            $cancelledOrders = (clone $query)->where('status', 'cancelled')->with(['table', 'items'])->latest()->get();
        }

        return view('orders', compact('orders', 'counts', 'pendingOrders', 'inProgressOrders', 'completedOrders', 'cancelledOrders', 'from', 'to'));
    }

    /**
     * Create order from POS (dine-in, takeaway, delivery).
     */
    public function store(Request $request)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId) {
            return redirect()->route('dashboard')->with('error', 'No restaurant selected.');
        }

        $request->validate([
            'restaurant_table_id' => ['nullable', 'exists:restaurant_tables,id', function ($attr, $value, $fail) use ($restaurantId) {
                if ($value && RestaurantTable::where('id', $value)->where('restaurant_id', $restaurantId)->doesntExist()) {
                    $fail('The selected table does not belong to your restaurant.');
                }
            }],
            'order_type' => ['required', 'string', 'in:dine_in,takeaway,delivery'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'items.required' => 'Add at least one item to the order.',
        ]);

        $tableId = $request->restaurant_table_id;
        if ($request->order_type === 'dine_in' && ! $tableId) {
            return back()->with('error', 'Table is required for dine-in orders.')->withInput();
        }

        $subtotal = 0;
        $orderItemsData = [];

        foreach ($request->items as $row) {
            $item = Item::where('restaurant_id', $restaurantId)->where('id', $row['item_id'])->first();
            if (! $item || ! $item->is_available) {
                continue;
            }
            $qty = (int) $row['quantity'];
            $unitPrice = isset($row['unit_price']) && is_numeric($row['unit_price']) ? (float) $row['unit_price'] : (float) $item->price;
            $total = $unitPrice * $qty;
            $subtotal += $total;
            $orderItemsData[] = [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'unit_price' => $unitPrice,
                'quantity' => $qty,
                'total_price' => $total,
                'notes' => $row['notes'] ?? null,
            ];
        }

        if (empty($orderItemsData)) {
            return back()->with('error', 'Please add at least one valid item.')->withInput();
        }

        $taxRate = 0.18;
        $taxAmount = round($subtotal * $taxRate, 2);
        $couponId = $request->filled('coupon_id') ? (int) $request->coupon_id : null;
        $coupon = null;
        if ($couponId) {
            $coupon = Coupon::where('restaurant_id', $restaurantId)->where('id', $couponId)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()->toDateString());
                })
                ->where(function ($q) {
                    $q->whereNull('valid_to')->orWhere('valid_to', '>=', now()->toDateString());
                })
                ->first();
            if (! $coupon) {
                $couponId = null;
            }
        }
        if ($coupon) {
            $discountAmount = $coupon->discount_type === 'percentage'
                ? round($subtotal * (float) $coupon->discount_amount / 100, 2)
                : round(min((float) $coupon->discount_amount, $subtotal), 2);
        } else {
            $discountAmount = $request->filled('discount_amount') ? round((float) $request->discount_amount, 2) : 0;
        }
        $total = round(max(0, $subtotal + $taxAmount - $discountAmount), 2);
        $receivedAmount = $request->filled('received_amount') ? round((float) $request->received_amount, 2) : null;
        $customerId = $request->filled('customer_id') ? (int) $request->customer_id : null;
        if ($customerId && Customer::where('id', $customerId)->where('restaurant_id', $restaurantId)->doesntExist()) {
            $customerId = null;
        }

        $order = Order::create([
            'restaurant_id' => $restaurantId,
            'restaurant_table_id' => $tableId,
            'customer_id' => $customerId,
            'coupon_id' => $couponId,
            'order_number' => Order::generateOrderNumber(),
            'order_type' => $request->order_type,
            'status' => Order::STATUS_PENDING,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'received_amount' => $receivedAmount,
            'customer_name' => $request->customer_name ?: 'Walk-in',
            'notes' => $request->notes,
        ]);

        foreach ($orderItemsData as $data) {
            $order->items()->create($data);
        }

        // Create a print job immediately (tablet can fetch and print via Android bridge)
        PrintJob::create([
            'restaurant_id' => $restaurantId,
            'order_id' => $order->id,
            'type' => 'receipt',
            'status' => 'pending',
            'payload' => ReceiptPayload::fromOrder($order),
        ]);

        if ($customerId) {
            $customer = Customer::where('restaurant_id', $restaurantId)->find($customerId);
            if ($customer) {
                $unpaid = $total - ($receivedAmount ?? 0);
                $customer->balance = (float) $customer->balance - $unpaid;
                $customer->save();
            }
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'order_number' => $order->order_number, 'order' => $order->load('table', 'items')]);
        }

        return redirect()->route('pos')->with('success', 'Order #' . $order->order_number . ' created.')->with('order_number', $order->order_number);
    }

    /**
     * Update an existing order (items, totals, table, customer). Only for non-completed/cancelled.
     */
    public function update(Request $request, Order $order)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId || (int) $order->restaurant_id !== (int) $restaurantId) {
            abort(403);
        }
        if (in_array($order->status, [Order::STATUS_COMPLETED, Order::STATUS_CANCELLED], true)) {
            return redirect()->route('orders')->with('error', 'Cannot update a completed or cancelled order.');
        }

        $request->validate([
            'restaurant_table_id' => ['nullable', 'exists:restaurant_tables,id', function ($attr, $value, $fail) use ($restaurantId) {
                if ($value && RestaurantTable::where('id', $value)->where('restaurant_id', $restaurantId)->doesntExist()) {
                    $fail('The selected table does not belong to your restaurant.');
                }
            }],
            'order_type' => ['required', 'string', 'in:dine_in,takeaway,delivery'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_id' => ['required', 'exists:items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'coupon_id' => ['nullable', 'integer', 'exists:coupons,id'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'received_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'items.required' => 'Add at least one item to the order.',
        ]);

        $tableId = $request->restaurant_table_id;
        if ($request->order_type === 'dine_in' && ! $tableId) {
            return back()->with('error', 'Table is required for dine-in orders.')->withInput();
        }

        $subtotal = 0;
        $orderItemsData = [];

        foreach ($request->items as $row) {
            $item = Item::where('restaurant_id', $restaurantId)->where('id', $row['item_id'])->first();
            if (! $item || ! $item->is_available) {
                continue;
            }
            $qty = (int) $row['quantity'];
            $unitPrice = isset($row['unit_price']) && is_numeric($row['unit_price']) ? (float) $row['unit_price'] : (float) $item->price;
            $total = $unitPrice * $qty;
            $subtotal += $total;
            $orderItemsData[] = [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'unit_price' => $unitPrice,
                'quantity' => $qty,
                'total_price' => $total,
                'notes' => $row['notes'] ?? null,
            ];
        }

        if (empty($orderItemsData)) {
            return back()->with('error', 'Please add at least one valid item.')->withInput();
        }

        $taxRate = 0.18;
        $taxAmount = round($subtotal * $taxRate, 2);
        $couponId = $request->filled('coupon_id') ? (int) $request->coupon_id : null;
        $coupon = null;
        if ($couponId) {
            $coupon = Coupon::where('restaurant_id', $restaurantId)->where('id', $couponId)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()->toDateString());
                })
                ->where(function ($q) {
                    $q->whereNull('valid_to')->orWhere('valid_to', '>=', now()->toDateString());
                })
                ->first();
            if (! $coupon) {
                $couponId = null;
            }
        }
        if ($coupon) {
            $discountAmount = $coupon->discount_type === 'percentage'
                ? round($subtotal * (float) $coupon->discount_amount / 100, 2)
                : round(min((float) $coupon->discount_amount, $subtotal), 2);
        } else {
            $discountAmount = $request->filled('discount_amount') ? round((float) $request->discount_amount, 2) : 0;
        }
        $total = round(max(0, $subtotal + $taxAmount - $discountAmount), 2);
        $receivedAmount = $request->filled('received_amount') ? round((float) $request->received_amount, 2) : null;
        $customerId = $request->filled('customer_id') ? (int) $request->customer_id : null;
        if ($customerId && Customer::where('id', $customerId)->where('restaurant_id', $restaurantId)->doesntExist()) {
            $customerId = null;
        }

        $order->update([
            'restaurant_table_id' => $tableId,
            'customer_id' => $customerId,
            'coupon_id' => $couponId,
            'order_type' => $request->order_type,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'received_amount' => $receivedAmount,
            'customer_name' => $request->customer_name ?: 'Walk-in',
            'notes' => $request->notes,
        ]);

        $order->items()->delete();
        foreach ($orderItemsData as $data) {
            $order->items()->create($data);
        }

        return redirect()->route('orders')->with('success', 'Order #' . $order->order_number . ' updated.');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId || (int) $order->restaurant_id !== (int) $restaurantId) {
            abort(403);
        }
        $request->validate(['status' => 'required|in:pending,confirmed,preparing,ready,completed,cancelled']);
        $order->update(['status' => $request->status]);

        return redirect()->route('orders')->with('success', 'Order status updated.');
    }

    /**
     * Update order payment status (paid/unpaid). Used when customer pays via QR or other method.
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $restaurantId = $this->currentRestaurantId();
        if (! $restaurantId || (int) $order->restaurant_id !== (int) $restaurantId) {
            abort(403);
        }
        $request->validate(['payment_status' => 'required|in:paid,unpaid']);
        $order->update(['payment_status' => $request->payment_status]);

        return redirect()->route('orders')->with('success', 'Payment status updated to ' . $request->payment_status . '.');
    }
}
