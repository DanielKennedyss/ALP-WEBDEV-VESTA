<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // 1. RELASI (Tetap di sini agar tabel terbentuk dengan benar)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // 2. DETAIL DASAR
            $table->integer('quantity');
            $table->decimal('total_price', 15, 2);
            $table->string('customer_name');

            // 3. STATUS DASAR
            $table->enum('status', ['pending', 'success', 'failed', 'expired', 'cancelled'])
                  ->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};