<?php

namespace App\Models\Payroll;

use App\Models\User;
use App\Models\TimeClockEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

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

    public function timeClockEntriesWithUnassigned(): HasMany|Null
    {

        if (! $this->payroll_batch_id) { 
            return null;
        }

        $period_ending = Batch::find($this->payroll_batch_id)->period_ending;

        return User::find($this->user_id)->HasMany(TimeClockEntry::class)
            ->where(function (Builder $query) {
                $query->where('payroll_batch_user_id', $this->id)
                    ->orWhereNull('payroll_batch_user_id');
            })
            ->where('clock_out_at', '!=', NULL)
            ->where('clock_out_at', '<=', $period_ending->addDay());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}