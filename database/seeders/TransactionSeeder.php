<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Sinkronisasi data transaksi dengan user_id dan status success.
     */
    public function run(): void
    {
        // 1. Ambil data pendukung
        $products = Product::all();
        
        // Mengambil user dengan role customer agar transaksi "menempel" ke akun yang benar
        $customers = User::where('role', 'customer')->get();

        // Guard Clause: Pastikan data pendukung sudah ada
        if ($products->isEmpty()) {
            $this->command->warn("Data Produk tidak ditemukan. Jalankan ProductSeeder terlebih dahulu!");
            return;
        }

        if ($customers->isEmpty()) {
            $this->command->warn("Data Customer tidak ditemukan. Pastikan UserSeeder sudah membuat user role 'customer'!");
            return;
        }

        // 2. Generate 50 Transaksi untuk mengisi Dashboard Analytics
        for ($i = 0; $i < 50; $i++) {
            $product = $products->random();
            $user = $customers->random();
            $qty = rand(1, 3);
            $totalPrice = $product->price * $qty;
            
            // Variasi tanggal dalam 2 bulan terakhir untuk grafik Sales Intelligence
            $randomDate = Carbon::now()->subDays(rand(0, 60));

            // Status Mix: Prioritaskan 'success' agar statistik Total Spending muncul di profil
            $statuses = ['success', 'success', 'processing', 'shipped', 'delivered', 'pending', 'failed'];
            $status = $statuses[array_rand($statuses)];

            Transaction::create([
                'user_id'        => $user->id, // MENGHUBUNGKAN KE CUSTOMER (Daniel/Ella/Angie)
                'product_id'     => $product->id,
                'invoice_number' => 'INV-' . $randomDate->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'quantity'       => $qty,
                'total_price'    => $totalPrice,
                'customer_name'  => $user->name,
                'customer_email' => $user->email,
                'status'         => $status, 
                'paid_at'        => in_array($status, ['success', 'processing', 'shipped', 'delivered']) ? $randomDate : null,
                'created_at'     => $randomDate,
                'updated_at'     => $randomDate,
            ]);
        }

        $this->command->info('TransactionSeeder sukses: 50 data transaksi sinkron telah ditambahkan.');
    }
}