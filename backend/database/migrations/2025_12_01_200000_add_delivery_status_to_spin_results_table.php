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
        Schema::table('spin_results', function (Blueprint $table) {
            $table->string('delivery_status', 20)->default('pending')->after('status');
            $table->text('delivery_note')->nullable()->after('delivery_status');
            $table->timestamp('delivered_at')->nullable()->after('delivery_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spin_results', function (Blueprint $table) {
            $table->dropColumn(['delivery_status', 'delivery_note', 'delivered_at']);
        });
    }
};
