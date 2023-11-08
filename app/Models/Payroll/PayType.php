<?php

namespace App\Models\Payroll;

use App\Models\User;
use App\Enums\Payroll\PayTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PayType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'payroll_pay_types';

    protected $fillable = [
        'name',
        'details',
        'type',
        'is_used_for_stat_pay',
    ];

    protected $casts = [
        'type' => PayTypeEnum::class,
    ];

    public function getForeignKey() {
        return 'payroll_pay_type_id';
    }

    // public function getNameLabelAttribute(): String {
    //     return $this->details
    //     ? "$this->name ($this->details)" 
    //     : $this->name;;
    // }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'payroll_pay_type_user')
            ->withPivot('id', 'default_value', 'effective_date');
    }
}
