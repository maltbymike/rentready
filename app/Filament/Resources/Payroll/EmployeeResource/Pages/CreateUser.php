<?php

namespace App\Filament\Resources\Payroll\EmployeeResource\Pages;

use App\Filament\Resources\Payroll\EmployeeResource;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    use HasPageSidebar;

    protected static string $resource = EmployeeResource::class;
}
