<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayType extends Model
{
    use HasFactory;

    protected $table = 'payroll_pay_types';

    protected $fillable = [
        'name',
        'is_used_for_stat_pay',
    ];

    public function getForeignKey()
    {
        return 'payroll_pay_type_id';
    }
}
