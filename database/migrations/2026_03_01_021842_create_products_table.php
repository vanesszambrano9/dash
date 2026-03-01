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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('sku')->unique()->nullable();     // Código interno
            $table->string('unit');                          // pieza, litro, kg, caja, botella
            $table->decimal('purchase_price', 10, 2);        // Precio al que se compra
            $table->decimal('sale_price', 10, 2)->nullable();// Precio al que se vende
            $table->decimal('stock', 10, 2)->default(0);     // Stock actual
            $table->decimal('min_stock', 10, 2)->default(5); // Alerta de stock mínimo
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
