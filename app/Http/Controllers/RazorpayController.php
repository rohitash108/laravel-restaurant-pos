<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Item;
use App\Models\Order;
use App\Models\PrintJob;
use App\Models\Restaurant;
use App\Models\RestaurantTable;
use App\Models\Tax;
use App\Services\Inventory\InventoryStockService;
use App\Services\RazorpayService;
use App\Support\ReceiptPayload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RazorpayController extends Controller
{
    /**
     * Step 1: Build cart totals and create a Razorpay order.
     * POST /payment/razorpay/initiate
     * Returns: key_id, razorpay_order_id, amount (paise), currency, restaurant name, description
     */
    public function initiate(Request $request): JsonResponse
    {
        $request->validate([
            'restaurant_id'       => 'required|exists:restaurants,id',
            'restaurant_table_id' => 'required|exists:restaurant_tables,id',
            'items'               => 'required|array|min:1',
            'items.*.item_id'     => 'required|exists:items,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_price'  => 'nullable|numeric|min:0',
            'coupon_id'           => 'nullable|integer|exists:coupons,id',
            'customer_name'       => 'nullable|string|max:255',
            'notes'               => 'nullable|string|max:500',
        ]);

        $restaurant = Restaurant::findOrFail($request->restaurant_id);
        $table      = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->findOrFail($request->restaurant_table_id);

        $razorpay = RazorpayService::forRestaurant($restaurant->id);
        if (! $razorpay) {
            return response()->json([
                'error' => 'Online payment is not configured for this restaurant.',
            ], 422);
        }

        // Calculate totals (same logic as placeOrder)
        [$subtotal, $orderItemsData] = $this->buildItemsAndSubtotal($request, $restaurant);

        if (empty($orderItemsData)) {
            return response()->json(['error' => 'No valid items in cart.'], 422);
        }

        [$discountAmount, $couponId] = $this->applyConpon($request, $restaurant, $subtotal);

        [$taxAmount, $total] = $this->calcTax($restaurant, $subtotal, $discountAmount);

        $amountPaise = (int) round($total * 100);

        if ($amountPaise < 100) {
            return response()->json(['error' => 'Minimum order amount is ₹1.'], 422);
        }

        $receipt = 'rcpt_' . $restaurant->id . '_' . $table->id . '_' . time();

        try {
            $rzpOrder = $razorpay->createOrder($amountPaise, $receipt);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment gateway error: ' . $e->getMessage()], 502);
        }

        // Stash the metadata we'll need at verify time — keyed by Razorpay order id.
        // (Sessions on the QR menu are not authenticated, but Laravel still gives us
        // a session via cookie which is plenty for this short hop.)
        session([
            'rzp_order_meta:' . $rzpOrder['id'] => [
                'transfer_id'        => $rzpOrder['transfer_id']        ?? null,
                'platform_fee_paise' => $rzpOrder['platform_fee_paise'] ?? 0,
                'mode'               => $razorpay->mode,
                'linked_account_id'  => $razorpay->linkedAccountId,
            ],
        ]);

        return response()->json([
            'key_id'            => $razorpay->getKeyId(),
            'razorpay_order_id' => $rzpOrder['id'],
            'amount'            => $rzpOrder['amount'],
            'currency'          => $rzpOrder['currency'],
            'restaurant_name'   => $restaurant->name,
            'description'       => 'Order at Table ' . $table->name,
        ]);
    }

    /**
     * Step 2: Verify payment signature and place the Laravel order.
     * POST /payment/razorpay/verify
     */
    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'razorpay_order_id'   => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature'  => 'required|string',
            'restaurant_id'       => 'required|exists:restaurants,id',
            'restaurant_table_id' => 'required|exists:restaurant_tables,id',
            'items'               => 'required|array|min:1',
            'items.*.item_id'     => 'required|exists:items,id',
            'items.*.quantity'    => 'required|integer|min:1',
            'items.*.unit_price'  => 'nullable|numeric|min:0',
            'coupon_id'           => 'nullable|integer|exists:coupons,id',
            'customer_name'       => 'nullable|string|max:255',
            'notes'               => 'nullable|string|max:500',
        ]);

        $restaurant = Restaurant::findOrFail($request->restaurant_id);
        $table      = RestaurantTable::where('restaurant_id', $restaurant->id)
            ->findOrFail($request->restaurant_table_id);

        $razorpay = RazorpayService::forRestaurant($restaurant->id);
        if (! $razorpay) {
            return response()->json(['error' => 'Payment not configured.'], 422);
        }

        $valid = $razorpay->verifyPayment(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        );

        if (! $valid) {
            return response()->json(['error' => 'Payment verification failed. Please contact support.'], 422);
        }

        // Build order now that payment is verified
        [$subtotal, $orderItemsData] = $this->buildItemsAndSubtotal($request, $restaurant);

        if (empty($orderItemsData)) {
            return response()->json(['error' => 'No valid items.'], 422);
        }

        [$discountAmount, $couponId] = $this->applyConpon($request, $restaurant, $subtotal);
        [$taxAmount, $total]         = $this->calcTax($restaurant, $subtotal, $discountAmount);

        // Pull the metadata we stashed during initiate (transfer id, platform fee, mode)
        $meta = session('rzp_order_meta:' . $request->razorpay_order_id, []);

        $order = Order::create([
            'restaurant_id'              => $restaurant->id,
            'restaurant_table_id'        => $table->id,
            'order_number'               => Order::generateOrderNumber(),
            'order_type'                 => Order::TYPE_QR_ORDER,
            'status'                     => Order::STATUS_PENDING,
            'payment_status'             => 'paid',
            'subtotal'                   => $subtotal,
            'tax_amount'                 => $taxAmount,
            'discount_amount'            => $discountAmount,
            'total'                      => $total,
            'coupon_id'                  => $couponId,
            'customer_name'              => $request->customer_name,
            'notes'                      => $request->notes,
            'razorpay_order_id'          => $request->razorpay_order_id,
            'razorpay_payment_id'        => $request->razorpay_payment_id,
            'razorpay_signature'         => $request->razorpay_signature,
            'razorpay_transfer_id'       => $meta['transfer_id']        ?? null,
            'razorpay_linked_account_id' => $meta['linked_account_id']  ?? null,
            'platform_fee_amount'        => isset($meta['platform_fee_paise'])
                ? round(((int) $meta['platform_fee_paise']) / 100, 2)
                : null,
            'payment_method'             => 'razorpay',
        ]);

        // Cleanup short-lived stash
        session()->forget('rzp_order_meta:' . $request->razorpay_order_id);

        foreach ($orderItemsData as $data) {
            $order->items()->create($data);
        }

        PrintJob::create([
            'restaurant_id' => $restaurant->id,
            'order_id'      => $order->id,
            'type'          => 'receipt',
            'status'        => 'pending',
            'payload'       => ReceiptPayload::fromOrder($order),
        ]);

        try {
            $order->load('items');
            app(InventoryStockService::class)->deductForOrder($order);
        } catch (\Throwable $e) {
            report($e);
        }

        $successUrl = route('order.by-qr.success', [
            'restaurant' => $restaurant->slug,
            'table'      => $table->slug ?? $table->id,
            'order'      => $order->order_number,
        ]);

        // Store order_number in session so success page can retrieve it
        session(['order_number' => $order->order_number]);

        return response()->json([
            'success'      => true,
            'order_number' => $order->order_number,
            'redirect_url' => $successUrl,
        ]);
    }

    // ─── Helpers ────────────────────────────────────────────────────────────────

    private function buildItemsAndSubtotal(Request $request, Restaurant $restaurant): array
    {
        $subtotal = 0;
        $orderItemsData = [];

        foreach ($request->items as $row) {
            $item = Item::forRestaurant($restaurant->id)->where('id', $row['item_id'])->first();
            if (! $item || ! $item->is_available) {
                continue;
            }
            $qty       = (int) $row['quantity'];
            $unitPrice = isset($row['unit_price']) && is_numeric($row['unit_price'])
                ? (float) $row['unit_price']
                : (float) $item->price;
            $rowTotal  = round($unitPrice * $qty, 2);
            $subtotal += $rowTotal;

            $orderItemsData[] = [
                'item_id'    => $item->id,
                'item_name'  => $item->name,
                'unit_price' => $unitPrice,
                'quantity'   => $qty,
                'total_price'=> $rowTotal,
                'notes'      => $row['notes'] ?? null,
            ];
        }

        return [$subtotal, $orderItemsData];
    }

    private function applyConpon(Request $request, Restaurant $restaurant, float $subtotal): array
    {
        $couponId = $request->filled('coupon_id') ? (int) $request->coupon_id : null;
        $discountAmount = 0;

        if ($couponId) {
            $coupon = Coupon::where('restaurant_id', $restaurant->id)
                ->where('id', $couponId)
                ->where('is_active', true)
                ->where(fn ($q) => $q->whereNull('valid_from')->orWhere('valid_from', '<=', now()->toDateString()))
                ->where(fn ($q) => $q->whereNull('valid_to')->orWhere('valid_to', '>=', now()->toDateString()))
                ->first();

            if ($coupon) {
                $discountAmount = $coupon->discount_type === 'percentage'
                    ? round($subtotal * (float) $coupon->discount_amount / 100, 2)
                    : round(min((float) $coupon->discount_amount, $subtotal), 2);
            } else {
                $couponId = null;
            }
        }

        return [$discountAmount, $couponId];
    }

    private function calcTax(Restaurant $restaurant, float $subtotal, float $discountAmount): array
    {
        $tax     = Tax::where('restaurant_id', $restaurant->id)->where('is_active', true)->first();
        $taxRate = $tax ? ((float) $tax->rate / 100) : 0;
        $taxType = $tax ? $tax->type : 'exclusive';

        if ($taxType === 'inclusive') {
            $taxAmount = $taxRate > 0 ? round($subtotal * $taxRate / (1 + $taxRate), 2) : 0;
            $total     = round(max(0, $subtotal - $discountAmount), 2);
        } else {
            $taxAmount = round($subtotal * $taxRate, 2);
            $total     = round(max(0, $subtotal + $taxAmount - $discountAmount), 2);
        }

        return [$taxAmount, $total];
    }
}
