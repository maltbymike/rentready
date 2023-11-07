<?php

namespace App\Filament\Resources\Payroll\UserResource\Pages;

use App\Models\User;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Payroll\UserResource;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;

class EditUser extends EditRecord
{
    use HasPageSidebar;
    
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
