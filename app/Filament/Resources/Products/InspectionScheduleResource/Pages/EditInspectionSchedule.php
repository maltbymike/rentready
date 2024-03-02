<?php

namespace App\Filament\Resources\Products\InspectionScheduleResource\Pages;

use App\Filament\Resources\Products\InspectionScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInspectionSchedule extends EditRecord
{
    protected static string $resource = InspectionScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
