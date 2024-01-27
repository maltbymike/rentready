<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Inspections extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'product_inspections';

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(InspectionSchedule::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(InspectionStatus::class);
    }
}
