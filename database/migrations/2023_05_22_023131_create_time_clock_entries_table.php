<?php

use App\Models\TimeClockStatus;
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
        $defaultStatus = TimeClockStatus::firstWhere('name', 'Created')->id;

        Schema::create('time_clock_entries', function (Blueprint $table) use ($defaultStatus) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->timestamp('clock_in_at')->nullable();
            $table->timestamp('clock_out_at')->nullable();
            $table->foreignId('status_id')
                ->default($defaultStatus)
                ->nullable()
                ->constrained(table: 'time_clock_statuses');
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
