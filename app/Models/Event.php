<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'theme_color',
        'text_color',
        'banner_image',
        'short_name',
        'background_image',
        'main_image',
        'display_title',
        'display_description',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Relasi: Event memiliki banyak Subcategory Event
     */
    public function subcategories()
    {
        return $this->hasMany(EventSubcategory::class);
    }

    /**
     * Relasi: Event dapat memiliki banyak Product secara langsung
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'event_product');
    }

    /*
    |--------------------------------------------------------------------------
    | Local Environment Image Fallback Accessors
    |--------------------------------------------------------------------------
    */

    public function getBackgroundImageAttribute($value)
    {
        return $this->resolveImageUrl($value);
    }

    public function getMainImageAttribute($value)
    {
        return $this->resolveImageUrl($value);
    }

    public function getBannerImageAttribute($value)
    {
        return $this->resolveImageUrl($value);
    }

    private function resolveImageUrl($value)
    {
        if (!$value) {
            return $value;
        }

        if (\Illuminate\Support\Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        if (app()->environment('local')) {
            $localPath = public_path('storage/' . $value);
            if (!file_exists($localPath)) {
                return 'https://nicholasd.my.id/storage/' . $value;
            }
        }

        return $value;
    }
}
