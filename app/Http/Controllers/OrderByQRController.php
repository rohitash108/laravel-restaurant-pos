<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\PrintJob;
use App\Models\Tax;
use App\Services\Inventory\InventoryStockService;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Support\ReceiptPayload;

class OrderByQRController extends Controller
{
    /**
     * Return QR code image for table ordering (public). URL: /order/{restaurant}/{table}/qr
     */
    public function qrImage(Restaurant $restaurant, string $table)
    {
        if (! $restaurant->is_active) {
            abort(404);
        }
        $tableModel = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where(fn ($q) => $q->where('slug', $table)->orWhere('id', (int) $table))
            ->firstOrFail();

        $url = route('order.by-qr', [
            'restaurant' => $restaurant->slug,
            'table' => $tableModel->slug ?? (string) $tableModel->id,
        ]);

        try {
            $png = QrCode::format('png')->size(280)->margin(2)->generate($url);
            return response($png)->header('Content-Type', 'image/png');
        } catch (\Throwable $e) {
            $svg = QrCode::format('svg')->size(280)->margin(2)->generate($url);
            return response($svg)->header('Content-Type', 'image/svg+xml');
        }
    }

    /**
     * Show menu for a table (public – no auth). URL: /order/{restaurant}/{table}
     */
    public function show(Restaurant $restaurant, string $table)
    {
        if (! $restaurant->is_active) {
            abort(404);
        }
        $table = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where(fn ($q) => $q->where('slug', $table)->orWhere('id', (int) $table))
            ->firstOrFail();

        // Load categories with items; each item with variations and addons from database so customer can choose when ordering
        $categories = $restaurant->categories()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with([
                'items' => function ($q) {
                    $q->where('is_available', true)
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->with([
                            'addons' => fn ($aq) => $aq->where('status', 'active')->orderBy('id'),
                            'variations' => fn ($vq) => $vq->orderBy('sort_order')->orderBy('name'),
                        ]);
                },
            ])
            ->get();

        $currencySymbol = $restaurant->currencySymbol();

        $coupons = Coupon::where('restaurant_id', $restaurant->id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()->toDateString());
            })
            ->where(function ($q) {
                $q->whereNull('valid_to')->orWhere('valid_to', '>=', now()->toDateString());
            })
            ->get(['id', 'code', 'discount_type', 'discount_amount']);

        return view('order-by-qr.menu', [
            'restaurant' => $restaurant,
            'table' => $table,
            'categories' => $categories,
            'currency_symbol' => $currencySymbol,
            'coupons' => $coupons,
        ]);
    }

    /**
     * Place order from QR menu (table-wise).
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'restaurant_table_id' => 'required|exists:restaurant_tables,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500',
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'coupon_id' => 'nullable|integer|exists:coupons,id',
        ]);

        $restaurant = Restaurant::findOrFail($request->restaurant_id);
        $table = RestaurantTable::where('restaurant_id', $restaurant->id)->findOrFail($request->restaurant_table_id);

        $subtotal = 0;
        $orderItemsData = [];

        foreach ($request->items as $row) {
            $item = Item::forRestaurant($restaurant->id)->where('id', $row['item_id'])->first();
            if (! $item || ! $item->is_available) {
                continue;
            }
            $qty = (int) $row['quantity'];
            $unitPrice = isset($row['unit_price']) && is_numeric($row['unit_price']) ? (float) $row['unit_price'] : (float) $item->price;
            $total = round($unitPrice * $qty, 2);
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
            return back()->with('error', 'Please add at least one item.')->withInput();
        }

        $couponId = $request->filled('coupon_id') ? (int) $request->coupon_id : null;
        $discountAmount = 0;
        if ($couponId) {
            $coupon = Coupon::where('restaurant_id', $restaurant->id)->where('id', $couponId)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()->toDateString());
                })
                ->where(function ($q) {
                    $q->whereNull('valid_to')->orWhere('valid_to', '>=', now()->toDateString());
                })
                ->first();
            if ($coupon) {
                $discountAmount = $coupon->discount_type === 'percentage'
                    ? round($subtotal * (float) $coupon->discount_amount / 100, 2)
                    : round(min((float) $coupon->discount_amount, $subtotal), 2);
            } else {
                $couponId = null;
            }
        }

        // C3: fetch restaurant tax from DB (same as POS orders)
        $tax      = Tax::where('restaurant_id', $restaurant->id)->where('is_active', true)->first();
        $taxRate  = $tax ? ((float) $tax->rate / 100) : 0;
        $taxAmount = round($subtotal * $taxRate, 2);

        $total = round(max(0, $subtotal + $taxAmount - $discountAmount), 2);

        $order = Order::create([
            'restaurant_id'       => $restaurant->id,
            'restaurant_table_id' => $table->id,
            'order_number'        => Order::generateOrderNumber(),
            'order_type'          => Order::TYPE_QR_ORDER,
            'status'              => Order::STATUS_PENDING,
            'subtotal'            => $subtotal,
            'tax_amount'          => $taxAmount,
            'discount_amount'     => $discountAmount,
            'total'               => $total,
            'coupon_id'           => $couponId,
            'customer_name'       => $request->customer_name,
            'notes'               => $request->notes,
        ]);

        foreach ($orderItemsData as $data) {
            $order->items()->create($data);
        }

        // Create a print job for the restaurant (staff tablet can print when online)
        PrintJob::create([
            'restaurant_id' => $restaurant->id,
            'order_id' => $order->id,
            'type' => 'receipt',
            'status' => 'pending',
            'payload' => ReceiptPayload::fromOrder($order),
        ]);

        try {
            $order->load('items');
            app(InventoryStockService::class)->deductForOrder($order);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('order.by-qr.success', [
            'restaurant' => $restaurant->slug,
            'table' => $table->slug ?? $table->id,
            'order' => $order->order_number,
        ])->with('order_number', $order->order_number);
    }

    /**
     * Order placed success page. Loads order so we can show payment QR, total, and payment status.
     */
    public function success(Restaurant $restaurant, string $table, string $order)
    {
        $tableModel = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where(fn ($q) => $q->where('slug', $table)->orWhere('id', (int) $table))
            ->firstOrFail();

        $orderNumber = session('order_number', $order);
        $orderModel = Order::where('restaurant_id', $restaurant->id)
            ->where('restaurant_table_id', $tableModel->id)
            ->where('order_number', $orderNumber)
            ->first();

        $currencySymbol = $restaurant->currencySymbol();

        return view('order-by-qr.success', [
            'restaurant' => $restaurant,
            'table' => $tableModel,
            'order_number' => $orderNumber,
            'order' => $orderModel,
            'currency_symbol' => $currencySymbol,
        ]);
    }

    /**
     * Public API: get order payment/status for success page polling (no auth).
     * GET /order/{restaurant}/{table}/order-status?order_number=XXX
     */
    public function orderStatus(Request $request, Restaurant $restaurant, string $table)
    {
        $orderNumber = $request->query('order_number');
        if (! $orderNumber) {
            return response()->json(['error' => 'order_number required'], 400);
        }

        $tableModel = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where(fn ($q) => $q->where('slug', $table)->orWhere('id', (int) $table))
            ->first();

        if (! $tableModel) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $order = Order::where('restaurant_id', $restaurant->id)
            ->where('restaurant_table_id', $tableModel->id)
            ->where('order_number', $orderNumber)
            ->first(['id', 'order_number', 'status', 'payment_status', 'total']);

        if (! $order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        return response()->json([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status ?? 'unpaid',
            'total' => (float) $order->total,
        ]);
    }
}
