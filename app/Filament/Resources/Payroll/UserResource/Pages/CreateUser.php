<?php

namespace App\Filament\Resources\Payroll\UserResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Payroll\UserResource;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;

class CreateUser extends CreateRecord
{   
    use HasPageSidebar;
    
    protected static string $resource = UserResource::class;
}
