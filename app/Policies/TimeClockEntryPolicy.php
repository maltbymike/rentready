<?php

namespace App\Policies;

use App\Models\TimeClockEntry;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TimeClockEntryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('Manage Timeclock Entries') || $user->hasRole('Timeclock User');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TimeClockEntry $timeClockEntry): bool
    {
        return $user->can('Manage Timeclock Entries');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('Manage Timeclock Entries');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TimeClockEntry $timeClockEntry): bool
    {
        return $user->can('Manage Timeclock Entries');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TimeClockEntry $timeClockEntry): bool
    {
        return $user->can('Manage Timeclock Entries');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TimeClockEntry $timeClockEntry): bool
    {
        return $user->can('Manage Timeclock Entries');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TimeClockEntry $timeClockEntry): bool
    {
        return $user->can('Manage Timeclock Entries');
    }
}
