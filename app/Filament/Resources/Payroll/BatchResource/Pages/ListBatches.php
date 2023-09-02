<?php

namespace App\Filament\Resources\Payroll\BatchResource\Pages;

use App\Filament\Resources\Payroll\BatchResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBatches extends ListRecords
{
    protected static string $resource = BatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
