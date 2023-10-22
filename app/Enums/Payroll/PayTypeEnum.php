<?php
  
namespace App\Enums\Payroll;
 
enum PayTypeEnum:string {
    case Hour = 'hour';
    case Dollar = 'dollar';
    case Deduction = 'deduction';
    case Accrual = 'accrual';
}