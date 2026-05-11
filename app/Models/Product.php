<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'category',
        'image_path',
    ];

    protected $casts = [
        'price' => 'decimal:0',
        'stock' => 'integer',
    ];
    // Hubungan One-to-Many: Satu Produk bisa punya banyak Transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}