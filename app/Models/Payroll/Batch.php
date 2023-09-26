<?php

namespace App\Models\Payroll;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Batch extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'payroll_batches';

    protected $casts = [
        'period_ending' => 'datetime:Y-m-d',
        'payment_date' => 'datetime:Y-m-d',
        'approved_at' => 'datetime:Y-m-d',
    ];

    protected $fillable = [
        'period_ending',
        'payment_date',
    ];

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function batchUsers(): HasMany
    {
        return $this->hasMany(BatchUser::class);
    }

    public function getLastPayrollEndingDate(): Carbon
    {
        return 
            Batch::select('period_ending')
                ->orderBy('period_ending')
                ->limit(1)
                ->get()
                ->pluck('period_ending')
                ->first()
            ?? Carbon::parse('last Saturday');
    }

    public function getNextPayrollEndingDate(): Carbon
    {
        return $this->getLastPayrollEndingDate()->next('Saturday');
    }

    public function getNextPayrollPaymentDate(): Carbon
    {
        return $this->getNextPayrollEndingDate()->next('Monday');
    }

    public function getForeignKey()
    {
        return 'payroll_batch_id';
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'payroll_batch_user', 'payroll_batch_id', 'user_id');
    }
}
