<?php

namespace App\Traits\Payroll;

use App\Models\Payroll\Batch;
use App\Models\Payroll\BatchUser;
use App\Models\Payroll\PayType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

trait AttachDefaultPayTypesToBatchUserTrait
{
    protected static function addUsersToPayrollBatch(Batch $batch, array $users, bool $addOnly = true): void
    {

        $resultOfSync = $addOnly == true
            ? $batch->users()->syncWithoutDetaching($users)
            : $batch->users()->sync($users);

        $batch = static::attachDefaultPayTypesToAllUsers($batch, $resultOfSync['attached']);

    }

    protected static function attachDefaultPayTypesToAllUsers(Batch $batch, array $newlyAddedUsers = null): void
    {

        $batch->load('users.defaultPayTypes');

        if ($newlyAddedUsers) {

            $attachDefaultPayTypesToUsers = $batch->users->map(fn (User $user) => in_array($user->id, $newlyAddedUsers) ? $user : null
            )->filter();

            $attachDefaultPayTypesToUsers->each(function (User $user) use ($batch) {
                $batchUser = BatchUser::find($user->pivot->id);
                static::attachDefaultPayTypesToBatchUser($batchUser, $batch->payment_date);
            });

        }

    }

    protected static function attachDefaultPayTypesToBatchUser(BatchUser $batchUser, Carbon $payrollDate): void
    {

        $paytypes = static::getCurrentPayTypes($batchUser->user->defaultPayTypes, $payrollDate)
            ->mapWithKeys(function (PayType $defaultPayType) {
                return [$defaultPayType->id => ['value' => $defaultPayType->pivot->default_value]];
            });

        $batchUser->payTypes()->attach($paytypes->toArray());

    }

    protected static function getCurrentPayTypes(Collection $payTypes, Carbon $payrollDate)
    {

        $payTypes = $payTypes->sortBy('pivot.effective_date');

        return $payTypes->map(function (PayType $payType) use ($payrollDate) {
            return $payType->pivot->effective_date <= $payrollDate ? $payType : null;
        })->filter();

    }
}
