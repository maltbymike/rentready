<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payroll.period_ends_on_day', 'Saturday');
        $this->migrator->add('payroll.period_paid_on_day', 'Thursday');
        $this->migrator->add('payroll.hours_before_overtime', 44);
        $this->migrator->add('payroll.regular_hours_pay_type', 1);
        $this->migrator->add('payroll.overtime_hours_pay_type', 2);
        $this->migrator->add('payroll.timeclock_additions', ['No Break' => 10]);
        $this->migrator->add('payroll.timeclock_deductions', ['Lunch' => 45]);
    }
};
