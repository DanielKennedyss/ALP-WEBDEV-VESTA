<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_inquiries', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('first_name');
            $blueprint->string('last_name');
            $blueprint->string('email');
            $blueprint->string('phone')->nullable();
            $blueprint->string('subject');
            $blueprint->text('message');
            // Menandai status pesan (PENDING, REPLIED)
            $blueprint->string('status')->default('PENDING'); 
            $blueprint->text('reply_message')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_inquiries');
    }
};