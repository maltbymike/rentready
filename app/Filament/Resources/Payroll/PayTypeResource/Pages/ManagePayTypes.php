<?php

namespace App\Filament\Resources\Payroll\PayTypeResource\Pages;

use App\Filament\Resources\Payroll\PayTypeResource;
use App\Models\Payroll\PayType;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePayTypes extends ManageRecords
{
    use HasPageSidebar;

    public ?PayType $record = null;

    protected static string $resource = PayTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
