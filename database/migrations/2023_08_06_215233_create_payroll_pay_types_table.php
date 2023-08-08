<?php

use App\Models\Payroll\PayType;
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
        Schema::create('payroll_pay_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_used_for_stat_pay')->default(false);
            $table->timestamps();
        });

        $types = collect([
            [
                'name' => 'Regular Hours',
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Overtime Hours',
            ],
            [
                'name' => 'Public Holiday Hours',
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Personal Hours',
            ],
            [
                'name' => 'Vacation Pay (Hours)',
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Vacation Pay (Dollars)',
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Bonus',
            ],
            [
                'name' => 'Advance',
            ],
            [
                'name' => 'Advance Repayment',
            ],
        ]);

        $types->each(function (array $type) {
            PayType::create($type);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_pay_types');
    }
};
