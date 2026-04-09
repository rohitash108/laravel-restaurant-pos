<?php

namespace App\Support;

use App\Models\Order;

class ReceiptPayload
{
    /**
     * Create a stable JSON payload for printing (Android bridge will format to ESC/POS).
     */
    public static function fromOrder(Order $order): array
    {
        $order->loadMissing(['restaurant', 'table', 'items']);

        $restaurant = $order->restaurant;
        $table = $order->table;

        return [
            'version' => 1,
            'type' => 'receipt',
            'generated_at' => now()->toIso8601String(),
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'created_at' => optional($order->created_at)->toIso8601String(),
                'order_type' => $order->order_type,
                'status' => $order->status,
                'payment_status' => $order->payment_status ?? 'unpaid',
                'customer_name' => $order->customer_name,
                'notes' => $order->notes,
                'table' => $table ? [
                    'id' => $table->id,
                    'name' => $table->name,
                    'table_number' => $table->table_number,
                    'floor' => $table->floor,
                ] : null,
            ],
            'restaurant' => $restaurant ? [
                'id' => $restaurant->id,
                'name' => $restaurant->name,
                'address' => $restaurant->address,
                'phone' => $restaurant->phone,
                'email' => $restaurant->email,
                'currency' => $restaurant->currency,
            ] : null,
            'items' => $order->items->map(function ($it) {
                return [
                    'name' => $it->item_name,
                    'qty' => (int) $it->quantity,
                    'unit_price' => (float) $it->unit_price,
                    'total' => (float) $it->total_price,
                    'notes' => $it->notes,
                ];
            })->values()->all(),
            'totals' => [
                'subtotal' => (float) $order->subtotal,
                'tax_amount' => (float) $order->tax_amount,
                'discount_amount' => (float) $order->discount_amount,
                'total' => (float) $order->total,
                'received_amount' => $order->received_amount !== null ? (float) $order->received_amount : null,
            ],
        ];
    }
}

