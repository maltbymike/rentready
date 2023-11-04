<?php

namespace App\Filament\Resources\Payroll\PayTypeResource\Pages;

use App\Filament\Resources\Payroll\PayTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePayTypes extends ManageRecords
{
    protected static string $resource = PayTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
