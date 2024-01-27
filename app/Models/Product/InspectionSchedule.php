<?php

namespace App\Models\Product;

use App\Models\Timeclock\TimeclockEntry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class InspectionSchedule extends Pivot
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'product_inspection_schedules';

    public function status(): BelongsTo 
    {
        return $this->belongsTo(InspectionStatus::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(InspectionProcedure::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function timeclockEntries(): MorphMany 
    {
        return $this->morphMany(TimeclockEntry::class, 'clockable');
    }
}
