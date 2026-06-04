<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductReview;
use App\Models\Product;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $customers = User::where('role', 'customer')->get();

        if ($products->isEmpty()) {
            $this->command->warn("Data Produk tidak ditemukan. Jalankan ProductSeeder terlebih dahulu!");
            return;
        }

        if ($customers->isEmpty()) {
            $this->command->warn("Data Customer tidak ditemukan. Jalankan UserSeeder terlebih dahulu!");
            return;
        }

        $reviewTemplates = [
            5 => [
                "Produknya luar biasa bagus! Sangat puas belanja di sini.",
                "Kualitasnya premium sekali, sangat melebihi ekspektasi saya.",
                "Desainnya estetik dan fungsional. Next order lagi pasti!",
                "Bahan tebal dan jahitan sangat rapi. Recommended seller!",
                "Bagus banget, pengiriman cepat dan respon adminnya ramah sekali.",
                "Cocok banget dipakai sehari-hari, nyaman banget bahannya.",
                "Desainnya mewah dan harganya sangat worth it untuk kualitas sekeren ini!"
            ],
            4 => [
                "Barangnya bagus, sesuai deskripsi. Pengiriman juga lumayan cepat.",
                "Secara keseluruhan oke banget, cuma packingnya kurang tebal sedikit.",
                "Kualitas mantap, berfungsi dengan baik. Terima kasih seller.",
                "Bahan bagus dan pas di badan, cuman warna aslinya sedikit lebih gelap.",
                "Puas belanja disini, respon seller cepat dan produk memuaskan."
            ],
            3 => [
                "Biasa saja sih, kualitas standar sesuai dengan harganya.",
                "Barang sampai dengan selamat, tapi pengirimannya agak lama ya.",
                "Kualitasnya lumayan, tapi ada beberapa bagian yang kurang rapi."
            ]
        ];

        $this->command->info("Memulai seeding review produk...");

        foreach ($products as $product) {
            // Generate antara 4 hingga 7 review per produk agar slider transisi bisa dites dengan baik
            $numReviews = rand(4, 7);
            
            // Acak urutan customer agar tidak monoton
            $shuffledCustomers = $customers->shuffle();
            $reviewsCreated = 0;

            foreach ($shuffledCustomers as $customer) {
                if ($reviewsCreated >= $numReviews) {
                    break;
                }

                // Cari atau buat transaksi agar review valid secara FK
                $transaction = Transaction::where('user_id', $customer->id)
                    ->where('product_id', $product->id)
                    ->first();

                if (!$transaction) {
                    $qty = rand(1, 2);
                    $totalPrice = $product->price * $qty;
                    $randomDate = Carbon::now()->subDays(rand(1, 30));

                    $transaction = Transaction::create([
                        'user_id'        => $customer->id,
                        'product_id'     => $product->id,
                        'invoice_number' => 'INV-' . $randomDate->format('Ymd') . '-' . strtoupper(Str::random(6)),
                        'quantity'       => $qty,
                        'total_price'    => $totalPrice,
                        'customer_name'  => $customer->name,
                        'customer_email' => $customer->email,
                        'status'         => 'success',
                        'paid_at'        => $randomDate,
                        'created_at'     => $randomDate,
                        'updated_at'     => $randomDate,
                    ]);
                }

                // Cek agar tidak melanggar unique key constraint: ['user_id', 'transaction_id', 'product_id']
                $exists = ProductReview::where('user_id', $customer->id)
                    ->where('transaction_id', $transaction->id)
                    ->where('product_id', $product->id)
                    ->exists();

                if (!$exists) {
                    $rating = rand(3, 5); // Review dominan bagus (3, 4, atau 5)
                    $comments = $reviewTemplates[$rating];
                    $comment = $comments[array_rand($comments)];
                    
                    // Variasikan sedikit agar ada tanggal pembuatan review
                    $reviewDate = Carbon::parse($transaction->created_at)->addHours(rand(1, 48));

                    ProductReview::create([
                        'user_id'        => $customer->id,
                        'product_id'     => $product->id,
                        'transaction_id' => $transaction->id,
                        'rating'         => $rating,
                        'comment'        => $comment,
                        'created_at'     => $reviewDate,
                        'updated_at'     => $reviewDate,
                    ]);

                    $reviewsCreated++;
                }
            }
        }

        $this->command->info("ProductReviewSeeder sukses: Review produk telah ditambahkan.");
    }
}
