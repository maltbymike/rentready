<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use SebastianBergmann\Type\VoidType;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
    }

    public function hours(): string
    {
        $clock_out_at = $this->clock_out_at ?? $this->freshTimestamp();
        return round($clock_out_at->floatDiffInHours($this->clock_in_at), 2);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
