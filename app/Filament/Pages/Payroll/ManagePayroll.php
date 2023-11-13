<?php

namespace App\Filament\Pages\Payroll;

use App\Models\Payroll\PayType;
use App\Settings\PayrollSettings;
use App\Traits\Payroll\HasSidebarPayrollSettingsTrait;
use AymanAlhattami\FilamentPageWithSidebar\Traits\HasPageSidebar;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManagePayroll extends SettingsPage
{
    use HasPageSidebar;
    use HasSidebarPayrollSettingsTrait;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Payroll';

    protected static string $settings = PayrollSettings::class;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('Manage Payroll Settings');
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->can('Manage Payroll Settings'), 403);
    }

    public function form(Form $form): Form
    {
        $fieldColumns = [
            'sm' => 3,
            '2xl' => 4,
        ];

        return $form
            ->schema([
                Fieldset::make('Pay Period Setup')
                    ->columns($fieldColumns)
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
                    ]),
                Fieldset::make('Timeclock Overtime Calculations')
                    ->columns($fieldColumns)
                    ->schema([
                        TextInput::make('hours_before_overtime')
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
                    ]),
                Fieldset::make('Additions and Deductions')
                    ->columns(2)
                    ->schema([
                        KeyValue::make('timeclock_additions')
                            ->addActionLabel('New Addition')
                            ->keyLabel('Description')
                            ->valueLabel('Minutes to Add'),
                        KeyValue::make('timeclock_deductions')
                            ->addActionLabel('New Deduction')
                            ->keyLabel('Description')
                            ->valueLabel('Minutes to Deduct'),
                    ]),

            ]);
    }
}
