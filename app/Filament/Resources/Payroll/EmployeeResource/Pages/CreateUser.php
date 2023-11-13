<?php

namespace App\Filament\Resources\Payroll\EmployeeResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Payroll\EmployeeResource;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;

class CreateUser extends CreateRecord
{   
    use HasPageSidebar;
    
    protected static string $resource = EmployeeResource::class;
}
