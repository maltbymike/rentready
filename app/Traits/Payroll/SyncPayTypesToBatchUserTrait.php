<?php

namespace App\Traits\Payroll;

use App\Models\Payroll\BatchUser;

trait SyncPayTypesToBatchUserTrait
{
    protected function syncPayTypes (BatchUser $batchUser, array $payTypes): BatchUser 
    {
        $payTypes = collect($payTypes)
                    ->filter()
                    ->map(function ($value, $key) {
                        return ['value' => $value];
                    })
                    ->toArray();
        $batchUser->payTypes()->sync($payTypes);
        return $batchUser;
    }
}