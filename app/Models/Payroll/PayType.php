<?php

namespace App\Models\Payroll;

use App\Enums\Payroll\PayTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayType extends Model
{
    use HasFactory;

    protected $table = 'payroll_pay_types';

    protected $fillable = [
        'name',
        'type',
        'is_used_for_stat_pay',
    ];

    protected $casts = [
        'type' => PayTypeEnum::class,
    ];

    public function getForeignKey()
    {
        return 'payroll_pay_type_id';
    }
}
