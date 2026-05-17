<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Menambahkan kolom yang spesifik untuk Midtrans & Identitas tambahan
            $table->string('invoice_number')->unique()->after('id'); 
            $table->string('customer_email')->nullable()->after('customer_name');
            $table->text('shipping_address')->nullable()->after('customer_email');
            $table->string('payment_url')->nullable()->after('status');
            $table->timestamp('paid_at')->nullable()->after('payment_url');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'customer_email', 'shipping_address', 'payment_url', 'paid_at']);
        });
    }
};