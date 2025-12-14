<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    const METHOD_CASH = 'cash';
    const METHOD_YAPE = 'yape';

    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'transaction_id',
        'yape_reference',
        'yape_phone',
        'metadata',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'paid_at' => 'datetime',
    ];

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_COMPLETED => 'Completado',
            self::STATUS_FAILED => 'Fallido',
            self::STATUS_REFUNDED => 'Reembolsado',
        ];
    }

    /**
     * Get the order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

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
            self::STATUS_COMPLETED => 'green',
            self::STATUS_FAILED => 'red',
            self::STATUS_REFUNDED => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'S/ ' . number_format($this->amount, 2);
    }

    /**
     * Check if payment is pending
     */
    public function getIsPendingAttribute(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment is completed
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Mark as completed
     */
    public function markAsCompleted(?string $transactionId = null): void
    {
        $this->status = self::STATUS_COMPLETED;
        $this->paid_at = now();
        if ($transactionId) {
            $this->transaction_id = $transactionId;
        }
        $this->save();

        // Update order status
        $this->order->markAsPaid();
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(?string $reason = null): void
    {
        $this->status = self::STATUS_FAILED;
        if ($reason) {
            $this->notes = $reason;
        }
        $this->save();
    }

    /**
     * Generate Yape reference
     */
    public static function generateYapeReference(): string
    {
        return 'YAPE-' . strtoupper(uniqid()) . '-' . time();
    }

    /**
     * Create payment for order
     */
    public static function createForOrder(Order $order): self
    {
        $data = [
            'order_id' => $order->id,
            'payment_method' => $order->payment_method,
            'amount' => $order->total,
            'currency' => 'PEN',
            'status' => self::STATUS_PENDING,
        ];

        if ($order->payment_method === self::METHOD_YAPE) {
            $data['yape_reference'] = self::generateYapeReference();
        }

        return static::create($data);
    }
}
