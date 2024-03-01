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
        Schema::create('product_inspection_schedule_question_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_question_id')
                ->constrained(
                    table: 'product_inspection_schedule_questions',
                    indexName: 'product_inspection_question_answers_id_foreign',
                )
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('inspection_id')
                ->nullable()
                ->constrained(
                    table: 'product_inspections',
                    indexName: 'product_inspection_question_inspection_id_foreign',
                )
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inspection_schedule_question_answers');
    }
};
