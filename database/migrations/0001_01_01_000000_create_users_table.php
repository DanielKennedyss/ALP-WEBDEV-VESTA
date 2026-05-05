<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi (Membangun tabel).
     */
    public function up(): void
    {
        // 1. Tabel Utama: Users
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone_number')->nullable(); // Keperluan VESTA
            $table->string('avatar')->nullable();

            // RBAC & Membership System
            // Di file migration users
            $table->enum('role', ['owner', 'admin', 'customer'])->default('customer');
            $table->enum('membership_level', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
            $table->integer('loyalty_points')->default(0);
            $table->decimal('total_spending', 15, 2)->default(0.00);

            $table->enum('status', ['active', 'suspended'])->default('active');
            $table->rememberToken();
            $table->timestamps();
        });

        // 2. Tabel Sistem: Password Resets
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. Tabel Sistem: Sessions (Memperbaiki error image_993cf2.png)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Membatalkan migrasi (Menghapus tabel).
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};