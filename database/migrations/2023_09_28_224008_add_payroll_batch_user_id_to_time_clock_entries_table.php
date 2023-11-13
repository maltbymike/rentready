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
            $table->unsignedBigInteger('payroll_batch_user_id')
                ->nullable()
                ->after('user_id');
            $table->foreign('payroll_batch_user_id')->references('id')->on('payroll_batch_user')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_clock_entries', function (Blueprint $table) {
            $table->dropForeign('time_clock_entries_payroll_batch_user_id_foreign');
            $table->dropColumn('payroll_batch_user_id');
        });
    }
};
