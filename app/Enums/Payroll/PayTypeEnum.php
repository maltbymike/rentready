<?php
  
namespace App\Enums\Payroll;
 
enum PayTypeEnum:string {
    case Earning = 'earning';
    case Benefit = 'benefit';
    case Deduction = 'deduction';
    case Accrual = 'accrual';
}