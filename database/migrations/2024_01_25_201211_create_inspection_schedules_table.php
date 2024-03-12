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
        Schema::create('product_inspection_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->nullable()
                ->constrained(table: 'products')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('procedure_id')
                ->constrained(table: 'product_inspection_procedures')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inspection_schedules');
    }
};
