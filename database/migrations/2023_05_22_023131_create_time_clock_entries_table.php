<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('time_clock_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->timestamp('clock_in_at')->nullable();
            $table->timestamp('clock_in_requested')->nullable();
            $table->timestamp('clock_in_approved')->nullable();
            $table->timestamp('clock_out_at')->nullable();
            $table->timestamp('clock_out_requested')->nullable();
            $table->timestamp('clock_out_approved')->nullable();
            $table->foreignId('approved_by_id')
                ->nullable()
                ->constrained(table: 'users');
            $table->date('payment_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_clock_entries');
    }
};
