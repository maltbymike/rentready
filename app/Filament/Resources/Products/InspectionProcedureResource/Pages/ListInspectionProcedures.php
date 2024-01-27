<?php

namespace App\Filament\Resources\Products\InspectionProcedureResource\Pages;

use App\Filament\Resources\Products\InspectionProcedureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInspectionProcedures extends ListRecords
{
    protected static string $resource = InspectionProcedureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
