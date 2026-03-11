<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modificar el enum para agregar nuevos tipos
        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM(
            'purchase',
            'sale',
            'adjustment',
            'waste',
            'return',
            'return_supplier',
            'initial',
            'transfer'
        ) NOT NULL");

        Schema::table('stock_movements', function (Blueprint $table) {
            // Costo unitario al momento del movimiento
            $table->decimal('unit_cost', 10, 2)->nullable()->after('quantity');

            // Número de referencia / documento (factura, orden, etc.)
            $table->string('reference', 100)->nullable()->after('reason');

            // Usuario que registró el movimiento
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('reference');
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn(['unit_cost', 'reference']);
        });

        DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM(
            'purchase',
            'sale',
            'adjustment',
            'waste'
        ) NOT NULL");
    }
};
