<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchUserPayType extends Model
{
    use HasFactory;

    protected $table = 'payroll_batch_user_pay_type';

    protected $fillable = [
        'payroll_pay_type_id',
        'value',
    ];

    public function payTypes(): BelongsTo
    {
        return $this->belongsTo(PayType::class);
    }
}
