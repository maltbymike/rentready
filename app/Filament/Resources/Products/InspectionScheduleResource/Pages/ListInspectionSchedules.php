<?php

namespace App\Filament\Resources\Products\InspectionScheduleResource\Pages;

use App\Filament\Resources\Products\InspectionScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInspectionSchedules extends ListRecords
{
    protected static string $resource = InspectionScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
