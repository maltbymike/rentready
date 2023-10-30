<?php

namespace App\Models;

use App\Models\Payroll\Batch;
use Illuminate\Support\Carbon;
use App\Models\Payroll\BatchUser;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use SebastianBergmann\Type\VoidType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Znck\Eloquent\Relations\BelongsToThrough;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class TimeClockEntry extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $appends = [
        'clocked_hours',
        'clocked_or_approved_hours_with_deduction',
    ];

    protected $casts = [
        'clock_in_at' => 'datetime:Y-m-d H:i:s',
        'clock_out_at' => 'datetime:Y-m-d H:i:s',
        'clock_in_requested' => 'datetime:Y-m-d H:i:s',
        'clock_out_requested' => 'datetime:Y-m-d H:i:s',
        'clocked_or_approved_time_in' => 'datetime:Y-m-d H:i:s',
        'clocked_or_approved_time_out' => 'datetime:Y-m-d H:i:s',
        'payment_date' => 'date:Y-m-d H:i:s',
    ];

    protected $fillable = [
        'user_id',
        'payroll_batch_user_id',
        'clock_in_at',
        'clock_out_at',
        'clock_in_requested',
        'clock_out_requested',
        'approve_requested_clock_in',
        'approve_requested_clock_out',
        'minutes_deducted',
        'deduction_reason',
        'minutes_added',
        'addition_reason',
        'approved_by',
        'approved_at',
    ];

    public function batchUser(): BelongsTo {
        return $this->belongsTo(BatchUser::class, 'payroll_batch_user_id');
    }

    protected function clockInRequested(): Attribute {
        return Attribute::make(
            get: fn (Mixed $value) 
                => $this->getTimestampOrFallback($value, $this->clock_in_at),
            set: fn (Mixed $value, Array $attributes) 
                => $this->setTimestampOrFallback($value, $attributes['clock_in_at'])
        );
    }

    protected function clockOutRequested(): Attribute {
        return Attribute::make(
            get: fn (Mixed $value) 
                => $this->getTimestampOrFallback($value, $this->clock_out_at),
            set: fn (Mixed $value, Array $attributes) 
                => $this->setTimestampOrFallback($value, $attributes['clock_out_at'])
        );
    }

    protected function getClockedHoursAttribute(): Float|Null {
        if ($this->clock_out_at) {
            return round($this->clock_out_at->floatDiffInHours($this->clock_in_at), 2);
        }

        return null;
    }

    protected function getClockedOrApprovedHoursWithDeductionAttribute(): Float|Null {
        if ($this->clock_out_at) {
            return round(
                $this->clocked_or_approved_time_out->floatDiffInHours($this->clocked_or_approved_time_in) 
                - ($this->minutes_deducted / 60)
                + ($this->minutes_added / 60)
            , 2);
        }

        return null;
    }

    protected static function getClockedOrApprovedHoursWithDeductionAsRawSqlString(): String {
        return "cast(TIMESTAMPDIFF(SECOND, clocked_or_approved_time_in, clocked_or_approved_time_out) / (60 * 60) - (minutes_deducted / 60) + (minutes_added / 60) AS DECIMAL(10, 2)) AS hours_clocked_with_deduction";
    }

    public static function getClockedHoursAsRawSqlString(Bool $withDeductions = false): String {        
        $deductionString = $withDeductions ? '- (minutes_deducted / 60)' : '';
        return "cast(TIMESTAMPDIFF(SECOND, clock_in_at, clock_out_at) / (60 * 60) $deductionString AS DECIMAL(10, 2)) AS hours_clocked";
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
    }

    protected function getTimestampOrFallback(Mixed $value, ?Carbon $fallback) {
        if ($value) {
            return Carbon::parse($value);
        } else {
            return $fallback;
        }
    }

    public function hasClockInChangeRequest(string $status = null): bool
    {
        switch ($status) {
            case 'approved':
                return $this->approve_requested_clock_in === 1 && $this->clock_in_requested !== null ? true : false;
            case 'rejected':
                return $this->approve_requested_clock_in === 0 && $this->clock_in_requested !== null ? true : false;
            case 'unapproved':
                return $this->approve_requested_clock_in === null && $this->clock_in_requested != $this->clock_in_at ? true : false;
        }

        return $this->clock_in_at != $this->clock_in_requested;
    }

    public function hasClockOutChangeRequest(string $status = null): bool
    {
        switch ($status) {
            case 'approved':
                return $this->approve_requested_clock_out === 1 && $this->clock_out_requested !== null ? true : false;
            case 'rejected':
                return $this->approve_requested_clock_out === 0 && $this->clock_out_requested !== null ? true : false;
            case 'unapproved':
                return $this->approve_requested_clock_out === null && $this->clock_out_requested != $this->clock_out_at ? true : false;
        }

        return $this->clock_out_at != $this->clock_out_requested;
    }

    public function payrollBatch(): BelongsToThrough {
        return $this->belongsToThrough(
            Batch::class, 
            BatchUser::class,
        );
    }

    protected function setTimestampOrFallback(Mixed $value, ?String $fallback) {
        if ($value != Carbon::parse($fallback)) {
            return $value;
        }
        return null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
