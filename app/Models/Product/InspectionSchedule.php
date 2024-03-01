<?php

namespace App\Models\Product;

use App\Models\Timeclock\TimeclockEntry;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InspectionSchedule extends Pivot
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'product_inspection_schedules';

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspections::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(InspectionProcedure::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(InspectionQuestion::class, 'schedule_id');
    }

    public function timeclockEntries(): MorphMany 
    {
        return $this->morphMany(TimeclockEntry::class, 'clockable');
    }
}
