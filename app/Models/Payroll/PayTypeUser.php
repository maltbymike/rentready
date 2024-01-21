<?php

namespace App\Models\Payroll;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayTypeUser extends Model
{
    use HasFactory;

    protected $casts = [
        'effective_date' => 'datetime:Y-m-d',
    ];
}
