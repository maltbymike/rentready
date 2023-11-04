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
            $table->string('details')->nullable();
            $table->string('type');
            $table->boolean('is_used_for_stat_pay')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        $types = collect([
            [
                'name' => 'Regular Hours',
                'details' => 'Calculated',
                'type' => PayTypeEnum::CalculatedHour,
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Overtime Hours',
                'details' => 'Calculated',
                'type' => PayTypeEnum::CalculatedHour,
            ],
            [
                'name' => 'Regular Hours',
                'details' => 'Additional',
                'type' => PayTypeEnum::Hour,
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Overtime Hours',
                'details' => 'Additional',
                'type' => PayTypeEnum::Hour,
            ],
            [
                'name' => 'Public Holiday Hours',
                'type' => PayTypeEnum::Hour,
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Personal Hours',
                'type' => PayTypeEnum::Hour,
            ],
            [
                'name' => 'Vacation Pay',
                'details' => 'Hours',
                'type' => PayTypeEnum::Hour,
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Vacation Pay',
                'details' => 'Dollars',
                'type' => PayTypeEnum::Dollar,
                'is_used_for_stat_pay' => true,
            ],
            [
                'name' => 'Bonus',
                'type' => PayTypeEnum::Dollar,
            ],
            [
                'name' => 'Advance',
                'type' => PayTypeEnum::Dollar,
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
