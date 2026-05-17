<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('id');
            $table->string('payment_url')->nullable()->after('status');
            $table->timestamp('paid_at')->nullable()->after('payment_url');
            $table->string('customer_email')->nullable()->after('customer_name');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'payment_url', 'paid_at', 'customer_email']);
        });
    }
};