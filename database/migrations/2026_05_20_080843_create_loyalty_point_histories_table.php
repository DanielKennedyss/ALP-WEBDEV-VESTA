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
    Schema::create('loyalty_point_histories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null'); // Terhubung ke invoice jika ada
        
        $table->enum('type', ['earn', 'redeem', 'refund']); // earn = poin masuk, redeem = poin terpakai, refund = poin dikembalikan karena gagal bayar
        $table->integer('points'); // jumlah poinnya (misal: 150 atau -50)
        $table->string('description'); // Keterangan, misal: "Points earned from Invoice #VS-20260520"
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_point_histories');
    }
};
