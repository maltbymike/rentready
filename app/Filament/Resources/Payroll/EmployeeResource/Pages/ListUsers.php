<?php

namespace App\Filament\Resources\Payroll\EmployeeResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Payroll\EmployeeResource;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;

class ListUsers extends ListRecords
{
    use HasPageSidebar;

    public ?User $record = NULL;

    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
