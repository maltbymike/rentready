<?php

namespace App\Models;

use App\Models\Payroll\BatchUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use SebastianBergmann\Type\VoidType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TimeClockEntry extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

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
        return $this->belongsTo(BatchUser::class);
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
