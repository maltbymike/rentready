<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InspectionProcedure extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'product_inspection_procedures';

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_inspections', 'procedure_id', 'product_id');
    }
}
