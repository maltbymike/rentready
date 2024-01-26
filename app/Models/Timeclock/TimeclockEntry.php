<?php

namespace App\Models\Timeclock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TimeclockEntry extends Model
{
    use HasFactory;

    public function clockable(): MorphTo 
    {
        return $this->morphTo();
    }
}
