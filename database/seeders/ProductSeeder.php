<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil category IDs
        $outerwear   = Category::where('name', 'Outerwear')->first()->id;
        $dress       = Category::where('name', 'Dress')->first()->id;
        $pants       = Category::where('name', 'Pants')->first()->id;
        $clothing    = Category::where('name', 'Clothing')->first()->id;
        $accessories = Category::where('name', 'Accessories')->first()->id;

        $products = [
            [
                'name' => 'Midnight Velvet Blazer',
                'category_id' => $outerwear,
                'gender' => 'Male',
                'price' => 2500000,
                'weight' => 800, // 800 grams
                'image_path' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=600&q=80',
                'description' => 'Luxurious midnight velvet blazer with silk lining. Perfect for evening occasions and formal events.',
                // Custom sizes: S, M, L, XL
                'variants' => [
                    ['size_label' => 'S',  'stock' => 10, 'minimum_stock' => 5],
                    ['size_label' => 'M',  'stock' => 15, 'minimum_stock' => 5],
                    ['size_label' => 'L',  'stock' => 12, 'minimum_stock' => 5],
                    ['size_label' => 'XL', 'stock' => 8,  'minimum_stock' => 3],
                ],
            ],
            [
                'name' => 'Oxford Tailored Trousers',
                'category_id' => $pants,
                'gender' => 'Male',
                'price' => 1200000,
                'weight' => 500, // 500 grams
                'image_path' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=600&q=80',
                'description' => 'Classic oxford tailored trousers with perfect fit. Premium wool blend for ultimate comfort.',
                'variants' => [
                    ['size_label' => 'S',  'stock' => 20, 'minimum_stock' => 5],
                    ['size_label' => 'M',  'stock' => 25, 'minimum_stock' => 5],
                    ['size_label' => 'L',  'stock' => 18, 'minimum_stock' => 5],
                    ['size_label' => 'XL', 'stock' => 10, 'minimum_stock' => 5],
                ],
            ],
            [
                'name' => 'Minimalist Linen Shirt',
                'category_id' => $clothing,
                'gender' => 'Unisex',
                'price' => 850000,
                'weight' => 300, // 300 grams
                'image_path' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&q=80',
                'description' => 'Premium linen shirt with relaxed fit. Breathable fabric for effortless summer style.',
                'variants' => [
                    ['size_label' => 'S',  'stock' => 30, 'minimum_stock' => 10],
                    ['size_label' => 'M',  'stock' => 30, 'minimum_stock' => 10],
                    ['size_label' => 'L',  'stock' => 25, 'minimum_stock' => 10],
                    ['size_label' => 'XL', 'stock' => 15, 'minimum_stock' => 5],
                ],
            ],
            [
                'name' => 'Elegant Evening Dress',
                'category_id' => $dress,
                'gender' => 'Female',
                'price' => 3200000,
                'weight' => 600, // 600 grams
                'image_path' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=600&q=80',
                'description' => 'Stunning evening dress with flowing silhouette. Perfect for gala events and special occasions.',
                'variants' => [
                    ['size_label' => 'S',  'stock' => 5,  'minimum_stock' => 3],
                    ['size_label' => 'M',  'stock' => 10, 'minimum_stock' => 3],
                    ['size_label' => 'L',  'stock' => 7,  'minimum_stock' => 3],
                ],
            ],
            [
                'name' => 'Monogram Silk Tie',
                'category_id' => $accessories,
                'gender' => 'Male',
                'price' => 750000,
                'weight' => 100, // 100 grams
                'image_path' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=600&q=80',
                'description' => 'Signature monogram silk tie. Versatile accessory for any formal outfit.',
                // Accessories = One Size
                'variants' => [
                    ['size_label' => 'One Size', 'stock' => 50, 'minimum_stock' => 20],
                ],
            ],
            [
                'name' => 'Classic Fedora Hat',
                'category_id' => $accessories,
                'gender' => 'Unisex',
                'price' => 1500000,
                'weight' => 200, // 200 grams
                'image_path' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=600&q=80',
                'description' => 'Classic fedora hat in premium wool.',
                'variants' => [
                    ['size_label' => 'One Size', 'stock' => 12, 'minimum_stock' => 5],
                ],
            ],
            [
                'name' => 'Oversized Wool Coat',
                'category_id' => $outerwear,
                'gender' => 'Female',
                'price' => 4500005,
                'weight' => 1200, // 1200 grams
                'image_path' => 'https://images.unsplash.com/photo-1539533113208-f6df8cc8b543?w=600&q=80',
                'description' => 'Luxurious oversized wool coat with premium craftsmanship. A winter essential.',
                'variants' => [
                    ['size_label' => 'M',  'stock' => 4,  'minimum_stock' => 3],
                    ['size_label' => 'L',  'stock' => 8,  'minimum_stock' => 3],
                    ['size_label' => 'XL', 'stock' => 3,  'minimum_stock' => 3],
                ],
            ],
            [
                'name' => 'Leather Belt',
                'category_id' => $accessories,
                'gender' => 'Unisex',
                'price' => 950000,
                'weight' => 150, // 150 grams
                'image_path' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600&q=80',
                'description' => 'Premium full-grain leather belt with signature buckle. Timeless accessory for every wardrobe.',
                'variants' => [
                    ['size_label' => 'One Size', 'stock' => 40, 'minimum_stock' => 15],
                ],
            ],
        ];

        $skuCounter = 1;
        foreach ($products as $productData) {
            $variants = $productData['variants'];
            unset($productData['variants']);

            // Ambil nama category untuk SKU
            $category = Category::find($productData['category_id']);
            $categoryCode = strtoupper(substr($category->name, 0, 3));
            $sku = 'VST-' . $categoryCode . '-' . str_pad($skuCounter, 3, '0', STR_PAD_LEFT);

            $productId = DB::table('products')->insertGetId([
                'sku' => $sku,
                'name' => $productData['name'],
                'category_id' => $productData['category_id'],
                'gender' => $productData['gender'],
                'price' => $productData['price'],
                'weight' => $productData['weight'],
                'image_path' => $productData['image_path'],
                'description' => $productData['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert variants
            foreach ($variants as $variant) {
                DB::table('product_variants')->insert([
                    'product_id' => $productId,
                    'size_label' => $variant['size_label'],
                    'stock' => $variant['stock'],
                    'minimum_stock' => $variant['minimum_stock'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $skuCounter++;
        }
    }
}
