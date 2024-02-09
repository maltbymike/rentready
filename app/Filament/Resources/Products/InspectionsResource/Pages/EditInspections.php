<?php

namespace App\Filament\Resources\Products\InspectionsResource\Pages;

use App\Filament\Resources\Products\InspectionsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInspections extends EditRecord
{
    protected static string $resource = InspectionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
