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
        'weight',
        'category_id',
        'gender',
        'image_path',
        'size_type', // Tambahkan jika ada di migration
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'weight' => 'integer',
    ];

    // Menambahkan total_stock ke JSON output secara otomatis
    protected $appends = ['total_stock', 'size_labels'];
    
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    
    /**
     * Accessor: Total stock dari semua varian
     * Digunakan di tabel Inventory: {{ $product->total_stock }}
     */
    public function getTotalStockAttribute(): int
    {
        return $this->variants->sum('stock');
    }

    /**
     * Logika Low Stock
     */
    public function hasLowStock(): bool
    {
        return $this->variants->contains(function ($variant) {
            // Memastikan method isLowStock() ada di Model ProductVariant
            return $variant->stock <= $variant->minimum_stock;
        });
    }

    /**
     * Logika Out of Stock
     */
    public function isOutOfStock(): bool
    {
        return $this->getTotalStockAttribute() <= 0;
    }

    /**
     * Accessor: Label Ukuran (S, M, L, XL)
     */
    public function getSizeLabelsAttribute(): string
    {
        return $this->variants->pluck('size_label')->implode(', ');
    }
}