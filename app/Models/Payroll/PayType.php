<?php

namespace App\Models\Payroll;

use App\Enums\Payroll\PayTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function getForeignKey() {
        return 'payroll_pay_type_id';
    }

    public function getNameLabelAttribute(): String {
        return $this->details
        ? "$this->name ($this->details)" 
        : $this->name;;
    }
}
