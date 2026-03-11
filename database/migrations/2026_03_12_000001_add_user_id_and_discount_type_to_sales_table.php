<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('notes')
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('discount_type', ['amount', 'percentage'])
                ->default('amount')
                ->after('discount');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'discount_type']);
        });
    }
};
