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
    ];

    protected $casts = [
        'clock_in_at' => 'datetime:Y-m-d H:i:s',
        'clock_out_at' => 'datetime:Y-m-d H:i:s',
        'clock_in_requested' => 'datetime:Y-m-d H:i:s',
        'clock_out_requested' => 'datetime:Y-m-d H:i:s',
        'clock_in_approved' => 'datetime:Y-m-d H:i:s',
        'clock_out_approved' => 'datetime:Y-m-d H:i:s',
        'payment_date' => 'date:Y-m-d H:i:s',
    ];

    protected $fillable = [
        'user_id',
        'payroll_batch_user_id',
        'clock_in_at',
        'clock_out_at',
        'clock_in_requested',
        'clock_out_requested',
        'clock_in_approved',
        'clock_out_approved',
        'approved_by',
        'approved_at',
    ];

    // protected function clockInApproved(): Attribute {
    //     return Attribute::make(
    //         get: fn(Mixed $value)
    //             => $this->getTimestampOrFallback($value, $this->clock_in_requested),
    //         set: fn (Mixed $value, Array $attributes)
    //             => $this->setTimestampOrFallback($value, $attributes['clock_in_at'])
    //     );
    // }

    // protected function clockOutApproved(): Attribute {
    //     return Attribute::make(
    //         get: fn (Mixed $value) 
    //             => $this->getTimestampOrFallback($value, $this->clock_out_requested),
    //         set: fn (Mixed $value, Array $attributes) 
    //             => $this->setTimestampOrFallback($value, $attributes['clock_out_at'])
    //     );
    // }

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

    public static function getClockedHoursAsRawSqlString(): String {
        return 'cast(TIMESTAMPDIFF(SECOND, clock_in_at, clock_out_at) / (60 * 60) AS DECIMAL(10, 2)) AS hours_clocked';
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


    public function hasClockInChangeRequest(): bool
    {
        return $this->clock_in_at != $this->clock_in_requested;
    }

    public function hasClockOutChangeRequest(): bool
    {
        return $this->clock_out_at != $this->clock_out_requested;
    }

    public function hours(): string
    {
        $clock_out_at = $this->clock_out_at ?? $this->freshTimestamp();
        return round($clock_out_at->floatDiffInHours($this->clock_in_at), 2);
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
