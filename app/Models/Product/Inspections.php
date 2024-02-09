<?php

namespace App\Models\Product;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Znck\Eloquent\Relations\BelongsToThrough;

class Inspections extends Model
{
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $guarded = [];

    protected $table = 'product_inspections';

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function procedure(): BelongsToThrough
    {
        return $this->belongsToThrough(
            InspectionProcedure::class,
            InspectionSchedule::class,
            foreignKeyLookup: [
                InspectionProcedure::class => 'procedure_id',
                InspectionSchedule::class => 'schedule_id',
            ]
        );
    }

    public function product(): BelongsToThrough
    {
        return $this->belongsToThrough(
            Product::class,
            InspectionSchedule::class,
            foreignKeyLookup: [
                Product::class => 'product_id',
                InspectionSchedule::class => 'schedule_id',
            ]
        );
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(InspectionSchedule::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(InspectionStatus::class);
    }
}
