<?php

namespace App\Filament\Resources\Payroll\BatchResource\Pages;

use App\Filament\Resources\Payroll\BatchResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBatch extends EditRecord
{
    protected static string $resource = BatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
