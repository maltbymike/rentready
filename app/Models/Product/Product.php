<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function children(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function inspections(): HasManyThrough
    {
        return $this->hasManyThrough(Inspections::class, InspectionSchedule::class, 'product_id', 'schedule_id');
    }

    public function inspectionSchedules(): HasMany
    {
        return $this->hasMany(InspectionSchedule::class)
            ->with('procedure');
    }

    public function inspectionProcedures(): BelongsToMany
    {
        return $this->belongsToMany(InspectionProcedure::class, 'product_inspection_schedules', 'product_id', 'procedure_id');
    }
}
