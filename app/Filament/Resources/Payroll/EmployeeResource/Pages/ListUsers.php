<?php

namespace App\Filament\Resources\Payroll\EmployeeResource\Pages;

use App\Filament\Resources\Payroll\EmployeeResource;
use App\Models\User;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    use HasPageSidebar;

    public ?User $record = null;

    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
