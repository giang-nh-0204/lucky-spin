<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spin_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_token', 64)->unique();
            $table->foreignId('code_id')->nullable()->constrained('spin_codes')->nullOnDelete();
            $table->integer('spin_balance')->default(0);
            $table->integer('total_spins')->default(0); // tổng lượt đã quay
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('last_spin_at')->nullable();
            $table->timestamps();

            $table->index('session_token');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spin_sessions');
    }
};
