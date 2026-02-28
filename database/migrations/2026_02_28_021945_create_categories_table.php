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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // "Mariscos", "Cervezas", "Refrescos", "Entradas"
            $table->enum('type', ['menu', 'inventory', 'both'])->default('both');
            // menu = solo aparece en platillos
            // inventory = solo en productos de stock
            // both = aplica para ambos (ej: cervezas que se venden y se inventarían)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
