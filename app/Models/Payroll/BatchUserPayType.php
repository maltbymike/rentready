<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BatchUserPayType extends Model
{
    use HasFactory;

    protected $table = 'payroll_batch_user_pay_type';

    public function payTypes(): BelongsTo
    {
        return $this->belongsTo(PayType::class);
    }

}
