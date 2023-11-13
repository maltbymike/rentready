<?php

namespace App\Traits\Payroll;

use App\Filament\Pages\Payroll\ManagePayroll;
use App\Filament\Resources\Payroll\EmployeeResource\Pages\ListUsers;
use App\Filament\Resources\Payroll\PayTypeResource\Pages\ManagePayTypes;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;

trait HasSidebarPayrollSettingsTrait
{
    public static function sidebar(): FilamentPageSidebar
    {
        return FilamentPageSidebar::make()
            ->topbarNavigation()
            ->setNavigationItems([
                PageNavigationItem::make('Payroll Setup')
                    ->translateLabel()
                    ->url(ManagePayroll::getUrl()),
                PageNavigationItem::make('Pay Types')
                    ->translateLabel()
                    ->url(ManagePayTypes::getUrl()),
                PageNavigationItem::make('Employee Setup')
                    ->translateLabel()
                    ->url(ListUsers::getUrl()),
            ]);
    }
}
