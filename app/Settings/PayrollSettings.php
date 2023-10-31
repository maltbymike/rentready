<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PayrollSettings extends Settings
{
    public int $hours_before_overtime;
    public string $period_ends_on_day;
    public string $period_paid_on_day;
    public int $regular_hours_pay_type;
    public int $overtime_hours_pay_type;
    public array $timeclock_additions;
    public array $timeclock_deductions;

    public static function group(): string
    {
        return 'payroll';
    }
}