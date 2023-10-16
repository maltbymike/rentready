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
            $table->boolean('approve_requested_clock_in')
                ->nullable()
                ->after('clock_in_requested');
            $table->timestamp('clocked_or_approved_time_in')
                ->virtualAs('CASE approve_requested_clock_in WHEN 1 THEN clock_in_requested ELSE clock_in_at END')
                ->nullable()
                ->after('approve_requested_clock_in');
            $table->boolean('approve_requested_clock_out')
                ->nullable()
                ->after('clock_out_requested');
            $table->timestamp('clocked_or_approved_time_out')
                ->virtualAs('CASE approve_requested_clock_out WHEN 1 THEN clock_out_requested ELSE clock_out_at END')
                ->nullable()
                ->after('approve_requested_clock_out');
            $table->dropColumn('clock_in_approved');
            $table->dropColumn('clock_out_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_clock_entries', function (Blueprint $table) {
            $table->dropColumn('clocked_or_approved_time_in');
            $table->dropColumn('approve_requested_clock_in');
            $table->dropColumn('clocked_or_approved_time_out');
            $table->dropColumn('approve_requested_clock_out');
            $table->timestamp('clock_in_approved')
                ->nullable()
                ->after('clock_in_requested');
            $table->timestamp('clock_out_approved')
                ->nullable()
                ->after('clock_out_requested');
        });
    }
};
