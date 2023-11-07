<?php

namespace App\Traits\Payroll;

use App\Filament\Pages\Payroll\ManagePayroll;
use App\Filament\Resources\Payroll\PayTypeResource\Pages\ManagePayTypes;
use App\Filament\Resources\Payroll\UserResource\Pages\ListUsers;
use AymanAlhattami\FilamentPageWithSidebar\PageNavigationItem;
use AymanAlhattami\FilamentPageWithSidebar\FilamentPageSidebar;

trait HasSidebarPayrollSettingsTrait {
    public static function sidebar(): FilamentPageSidebar {
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