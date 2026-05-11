<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Midnight Velvet Blazer',
                'category' => 'Outerwear',
                'price' => 2500000,
                'stock' => 15,
                'image_path' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=600&q=80',
                'description' => 'Luxurious midnight velvet blazer with silk lining. Perfect for evening occasions and formal events.',
            ],
            [
                'name' => 'Silk Satin Slip Dress',
                'category' => 'Dress',
                'price' => 1800000,
                'stock' => 20,
                'image_path' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=600&q=80',
                'description' => 'Elegant silk satin slip dress with delicate draping. Minimalist design for the modern woman.',
            ],
            [
                'name' => 'Oxford Tailored Trousers',
                'category' => 'Pants',
                'price' => 1200000,
                'stock' => 25,
                'image_path' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=600&q=80',
                'description' => 'Classic oxford tailored trousers with perfect fit. Premium wool blend for ultimate comfort.',
            ],
            [
                'name' => 'Cashmere Oversized Knit',
                'category' => 'Clothing',
                'price' => 2100000,
                'stock' => 10,
                'image_path' => 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=600&q=80',
                'description' => 'Luxurious cashmere oversized knit sweater. Soft, warm, and effortlessly elegant.',
            ],
            [
                'name' => 'Artisanal Leather Loafers',
                'category' => 'Footwear',
                'price' => 3200000,
                'stock' => 8,
                'image_path' => 'https://images.unsplash.com/photo-1614252369475-531eba835eb1?w=600&q=80',
                'description' => 'Handcrafted Italian leather loafers with precision stitching. Timeless sophistication.',
            ],
            [
                'name' => 'Classic Trench Coat',
                'category' => 'Outerwear',
                'price' => 4500000,
                'stock' => 5,
                'image_path' => 'https://images.unsplash.com/photo-1539533018447-63fcce2678e3?w=600&q=80',
                'description' => 'Iconic trench coat with signature details. Water-resistant gabardine for all seasons.',
            ],
            [
                'name' => 'Minimalist Linen Shirt',
                'category' => 'Clothing',
                'price' => 850000,
                'stock' => 30,
                'image_path' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&q=80',
                'description' => 'Premium linen shirt with relaxed fit. Breathable fabric for effortless summer style.',
            ],
            [
                'name' => 'Monogram Silk Scarf',
                'category' => 'Accessories',
                'price' => 1100000,
                'stock' => 50,
                'image_path' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=600&q=80',
                'description' => 'Signature monogram silk scarf with hand-rolled edges. Versatile accessory for any outfit.',
            ],
            [
                'name' => 'Double-Breasted Overcoat',
                'category' => 'Outerwear',
                'price' => 5200000,
                'stock' => 7,
                'image_path' => 'https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=600&q=80',
                'description' => 'Sophisticated double-breasted overcoat in premium wool. Statement piece for the discerning gentleman.',
            ],
            [
                'name' => 'Chelsea Suede Boots',
                'category' => 'Footwear',
                'price' => 2800000,
                'stock' => 12,
                'image_path' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&q=80',
                'description' => 'Classic chelsea boots in premium suede. Elastic side panels and pull tab for easy wear.',
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'sku' => 'VST-' . strtoupper(Str::random(6)),
                'name' => $product['name'],
                'category' => $product['category'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'image_path' => $product['image_path'],
                'description' => $product['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
