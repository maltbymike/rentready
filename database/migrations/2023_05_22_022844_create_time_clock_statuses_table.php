<?php

use App\Models\TimeClockStatus;
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
        Schema::create('time_clock_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->timestamps();
        });

        TimeClockStatus::create([
            'name' => 'Created'
        ]);
        TimeClockStatus::create([
            'name' => 'Approved',
        ]);
        TimeClockStatus::create([
            'name' => 'Rejected',
        ]);
        TimeClockStatus::create([
            'name' => 'Paid',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_clock_statuses');
    }
};
