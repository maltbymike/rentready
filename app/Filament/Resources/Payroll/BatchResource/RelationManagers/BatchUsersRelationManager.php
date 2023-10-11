<?php

namespace App\Filament\Resources\Payroll\BatchResource\RelationManagers;

use App\Settings\PayrollSettings;
use Closure;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\TimeClockEntry;
use App\Models\Payroll\PayType;
use App\Models\Payroll\BatchUser;
use App\Enums\Payroll\PayTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Traits\Payroll\SyncPayTypesToBatchUserTrait;
use Filament\Resources\RelationManagers\RelationManager;
use App\Traits\Payroll\SyncTimeClockEntriesToBatchUserTrait;

class BatchUsersRelationManager extends RelationManager
{
    use SyncPayTypesToBatchUserTrait;

    protected static string $relationship = 'batchUsers';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        $types = PayType::all();

        $earnings = $types
                        ->where('type', PayTypeEnum::Earning)
                        ->map(function (PayType $type) {
                            return TextInput::make('payTypes.' . $type->id)
                                ->label($type->name)
                                ->hiddenOn('create');
                        })->all();

        $benefits = $types
                        ->where('type', PayTypeEnum::Benefit)
                        ->map(function (PayType $type) {
                            return TextInput::make('payTypes.' . $type->id)
                                ->label($type->name)
                                ->hiddenOn('create');
                        })->all();

        $deductions = $types
                        ->where('type', PayTypeEnum::Deduction)
                        ->map(function (PayType $type) {
                            return TextInput::make('payTypes.' . $type->id)
                                ->label($type->name)
                                ->hiddenOn('create');
                        })->all();

        $timeClockSchema = [
            Forms\Components\Checkbox::make('pay_this_period')
                ->inline(false),
            Forms\Components\DateTimePicker::make('clock_in_at')
                ->readonly()
                ->columnSpan(2),
            Forms\Components\DateTimePicker::make('clock_out_at')
                ->readonly()
                ->columnSpan(2),
            Forms\Components\TextInput::make('clocked_hours')
                ->readonly(),
        ];

        return $form
            ->schema(
                array_merge([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Employee')
                        ->disabledOn('edit'),
                    Forms\Components\Section::make('Timeclock Entries')
                        ->collapsible()
                        ->hiddenOn('create')
                        ->schema([
                            Actions::make([
                                Action::make('Select All')
                                    ->icon('heroicon-m-check')
                                    ->action(function (Get $get, Set $set) {
                                        foreach ($get('unassignedTimeClockEntries') as $key => $value) {
                                            $set('unassignedTimeClockEntries.' . $key . '.pay_this_period', true);
                                        }
                                    }),
                            ]),
                            Forms\Components\Repeater::make('unassignedTimeClockEntries')
                                ->label(false)
                                ->relationship()
                                ->columns(6)
                                ->addable(false)
                                ->deletable(false)
                                ->mutateRelationshipDataBeforeFillUsing(function (array $data, $get): array {
                                    $clock_in_at = Carbon::parse($data['clock_in_at']);
                                    $clock_out_at = Carbon::parse($data['clock_out_at']);
                                    $data['clocked_hours'] = round($clock_out_at->floatDiffInHours($clock_in_at), 2);
                                    
                                    $data['pay_this_period'] = $data['payroll_batch_user_id'] == NULL ? FALSE : TRUE;

                                    return $data;
                                })
                                ->mutateRelationshipDataBeforeSaveUsing(function (TimeClockEntry $record, array $data, $get): array {
                                    $data['payroll_batch_user_id'] = $data['pay_this_period'] == TRUE ? $get('id') : NULL;
                                    return $data;
                                })
                                ->schema([
                                    Forms\Components\Checkbox::make('pay_this_period')
                                        ->live()
                                        ->inline(false),
                                    Forms\Components\DateTimePicker::make('clock_in_at')
                                        ->readonly()
                                        ->columnSpan(2),
                                    Forms\Components\DateTimePicker::make('clock_out_at')
                                        ->readonly()
                                        ->columnSpan(2),
                                    Forms\Components\TextInput::make('clocked_hours')
                                        ->readonly(),
                                ]),
                        ]),
                    Forms\Components\Section::make('Earnings')
                        ->extraAttributes(['class' => 'items-end-grid'])
                        ->columns(6)
                        ->hiddenOn('create')
                        ->collapsible()
                        ->schema(array_merge(
                            [
                                Forms\Components\Placeholder::make('Hours')
                                    ->label('Hours Clocked')
                                    ->extraAttributes([
                                        'class' => 'py-1.5 ps-3 pe-3 rounded-lg ring-1 sm:text-sm sm:leading-6 shadow-sm ring-1 bg-white dark:bg-white/5 ring-gray-950/10 dark:ring-white/20 overflow-hidden',
                                    ])
                                    ->content(function (Get $get, Set $set, PayrollSettings $settings) {

                                        $hours = 0;

                                        foreach ($get('unassignedTimeClockEntries') as $entry) {
                                            if ($entry['pay_this_period'] === true) {
                                                $hours += $entry['clocked_hours'];
                                            }
                                        }

                                        if ($hours <= $settings->hours_before_overtime) {
                                            $regularHours = $hours;
                                            $overtimeHours = 0;
                                        } else {
                                            $regularHours = $settings->hours_before_overtime;
                                            $overtimeHours = $hours - $settings->hours_before_overtime;
                                        }

                                        $set(
                                            'payTypes.' . $settings->regular_hours_pay_type, 
                                            number_format($regularHours, 2)
                                        );
                                        $set(
                                            'payTypes.' . $settings->overtime_hours_pay_type, 
                                            number_format($overtimeHours, 2)
                                        );

                                        return $hours;

                                    }),
                            ],
                            $earnings
                        )),
                    Forms\Components\Section::make('Benefits')
                        ->extraAttributes(['class' => 'items-end-grid'])
                        ->columns(6)
                        ->hiddenOn('create')
                        ->collapsible()
                        ->schema($benefits),
                    Forms\Components\Section::make('Deductions')
                        ->extraAttributes(['class' => 'items-end-grid'])
                        ->columns(6)
                        ->hiddenOn('create')
                        ->collapsible()
                        ->schema($deductions),
                ],
            ));
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('payTypes'))
            ->columns([
                Tables\Columns\TextColumn::make('user_id'),
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('user.email'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (BatchUser $record, array $data): array {
                        foreach ($record->payTypes as $payType) {
                            $data['payTypes'][$payType->id] = $payType->pivot->value;
                        }

                        return $data;
                    })
                    ->using(function (BatchUser $record, array $data): Model {
                        return $this->syncPayTypes($record, $data['payTypes']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->inverseRelationship('payrollBatch');
    }
}
