<?php

namespace App\Filament\Resources\Payroll\TimeClockEntryResource\Pages;

use App\Filament\Resources\Payroll\TimeClockEntryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimeClockEntry extends EditRecord
{
    protected static string $resource = TimeClockEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
