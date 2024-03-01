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
        Schema::create('product_inspection_schedule_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')
                ->constrained(table: 'product_inspection_schedules')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('question');
            $table->mediumText('description')->nullable();
            $table->string('type')->default('text');
            $table->json('options')->nullable();
            $table->integer('order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inspection_schedule_questions');
    }
};
