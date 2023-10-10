<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('payroll.period_ends_on_day', 'Saturday');
        $this->migrator->add('payroll.period_paid_on_day', 'Thursday');
        $this->migrator->add('payroll.hours_before_overtime', 40);
    }
};
