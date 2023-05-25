<?php

namespace App\Filament\Resources\TimeClockEntryResource\Pages;

use App\Filament\Resources\TimeClockEntryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTimeClockEntry extends CreateRecord
{
    protected static string $resource = TimeClockEntryResource::class;
}
