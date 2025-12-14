<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    // Order statuses
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PAID = 'paid';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';

    // Payment statuses
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_PAID = 'paid';
    const PAYMENT_FAILED = 'failed';
    const PAYMENT_REFUNDED = 'refunded';

    // Payment methods
    const PAYMENT_CASH = 'cash';
    const PAYMENT_YAPE = 'yape';

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'tax',
        'tax_rate',
        'delivery_fee',
        'discount',
        'total',
        'payment_method',
        'payment_status',
        'shipping_address',
        'billing_address',
        'notes',
        'coupon_code',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'created_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'SNEAK-';
        $number = static::withTrashed()->max('id') ?? 0;
        $number++;
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get all status options
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_CONFIRMED => 'Confirmado',
            self::STATUS_PAID => 'Pagado',
            self::STATUS_PROCESSING => 'En proceso',
            self::STATUS_SHIPPED => 'Enviado',
            self::STATUS_DELIVERED => 'Entregado',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_CANCELLED => 'Cancelado',
            self::STATUS_REFUNDED => 'Reembolsado',
        ];
    }

    /**
     * Get payment status options
     */
    public static function getPaymentStatusOptions(): array
    {
        return [
            self::PAYMENT_PENDING => 'Pendiente',
            self::PAYMENT_PAID => 'Pagado',
            self::PAYMENT_FAILED => 'Fallido',
            self::PAYMENT_REFUNDED => 'Reembolsado',
        ];
    }

    /**
     * Get payment method options
     */
    public static function getPaymentMethodOptions(): array
    {
        return [
            self::PAYMENT_CASH => 'Efectivo',
            self::PAYMENT_YAPE => 'Yape',
        ];
    }

    // ============= RELATIONSHIPS =============

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get order items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get payment
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Get invoice
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get creator
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ============= SCOPES =============

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for paid orders
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    /**
     * Scope for orders by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for orders by payment method
     */
    public function scopeByPaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope for orders by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for today's orders
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for this month's orders
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    // ============= ACCESSORS =============

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_PAID => 'green',
            self::STATUS_PROCESSING => 'indigo',
            self::STATUS_SHIPPED => 'purple',
            self::STATUS_DELIVERED => 'teal',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
            self::STATUS_REFUNDED => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get payment status label
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return self::getPaymentStatusOptions()[$this->payment_status] ?? $this->payment_status;
    }

    /**
     * Get payment method label
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return self::getPaymentMethodOptions()[$this->payment_method] ?? $this->payment_method;
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'S/ ' . number_format($this->total, 2);
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'S/ ' . number_format($this->subtotal, 2);
    }

    /**
     * Get items count
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get customer name from shipping address
     */
    public function getCustomerNameAttribute(): string
    {
        return $this->shipping_address['name'] ?? $this->user?->name ?? 'Invitado';
    }

    /**
     * Get customer phone from shipping address
     */
    public function getCustomerPhoneAttribute(): ?string
    {
        return $this->shipping_address['phone'] ?? $this->user?->phone ?? null;
    }

    /**
     * Check if order can be cancelled
     */
    public function getCanBeCancelledAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_CONFIRMED]);
    }

    /**
     * Check if order is paid
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    // ============= METHODS =============

    /**
     * Update order status
     */
    public function updateStatus(string $status): void
    {
        $this->status = $status;

        if ($status === self::STATUS_DELIVERED) {
            $this->delivered_at = now();
        } elseif ($status === self::STATUS_SHIPPED) {
            $this->shipped_at = now();
        }

        $this->save();
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(): void
    {
        $this->payment_status = self::PAYMENT_PAID;
        $this->paid_at = now();
        $this->status = self::STATUS_PAID;
        $this->save();
    }

    /**
     * Calculate totals
     */
    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->tax = $this->subtotal * ($this->tax_rate / 100);
        $this->total = $this->subtotal + $this->tax + $this->delivery_fee - $this->discount;
        $this->save();
    }

    /**
     * Create order from cart
     */
    public static function createFromCart(Cart $cart, array $shippingAddress, string $paymentMethod, ?int $userId = null): self
    {
        $taxRate = config('sneakerhub.tax_rate', 18);
        $deliveryFee = config('sneakerhub.delivery_fee', 15);

        $subtotal = $cart->subtotal;
        $tax = $subtotal * ($taxRate / 100);
        $total = $subtotal + $tax + $deliveryFee - $cart->discount_amount;

        $order = static::create([
            'user_id' => $userId,
            'status' => self::STATUS_PENDING,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'tax_rate' => $taxRate,
            'delivery_fee' => $deliveryFee,
            'discount' => $cart->discount_amount,
            'total' => $total,
            'payment_method' => $paymentMethod,
            'payment_status' => self::PAYMENT_PENDING,
            'shipping_address' => $shippingAddress,
            'coupon_code' => $cart->coupon_code,
        ]);

        // Create order items from cart
        foreach ($cart->items as $cartItem) {
            $order->items()->create([
                'product_id' => $cartItem->product_id,
                'sku' => $cartItem->product->sku,
                'name' => $cartItem->product->name,
                'size' => $cartItem->size,
                'color' => $cartItem->product->color,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
                'subtotal' => $cartItem->subtotal,
                'image_path' => $cartItem->product->mainImage?->path_thumb,
            ]);
        }

        return $order;
    }
}
