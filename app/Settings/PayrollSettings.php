<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PayrollSettings extends Settings
{
    public int $hours_before_overtime;
    public string $period_ends_on_day;
    public string $period_paid_on_day;

    public static function group(): string
    {
        return 'payroll';
    }
}