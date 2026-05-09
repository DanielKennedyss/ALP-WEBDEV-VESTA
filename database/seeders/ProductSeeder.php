<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Tambahkan ini di atas

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Midnight Velvet Blazer', 'category' => 'Outerwear', 'price' => 2500000, 'stock' => 15, 'image' => 'blazer.jpg'],
            ['name' => 'Silk Satin Slip Dress', 'category' => 'Dress', 'price' => 1800000, 'stock' => 20, 'image' => 'dress.jpg'],
            ['name' => 'Oxford Tailored Trousers', 'category' => 'Pants', 'price' => 1200000, 'stock' => 25, 'image' => 'trousers.jpg'],
            ['name' => 'Cashmere Oversized Knit', 'category' => 'Clothing', 'price' => 2100000, 'stock' => 10, 'image' => 'knit.jpg'],
            ['name' => 'Artisanal Leather Loafers', 'category' => 'Footwear', 'price' => 3200000, 'stock' => 8, 'image' => 'loafers.jpg'],
            ['name' => 'Classic Trench Coat', 'category' => 'Outerwear', 'price' => 4500000, 'stock' => 5, 'image' => 'trench.jpg'],
            ['name' => 'Minimalist Linen Shirt', 'category' => 'Clothing', 'price' => 850000, 'stock' => 30, 'image' => 'shirt.jpg'],
            ['name' => 'Monogram Silk Scarf', 'category' => 'Accessories', 'price' => 1100000, 'stock' => 50, 'image' => 'scarf.jpg'],
            ['name' => 'Double-Breasted Overcoat', 'category' => 'Outerwear', 'price' => 5200000, 'stock' => 7, 'image' => 'overcoat.jpg'],
            ['name' => 'Chelsea Suede Boots', 'category' => 'Footwear', 'price' => 2800000, 'stock' => 12, 'image' => 'boots.jpg'],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'sku' => 'VST-' . strtoupper(Str::random(6)), // Generate SKU otomatis
                'name' => $product['name'],
                'category' => $product['category'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'image_path' => $product['image'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}