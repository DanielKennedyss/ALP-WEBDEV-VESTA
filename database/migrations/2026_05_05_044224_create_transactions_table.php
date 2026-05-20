<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // 1. RELASI 
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // 2. DETAIL DASAR
            $table->integer('quantity');
            $table->decimal('total_price', 15, 2);
            $table->string('customer_name');

            // 3. STATUS LOGISTIK & PEMBAYARAN (Diperbarui dengan alur baru)
            $table->enum('status', [
                'pending', 
                'processing', 
                'shipped', 
                'delivered', 
                'success',    // Dipertahankan untuk kompatibilitas Midtrans
                'failed',     // Dipertahankan untuk kompatibilitas Midtrans
                'expired', 
                'cancelled'
            ])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};