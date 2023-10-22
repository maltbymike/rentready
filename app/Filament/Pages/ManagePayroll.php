<?php

namespace App\Filament\Pages;

use App\Models\Payroll\PayType;
use App\Settings\PayrollSettings;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManagePayroll extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Payroll';

    protected static string $settings = PayrollSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('period_ends_on_day')
                    ->label('Payroll Period Ends On')
                    ->options([
                        'Sunday' => 'Sunday',
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                    ])
                    ->required(),
                Select::make('period_paid_on_day')
                    ->label('Payroll Period Paid On')
                    ->options([
                        'Sunday' => 'Sunday',
                        'Monday' => 'Monday',
                        'Tuesday' => 'Tuesday',
                        'Wednesday' => 'Wednesday',
                        'Thursday' => 'Thursday',
                        'Friday' => 'Friday',
                        'Saturday' => 'Saturday',
                    ])
                    ->required(),
                TextInput::make('hours_before_overtime')
                    ->columnStart(1)
                    ->label('Hours Before Overtime Applies')
                    ->numeric()
                    ->required(),
                Select::make('regular_hours_pay_type')
                    ->label('Regular Hours Pay Type')
                    ->options(PayType::all()->pluck('name', 'id'))
                    ->required(),
                Select::make('overtime_hours_pay_type')
                    ->label('Overtime Hours Pay Type')
                    ->options(PayType::all()->pluck('name', 'id'))
                    ->required(),
            ])
            ->columns([
                'sm' => 3,
                '2xl' => 5,
            ]);
    }
}
