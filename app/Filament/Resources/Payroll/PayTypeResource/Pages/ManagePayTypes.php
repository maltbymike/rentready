<?php

namespace App\Filament\Resources\Payroll\PayTypeResource\Pages;

use App\Models\Payroll\PayType;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Payroll\PayTypeResource;
use App\Traits\Payroll\HasSidebarPayrollSettingsTrait;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;

class ManagePayTypes extends ManageRecords
{
    use HasPageSidebar;
    
    public ?PayType $record = NULL;

    protected static string $resource = PayTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
