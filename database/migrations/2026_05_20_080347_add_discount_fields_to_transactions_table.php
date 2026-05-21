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
        Schema::table('transactions', function (Blueprint $table) {
            // Menambahkan kolom kalkulasi diskon setelah kolom quantity
            $table->decimal('subtotal', 15, 2)->default(0.00)->after('quantity');
            $table->decimal('discount_voucher', 15, 2)->default(0.00)->after('subtotal');
            $table->decimal('discount_points', 15, 2)->default(0.00)->after('discount_voucher');
            $table->integer('points_redeemed')->default(0)->after('discount_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'discount_voucher', 'discount_points', 'points_redeemed']);
        });
    }
};  