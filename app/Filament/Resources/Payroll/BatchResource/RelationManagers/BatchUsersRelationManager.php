<?php

namespace App\Filament\Resources\Payroll\BatchResource\RelationManagers;

use App\Traits\Payroll\SyncTimeClockEntriesToBatchUserTrait;
use Closure;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\TimeClockEntry;
use App\Models\Payroll\PayType;
use App\Models\Payroll\BatchUser;
use App\Enums\Payroll\PayTypeEnum;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Traits\Payroll\SyncPayTypesToBatchUserTrait;
use Filament\Resources\RelationManagers\RelationManager;

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
                            Forms\Components\Repeater::make('unassignedTimeClockEntries')
                                ->relationship()
                                ->columns(6)
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
                                ->schema($timeClockSchema),
                            Forms\Components\Repeater::make('timeClockEntries')
                                ->relationship()
                                ->columns(6)
                                ->deletable(false)
                                ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                                    $clock_in_at = Carbon::parse($data['clock_in_at']);
                                    $clock_out_at = Carbon::parse($data['clock_out_at']);
                                    $data['clocked_hours'] = round($clock_out_at->floatDiffInHours($clock_in_at), 2);

                                    return $data;
                                })
                                ->schema($timeClockSchema),
                        ]),
                    Forms\Components\Section::make('Earnings')
                        ->extraAttributes(['class' => 'items-end-grid'])
                        ->columns(6)
                        ->hiddenOn('create')
                        ->collapsible()
                        ->schema($earnings),
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
