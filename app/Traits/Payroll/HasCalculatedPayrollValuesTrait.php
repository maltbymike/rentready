<?php

namespace App\Traits\Payroll;
use App\Settings\PayrollSettings;

trait HasCalculatedPayrollValuesTrait {

    protected static function calculateRegularAndOvertimeHours(float $hours, float $hoursBeforeOvertime): array {
        
        return $hours <= $hoursBeforeOvertime
            ? ['regular' => $hours, 'overtime' => 0]
            : ['regular' => $hoursBeforeOvertime, 'overtime' => $hours - $hoursBeforeOvertime];
    
    }

}