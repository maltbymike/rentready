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
        Schema::table('payroll_pay_types', function (Blueprint $table) {
            $table->boolean('is_visible_on_batch_review')
                ->after('is_used_for_stat_pay')
                ->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_pay_types', function (Blueprint $table) {
            $table->dropColumn('is_visible_on_batch_review');
        });
    }
};
