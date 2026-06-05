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
        // Mengecek apakah kolom 'otp_code' BELUM ada di tabel users
        if (!Schema::hasColumn('users', 'otp_code')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('otp_code', 6)->nullable()->after('password');
                $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menggunakan if untuk memastikan kolomnya ada sebelum di-drop (lebih aman)
            if (Schema::hasColumn('users', 'otp_code')) {
                $table->dropColumn(['otp_code', 'otp_expires_at']);
            }
        });
    }
};