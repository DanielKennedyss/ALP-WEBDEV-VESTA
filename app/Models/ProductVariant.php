<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size_label',
        'stock',
        'minimum_stock',
    ];

    protected $casts = [
        'stock' => 'integer',
        'minimum_stock' => 'integer',
    ];

    /**
     * Relasi: Variant milik satu Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Cek apakah variant ini stoknya rendah
     */
    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock && $this->stock > 0;
    }

    /**
     * Cek apakah variant ini habis stok
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }
}
