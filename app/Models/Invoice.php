<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'invoice_number',
        'type',
        'pdf_path',
        'ruc',
        'business_name',
        'subtotal',
        'tax',
        'total',
        'issued_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'issued_at' => 'datetime',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateInvoiceNumber($invoice->type);
            }
            if (empty($invoice->issued_at)) {
                $invoice->issued_at = now();
            }
        });
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber(string $type = 'boleta'): string
    {
        $prefix = $type === 'factura' ? 'FAC-' : 'BOL-';
        $number = static::where('type', $type)->max('id') ?? 0;
        $number++;
        return $prefix . str_pad($number, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Get the order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get PDF URL
     */
    public function getPdfUrlAttribute(): ?string
    {
        if ($this->pdf_path) {
            return asset('storage/' . $this->pdf_path);
        }
        return null;
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'factura' ? 'Factura' : 'Boleta';
    }

    /**
     * Get formatted total
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'S/ ' . number_format($this->total, 2);
    }

    /**
     * Check if PDF exists
     */
    public function getHasPdfAttribute(): bool
    {
        return !empty($this->pdf_path);
    }
}
