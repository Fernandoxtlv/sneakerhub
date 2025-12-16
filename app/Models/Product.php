<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'brand_id',
        'category_id',
        'description',
        'short_description',
        'price',
        'cost_price',
        'discount',
        'discount_price',
        'stock',
        'sizes_available',
        'color',
        'material',
        'gender',
        'featured',
        'is_active',
        'is_new',
        'rating_avg',
        'rating_count',
        'views_count',
        'sales_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'discount' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'rating_avg' => 'decimal:2',
        'stock' => 'integer',
        'rating_count' => 'integer',
        'views_count' => 'integer',
        'sales_count' => 'integer',
        'sizes_available' => 'array',
        'featured' => 'boolean',
        'is_active' => 'boolean',
        'is_new' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'SNK-' . strtoupper(Str::random(8));
            }
            $product->calculateDiscountPrice();
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
            if ($product->isDirty(['price', 'discount'])) {
                $product->calculateDiscountPrice();
            }
        });
    }

    /**
     * Calculate discount price
     */
    public function calculateDiscountPrice(): void
    {
        if ($this->discount > 0) {
            $this->discount_price = $this->price - ($this->price * $this->discount / 100);
        } else {
            $this->discount_price = null;
        }
    }

    /**
     * Get route key name
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Get the brand
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get product images
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    /**
     * Get main image
     */
    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    /**
     * Get order items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get cart items
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get stock movements
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    // ============= SCOPES =============

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope for new products
     */
    public function scopeNew($query)
    {
        return $query->where('is_new', true);
    }

    /**
     * Scope for products in stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope for products with low stock
     */
    public function scopeLowStock($query, $threshold = 5)
    {
        return $query->where('stock', '<=', $threshold)->where('stock', '>', 0);
    }

    /**
     * Scope for out of stock products
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('stock', '<=', 0);
    }

    /**
     * Scope for products by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for products by brand
     */
    public function scopeByBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    /**
     * Scope for price range
     */
    public function scopePriceRange($query, $min = null, $max = null)
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }
        if ($max !== null) {
            $query->where('price', '<=', $max);
        }
        return $query;
    }

    /**
     * Scope for products with specific size
     */
    public function scopeHasSize($query, $size)
    {
        return $query->whereJsonContains('sizes_available', (int) $size);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('sku', 'like', "%{$term}%");
        });
    }

    // ============= ACCESSORS =============

    /**
     * Get current price (with discount if applicable)
     */
    public function getCurrentPriceAttribute(): float
    {
        return $this->discount_price ?? $this->price;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'S/ ' . number_format($this->current_price, 2);
    }

    /**
     * Get formatted original price
     */
    public function getFormattedOriginalPriceAttribute(): string
    {
        return 'S/ ' . number_format($this->price, 2);
    }

    /**
     * Check if product has discount
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->discount > 0;
    }

    /**
     * Check if product is in stock
     */
    public function getIsInStockAttribute(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Get main image URL
     */
    public function getMainImageUrlAttribute(): string
    {
        $mainImage = $this->mainImage;
        if ($mainImage) {
            return $mainImage->url;
        }
        return asset('images/placeholder-sneaker.png');
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute(): string
    {
        $mainImage = $this->mainImage;
        if ($mainImage) {
            return $mainImage->thumbnail_url;
        }
        return $this->main_image_url;
    }

    /**
     * Get stock status label
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'Agotado';
        } elseif ($this->stock <= 5) {
            return 'Últimas unidades';
        }
        return 'En stock';
    }

    /**
     * Get stock status color
     */
    public function getStockStatusColorAttribute(): string
    {
        if ($this->stock <= 0) {
            return 'red';
        } elseif ($this->stock <= 5) {
            return 'yellow';
        }
        return 'green';
    }

    // ============= METHODS =============

    /**
     * Decrease stock
     */
    public function decreaseStock(int $quantity, string $reason = 'sale', $referenceType = null, $referenceId = null, $userId = null): void
    {
        $stockBefore = $this->stock;
        $this->stock -= $quantity;
        $this->save();

        StockMovement::create([
            'product_id' => $this->id,
            'quantity' => -$quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $this->stock,
            'type' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'user_id' => $userId,
        ]);
    }

    /**
     * Increase stock
     */
    public function increaseStock(int $quantity, string $reason = 'purchase', $referenceType = null, $referenceId = null, $userId = null): void
    {
        $stockBefore = $this->stock;
        $this->stock += $quantity;
        $this->save();

        StockMovement::create([
            'product_id' => $this->id,
            'quantity' => $quantity,
            'stock_before' => $stockBefore,
            'stock_after' => $this->stock,
            'type' => $reason,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'user_id' => $userId,
        ]);
    }

    /**
     * Increment views
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Increment sales
     */
    public function incrementSales(int $quantity = 1): void
    {
        $this->increment('sales_count', $quantity);
    }
}
