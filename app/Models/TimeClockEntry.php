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
        'payment_date' => 'date:Y-m-d H:i:s',
    ];

    protected $fillable = [
        'user_id',
        'clock_in_at',
        'clock_out_at',
        'approved_by',
        'approved_at',
    ];

    public ?Array $statuses;

    public function alternates(): BelongsToMany
    {
        return $this->belongsToMany(TimeClockEntry::class, 'time_clock_entry_alternates', 'time_clock_entry_id', 'alternate_id');
    }

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

    public function setEntryStatus(String $statusName, bool $save = true): void
    {
        $this->statuses[$statusName] = $this->statuses[$statusName] ?? TimeClockStatus::firstWhere('name', $statusName);
        $this->status()->associate($this->statuses[$statusName]);

        if ($save) {
            $this->save();
        }
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TimeClockStatus::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
