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
        Schema::create('timeclock_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained(table: 'users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamp('start_time');
            $table->timestamp('end_time')
                ->nullable();
            $table->foreignId('clockable_id');
            $table->string('clockable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timeclock_entries');
    }
};
