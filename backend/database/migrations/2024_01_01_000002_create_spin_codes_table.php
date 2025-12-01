<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spin_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->integer('spins_amount')->default(1); // số lượt quay được cấp
            $table->integer('max_uses')->nullable(); // null = không giới hạn
            $table->integer('used_count')->default(0);
            $table->string('note')->nullable(); // ghi chú admin
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['code', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spin_codes');
    }
};
