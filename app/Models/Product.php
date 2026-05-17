<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'category_id',
        'gender',
        'image_path',
    ];

    protected $casts = [
        'price' => 'decimal:0',
    ];
    
    /**
     * Relasi: Product milik satu Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi: Satu Product punya banyak Variant (ukuran)
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    // Hubungan One-to-Many: Satu Produk bisa punya banyak Transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    
    /**
     * Get total stock across all variants
     */
    public function getTotalStockAttribute(): int
    {
        return $this->variants->sum('stock');
    }

    /**
     * Check if any variant is low on stock
     */
    public function hasLowStock(): bool
    {
        return $this->variants->contains(function ($variant) {
            return $variant->isLowStock();
        });
    }

    /**
     * Check if all variants are out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->total_stock <= 0;
    }

    /**
     * Get size labels as comma-separated string
     */
    public function getSizeLabelsAttribute(): string
    {
        return $this->variants->pluck('size_label')->implode(', ');
    }
}
