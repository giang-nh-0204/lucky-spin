<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price')->default(0);
            $table->string('image')->nullable();
            $table->string('color')->default('#ffffff');
            $table->decimal('probability', 5, 4)->default(0.0500); // 0.0001 - 1.0000
            $table->boolean('is_active')->default(true);
            $table->integer('stock')->nullable(); // null = vô hạn
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prizes');
    }
};
