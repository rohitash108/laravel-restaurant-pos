<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        $categories = $restaurant->categories()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with(['items' => fn ($q) => $q->where('is_available', true)->orderBy('sort_order')->orderBy('name')])
            ->get();

        // QR order page always uses INR (₹) for India; USD is treated as INR
        $currencySymbol = match ($restaurant->currency ?? 'INR') {
            'INR' => '₹',
            'USD' => '₹',   // show INR on QR menu (India use)
            'EUR' => '€',
            'GBP' => '£',
            'AED' => 'AED ',
            default => '₹',
        };

        return view('order-by-qr.menu', [
            'restaurant' => $restaurant,
            'table' => $table,
            'categories' => $categories,
            'currency_symbol' => $currencySymbol,
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
            'customer_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $restaurant = Restaurant::findOrFail($request->restaurant_id);
        $table = RestaurantTable::where('restaurant_id', $restaurant->id)->findOrFail($request->restaurant_table_id);

        $subtotal = 0;
        $orderItemsData = [];

        foreach ($request->items as $row) {
            $item = Item::where('restaurant_id', $restaurant->id)->where('id', $row['item_id'])->first();
            if (! $item || ! $item->is_available) {
                continue;
            }
            $qty = (int) $row['quantity'];
            $total = $item->price * $qty;
            $subtotal += $total;
            $orderItemsData[] = [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'unit_price' => $item->price,
                'quantity' => $qty,
                'total_price' => $total,
                'notes' => $row['notes'] ?? null,
            ];
        }

        if (empty($orderItemsData)) {
            return back()->with('error', 'Please add at least one item.')->withInput();
        }

        $order = Order::create([
            'restaurant_id' => $restaurant->id,
            'restaurant_table_id' => $table->id,
            'order_number' => Order::generateOrderNumber(),
            'order_type' => Order::TYPE_QR_ORDER,
            'status' => Order::STATUS_PENDING,
            'subtotal' => $subtotal,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total' => $subtotal,
            'customer_name' => $request->customer_name,
            'notes' => $request->notes,
        ]);

        foreach ($orderItemsData as $data) {
            $order->items()->create($data);
        }

        return redirect()->route('order.by-qr.success', [
            'restaurant' => $restaurant->slug,
            'table' => $table->slug ?? $table->id,
            'order' => $order->order_number,
        ])->with('order_number', $order->order_number);
    }

    /**
     * Order placed success page.
     */
    public function success(Restaurant $restaurant, string $table, string $order)
    {
        $table = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->where(fn ($q) => $q->where('slug', $table)->orWhere('id', (int) $table))
            ->firstOrFail();

        $orderNumber = session('order_number', $order);

        return view('order-by-qr.success', [
            'restaurant' => $restaurant,
            'table' => $table,
            'order_number' => $orderNumber,
        ]);
    }
}
