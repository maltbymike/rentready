<?php

namespace App\Filament\Resources\Products\InspectionsResource\Pages;

use App\Filament\Resources\Products\InspectionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInspections extends ListRecords
{
    protected static string $resource = InspectionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
