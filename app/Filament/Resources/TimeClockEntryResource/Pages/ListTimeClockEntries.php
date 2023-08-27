<?php

namespace App\Filament\Resources\TimeClockEntryResource\Pages;

use Filament\Pages\Actions;
use App\Models\TimeClockEntry;
use Filament\Tables\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TimeClockEntryResource;

class ListTimeClockEntries extends ListRecords
{
    protected static string $resource = TimeClockEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Time Clock Entry'),
        ];
    }

}
