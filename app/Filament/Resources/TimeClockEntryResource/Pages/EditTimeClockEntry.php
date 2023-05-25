<?php

namespace App\Filament\Resources\TimeClockEntryResource\Pages;

use App\Filament\Resources\TimeClockEntryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTimeClockEntry extends EditRecord
{
    protected static string $resource = TimeClockEntryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
