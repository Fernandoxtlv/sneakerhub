<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    const TYPE_SALE = 'sale';
    const TYPE_PURCHASE = 'purchase';
    const TYPE_ADJUSTMENT = 'adjustment';
    const TYPE_RETURN = 'return';
    const TYPE_DAMAGE = 'damage';

    protected $fillable = [
        'product_id',
        'quantity',
        'stock_before',
        'stock_after',
        'type',
        'reason',
        'reference_type',
        'reference_id',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    /**
     * Get type options
     */
    public static function getTypeOptions(): array
    {
        return [
            self::TYPE_SALE => 'Venta',
            self::TYPE_PURCHASE => 'Compra',
            self::TYPE_ADJUSTMENT => 'Ajuste',
            self::TYPE_RETURN => 'Devolución',
            self::TYPE_DAMAGE => 'Daño',
        ];
    }

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reference model
     */
    public function reference()
    {
        if ($this->reference_type && $this->reference_id) {
            return $this->morphTo('reference', 'reference_type', 'reference_id');
        }
        return null;
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return self::getTypeOptions()[$this->type] ?? $this->type;
    }

    /**
     * Check if increase
     */
    public function getIsIncreaseAttribute(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * Check if decrease
     */
    public function getIsDecreaseAttribute(): bool
    {
        return $this->quantity < 0;
    }

    /**
     * Get formatted quantity
     */
    public function getFormattedQuantityAttribute(): string
    {
        $prefix = $this->quantity > 0 ? '+' : '';
        return $prefix . $this->quantity;
    }
}
