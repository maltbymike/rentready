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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
