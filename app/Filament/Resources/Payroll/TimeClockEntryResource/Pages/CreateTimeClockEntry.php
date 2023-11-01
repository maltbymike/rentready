<?php

namespace App\Filament\Resources\Payroll\TimeClockEntryResource\Pages;

use App\Filament\Resources\Payroll\TimeClockEntryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTimeClockEntry extends CreateRecord
{
    protected static string $resource = TimeClockEntryResource::class;
}
