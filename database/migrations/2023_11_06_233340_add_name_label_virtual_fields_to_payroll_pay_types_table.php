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
            $table->string('name_label')
                ->virtualAs("IFNULL(CONCAT(name, ' (', details, ')'), name)")
                ->after('details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_pay_types', function (Blueprint $table) {
            $table->dropColumn('name_label');
        });
    }
};
