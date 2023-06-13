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
        Schema::create('time_clock_entry_alternates', function (Blueprint $table) {
            $table->foreignId('time_clock_entry_id');
            $table->foreign('time_clock_entry_id')->references('id')->on('time_clock_entries');
            $table->foreignId('alternate_id');
            $table->foreign('alternate_id')->references('id')->on('time_clock_entries');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_clock_entry_alternates');
    }
};
