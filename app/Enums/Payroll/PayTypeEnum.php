<?php
  
namespace App\Enums\Payroll;
 
enum PayTypeEnum:string {
    case CalculatedHour = 'calculated_hour';
    case Hour = 'hour';
    case Dollar = 'dollar';
    case Deduction = 'deduction';
    case Accrual = 'accrual';
}