<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Product;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil satu produk contoh (Pastikan sudah ada produk di tabel products)
        $product = Product::first();

        if ($product) {
            Transaction::create([
                'product_id' => $product->id,
                'quantity' => 2,
                'total_price' => $product->price * 2,
                'status' => 'completed',
            ]);
        }
    }
}