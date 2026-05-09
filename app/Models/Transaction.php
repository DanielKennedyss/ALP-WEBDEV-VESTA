<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'product_id', 
        'quantity', 
        'total_price', 
        'status'
    ];

    // Hubungan Many-to-One: Banyak Transaksi merujuk ke satu Produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}