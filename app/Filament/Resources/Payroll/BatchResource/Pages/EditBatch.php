<?php

namespace App\Filament\Resources\Payroll\BatchResource\Pages;

use Filament\Pages\Actions;
use App\Models\Payroll\Batch;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Payroll\BatchResource;
use App\Traits\Payroll\SyncPayTypesToBatchUserTrait;

class EditBatch extends EditRecord
{
    use SyncPayTypesToBatchUserTrait;
    protected static string $resource = BatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    // protected function handleRecordUpdate(Model $record, array $data): Model
    // {
    //     dd ($record, $data);
    //     $record->update($data);

    //     $record->users()->sync(array_keys($data['user']));

    //     foreach ($record->batchUsers as $batchUser) {
    //         $this->syncPayTypes($batchUser, $data['user'][$batchUser->user_id]['payTypes']);
    //     }
    
    //     return $record;
    // }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $batch = Batch::where('id', $data['id'])->with('batchUsers.payTypes')->get()->first();

        foreach ($batch->batchUsers as $userKey => $batchUser) {
            foreach ($batchUser->payTypes as $payType) {
                $data['user'][$batchUser->user_id]['payTypes'][$payType->id] = $payType->pivot->value;
            }
        }

        return $data;
    }
}
