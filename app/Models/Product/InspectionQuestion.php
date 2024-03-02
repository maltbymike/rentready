<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionQuestion extends Model
{
    use HasFactory;

    protected $casts = [
        'options' => 'array',
    ];

    protected $guarded = [];

    protected $table = 'product_inspection_schedule_questions';

}
