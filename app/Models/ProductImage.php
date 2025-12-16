<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'filename',
        'path',
        'path_thumb',
        'path_medium',
        'alt_text',
        'is_main',
        'position',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'position' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Get the product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for main images
     */
    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    /**
     * Scope ordered by position
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }

    /**
     * Get original image URL
     */
    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }
        return asset('storage/' . $this->path);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute(): string
    {
        if ($this->path_thumb) {
            if (str_starts_with($this->path_thumb, 'http')) {
                return $this->path_thumb;
            }
            return asset('storage/' . $this->path_thumb);
        }
        return $this->url;
    }

    /**
     * Get medium image URL
     */
    public function getMediumUrlAttribute(): string
    {
        if ($this->path_medium) {
            if (str_starts_with($this->path_medium, 'http')) {
                return $this->path_medium;
            }
            return asset('storage/' . $this->path_medium);
        }
        return $this->url;
    }

    /**
     * Get alt text or product name
     */
    public function getAltAttribute(): string
    {
        return $this->alt_text ?? $this->product?->name ?? 'Sneaker image';
    }

    /**
     * Set as main image
     */
    public function setAsMain(): void
    {
        // Remove main from other images of this product
        static::where('product_id', $this->product_id)
            ->where('id', '!=', $this->id)
            ->update(['is_main' => false]);

        $this->is_main = true;
        $this->save();
    }
}
