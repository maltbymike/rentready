<?php

namespace App\Filament\Resources\Payroll\UserResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Payroll\UserResource;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;

class ListUsers extends ListRecords
{
    use HasPageSidebar;

    public ?User $record = NULL;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
