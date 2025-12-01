<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spin_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('spin_sessions')->cascadeOnDelete();
            $table->foreignId('prize_id')->constrained('prizes');
            $table->string('spin_token', 64)->unique();
            $table->decimal('target_angle', 8, 2); // góc quay
            $table->enum('status', ['pending', 'claimed', 'expired'])->default('pending');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();

            $table->index('spin_token');
            $table->index(['session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spin_results');
    }
};
