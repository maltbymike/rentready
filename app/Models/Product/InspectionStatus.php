<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionStatus extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'product_inspection_statuses';

    public function inspectionSchedule(): HasMany 
    {
        return $this->hasMany(InspectionSchedule::class);
    }
}
