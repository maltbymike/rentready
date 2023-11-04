<?php
  
namespace App\Enums\Payroll;

use Filament\Support\Contracts\HasLabel;
 
enum PayTypeEnum: string implements HasLabel {
    case CalculatedHour = 'calculated_hour';
    case Hour = 'hour';
    case Dollar = 'dollar';
    case Deduction = 'deduction';
    case Accrual = 'accrual';

    public function getLabel(): ?string {
        return match ($this) {
            self::CalculatedHour => 'Calculated Hours',
            self::Hour => 'Hours',
            self::Dollar => 'Dollars',
            self::Deduction => 'Deductions',
            self::Accrual => 'Accruals',
        };
    }
}