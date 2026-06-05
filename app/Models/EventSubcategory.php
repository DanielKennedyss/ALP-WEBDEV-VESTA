<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
    ];

    /**
     * Relasi: Subcategory Event milik satu Event utama
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Relasi: Subcategory Event memiliki banyak Product
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'event_subcategory_product');
    }
}
