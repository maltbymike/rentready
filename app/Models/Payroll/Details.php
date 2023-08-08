<?php

namespace App\Models\Payroll;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Details extends Model
{
    use HasFactory;

    protected $table = 'payroll_details';

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'payroll_batch_id');
    }

    public function getForeignKey()
    {
        return 'payroll_detail_id';
    }

    public function payType(): BelongsTo
    {
        return $this->belongsTo(PayType::class, 'payroll_pay_type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
