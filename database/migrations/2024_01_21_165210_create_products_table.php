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
            $table->string('reference', 30);
            $table->string('name', 255);
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained(table: 'products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('manufacturer_id')
                ->nullable()
                ->constrained(table: 'product_manufacturers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('model', 50)
                ->nullable();
            $table->string('model_type')
                ->nullable();
            $table->string('serial_number', 50)
                ->nullable();
            $table->integer('model_year')
                ->nullable();
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
