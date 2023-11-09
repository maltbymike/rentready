<?php

namespace App\Models;

use Filament\Panel;
use App\Models\Payroll\PayType;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use App\Models\Payroll\TimeClockEntry;
use Laravel\Jetstream\HasProfilePhoto;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Models\Contracts\FilamentUser;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'is_admin',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function canAccessPanel(Panel $panel): bool {

        if ($this->hasRole('Administrator')) {
            return true;
        } 
        
        if ($this->can('Access Admin Panel')) {
            return true;
        }

        return false;

    }

    public function clockIn() {

        if (! $this->isClockedIn()) {

            $now = $this->freshTimestamp();

            return $this->timeClockEntries()
                ->create([
                    'clock_in_at' => $now->format('Y-m-d H:i'),
                ]);

        }

    }

    public function clockOut() {
        $now = $this->freshTimestamp();

        return $this->timeClockEntries()
            ->whereNull('clock_out_at')
            ->firstOrFail()
            ->update([
                'clock_out_at' => $now->format('Y-m-d H:i'),
            ]);
    }

    public function defaultPayTypes(): BelongsToMany
    {
        return $this->belongsToMany(PayType::class, 'payroll_pay_type_user')
            ->withPivot('id', 'default_value', 'effective_date');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
    }

    public function isClockedIn() : bool {
        $lastTimeClock = $this->timeClockEntries()->orderBy('clock_in_at', 'desc')->firstOrNew();

        // User has no timeclock entries
        if (! $lastTimeClock->exists) {
            return false;
        }

        // The users last timeclock entry has a clock_out_at value
        if ($lastTimeClock->clock_out_at !== null) {
            return false;
        }

        return true;
    }

    public function payrollBatches(): BelongsToMany
    {
        return $this->belongsToMany(Payroll\Batch::class, 'payroll_batch_user', 'user_id', 'payroll_batch_id');
    }

    public function scopeEmployees(Builder $query): void {
        $query->whereHas('roles', function(Builder $query) {
                return $query->where('name', 'Employee');
            }
        );
    }

    public function scopeTimeclockUsers(Builder $query): void {
        $query->whereHas('roles', function(Builder $query) {
            $query->where('name', 'Timeclock User');
        });
    }

    public function timeClockEntries() : HasMany {
        return $this->hasMany(TimeClockEntry::class);
    }

    public function timeClockEntriesWithUnassigned(): HasMany {
        return $this->hasMany(TimeClockEntry::class)
            ->where('payroll_batch_user_id', NULL);
    }
}
