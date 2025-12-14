<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_code',
        'discount_amount',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
    ];

    /**
     * Get the user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get cart items
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get cart subtotal
     */
    public function getSubtotalAttribute(): float
    {
        return $this->items->sum('subtotal');
    }

    /**
     * Get cart total
     */
    public function getTotalAttribute(): float
    {
        return $this->subtotal - $this->discount_amount;
    }

    /**
     * Get items count
     */
    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /**
     * Get formatted subtotal
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return 'S/ ' . number_format($this->subtotal, 2);
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'S/ ' . number_format($this->total, 2);
    }

    /**
     * Check if cart is empty
     */
    public function getIsEmptyAttribute(): bool
    {
        return $this->items->isEmpty();
    }

    /**
     * Add item to cart
     */
    public function addItem(Product $product, int $quantity = 1, ?string $size = null): CartItem
    {
        $existingItem = $this->items()
            ->where('product_id', $product->id)
            ->where('size', $size)
            ->first();

        if ($existingItem) {
            $existingItem->quantity += $quantity;
            $existingItem->subtotal = $existingItem->quantity * $existingItem->price;
            $existingItem->save();
            return $existingItem;
        }

        $price = $product->current_price;

        return $this->items()->create([
            'product_id' => $product->id,
            'size' => $size,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $price * $quantity,
        ]);
    }

    /**
     * Update item quantity
     */
    public function updateItemQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity <= 0) {
            $item->delete();
            return;
        }

        $item->quantity = $quantity;
        $item->subtotal = $item->price * $quantity;
        $item->save();
    }

    /**
     * Remove item from cart
     */
    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    /**
     * Clear cart
     */
    public function clear(): void
    {
        $this->items()->delete();
        $this->coupon_code = null;
        $this->discount_amount = 0;
        $this->save();
    }

    /**
     * Get or create cart for user/session
     */
    public static function getOrCreate(?int $userId = null, ?string $sessionId = null): self
    {
        if ($userId) {
            return static::firstOrCreate(['user_id' => $userId]);
        }

        if ($sessionId) {
            return static::firstOrCreate(['session_id' => $sessionId]);
        }

        throw new \InvalidArgumentException('User ID or Session ID is required');
    }

    /**
     * Merge guest cart with user cart
     */
    public function mergeWith(Cart $guestCart): void
    {
        foreach ($guestCart->items as $item) {
            $this->addItem($item->product, $item->quantity, $item->size);
        }
        $guestCart->delete();
    }
}
