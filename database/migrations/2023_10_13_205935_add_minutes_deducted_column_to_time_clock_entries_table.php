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
        Schema::table('time_clock_entries', function (Blueprint $table) {
            $table->integer('minutes_deducted')
                ->default(0)
                ->after('clock_out_approved');
            $table->string('deduction_reason')
                ->nullable()
                ->after('minutes_deducted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_clock_entries', function (Blueprint $table) {
            $table->dropColumn('minutes_deducted');
            $table->dropColumn('deduction_reason');
        });
    }
};
