<?php

use App\Enums\Payroll\PayTypeEnum;
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
            $table->string('type');
            $table->boolean('is_used_for_stat_pay')->default(false);
            $table->timestamps();
        });

        $types = collect([
            [
                'name' => 'Regular Hours',
                'type' => PayTypeEnum::Earning,
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Overtime Hours',
                'type' => PayTypeEnum::Earning,
            ],
            [
                'name' => 'Public Holiday Hours',
                'type' => PayTypeEnum::Earning,
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Personal Hours',
                'type' => PayTypeEnum::Benefit,
            ],
            [
                'name' => 'Vacation Pay (Hours)',
                'type' => PayTypeEnum::Earning,
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Vacation Pay (Dollars)',
                'type' => PayTypeEnum::Earning,
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Bonus',
                'type' => PayTypeEnum::Earning,
            ],
            [
                'name' => 'Advance',
                'type' => PayTypeEnum::Benefit,
            ],
            [
                'name' => 'Advance Repayment',
                'type' => PayTypeEnum::Deduction,
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
