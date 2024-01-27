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
        Schema::create('product_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')
                ->constrained(table: 'product_inspection_schedules')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('status_id')
                ->nullable()
                ->constrained(table: 'product_inspection_statuses')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamp('completed_at')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inspections');
    }
};
