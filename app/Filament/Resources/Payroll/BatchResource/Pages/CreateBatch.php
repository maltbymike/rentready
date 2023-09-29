<?php

namespace App\Filament\Resources\Payroll\BatchResource\Pages;

use App\Models\Payroll\BatchUser;
use Filament\Pages\Actions;
use App\Models\Payroll\Batch;
use App\Traits\Payroll\SyncPayTypesToBatchUserTrait;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Payroll\BatchResource;

class CreateBatch extends CreateRecord
{
    use SyncPayTypesToBatchUserTrait;
    protected static string $resource = BatchResource::class;

    // protected function handleRecordCreation(array $data): Model
    // {
    //     $batch = static::getModel()::create($data);
        
    //     $batch->users()->attach(array_keys($data['user']));

    //     foreach ($batch->batchUsers as $batchUser) {
    //         $this->syncPayTypes($batchUser, $data['user'][$batchUser->user_id]['payTypes']);
    //     }

    //     return $batch;
    // }

}
