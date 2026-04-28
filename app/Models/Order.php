<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'restaurant_table_id',
        'customer_id',
        'coupon_id',
        'order_number',
        'order_type',
        'status',
        'payment_status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'received_amount',
        'customer_name',
        'customer_phone',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'received_amount' => 'decimal:2',
        ];
    }

    public const TYPE_DINE_IN = 'dine_in';
    public const TYPE_TAKEAWAY = 'takeaway';
    public const TYPE_DELIVERY = 'delivery';
    public const TYPE_QR_ORDER = 'qr_order';

    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_SERVED = 'served';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_STATUS_UNPAID = 'unpaid';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_PENDING = 'pending';

    /**
     * Invoice-friendly payment status.
     *
     * - paid: payment_status is paid
     * - pending: not paid yet, and the order is still in progress
     * - unpaid: not paid, and the order is completed/cancelled
     */
    public function getInvoicePaymentStatusAttribute(): string
    {
        if (($this->payment_status ?? null) === self::PAYMENT_STATUS_PAID) {
            return self::PAYMENT_STATUS_PAID;
        }

        $orderStatus = $this->status ?? self::STATUS_PENDING;
        if (! in_array($orderStatus, [self::STATUS_COMPLETED, self::STATUS_CANCELLED], true)) {
            return self::PAYMENT_STATUS_PENDING;
        }

        return self::PAYMENT_STATUS_UNPAID;
    }

    public function getInvoicePaymentStatusLabelAttribute(): string
    {
        return ucfirst($this->invoice_payment_status);
    }

    public function getInvoicePaymentStatusBadgeAttribute(): string
    {
        return match ($this->invoice_payment_status) {
            self::PAYMENT_STATUS_PAID => 'success',
            self::PAYMENT_STATUS_PENDING => 'warning',
            default => 'danger',
        };
    }

    /** Orders that are not yet completed or cancelled (table is still "in use"). */
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'restaurant_table_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD' . strtoupper(\Illuminate\Support\Str::random(8));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }
}
