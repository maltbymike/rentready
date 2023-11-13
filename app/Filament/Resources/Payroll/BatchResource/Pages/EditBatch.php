<?php

namespace App\Filament\Resources\Payroll\BatchResource\Pages;

use App\Filament\Resources\Payroll\BatchResource;
use App\Traits\Payroll\SyncPayTypesToBatchUserTrait;
use Filament\Resources\Pages\EditRecord;

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

    public function refreshForm()
    {
        $this->fillForm();
    }
}
