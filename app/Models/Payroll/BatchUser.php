<?php

namespace App\Models\Payroll;

use App\Models\TimeClockEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BatchUser extends Pivot
{
    use HasFactory;

    protected $table = 'payroll_batch_user';

    public $timestamps = false;

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function batchUserPayTypes(): HasMany
    {
        return $this->hasMany(BatchUserPayType::class);
    }

    public function getForeignKey()
    {
        return 'payroll_batch_user_id';
    }

 
    public function payrollBatch(): BelongsTo
    {
        return $this->belongsTo(Batch::class, 'payroll_batch_id');
    }

    public function payTypes(): BelongsToMany
    {
        return $this->belongsToMany(PayType::class, 'payroll_batch_user_pay_type')
            ->withPivot('value');
    }

    public function timeClockEntries(): HasMany
    {
        return $this->hasMany(TimeClockEntry::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}