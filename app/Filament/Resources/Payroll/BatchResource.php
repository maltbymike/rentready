<?php

namespace App\Filament\Resources\Payroll;

use App\Traits\Payroll\HasCalculatedPayrollValuesTrait;
use App\Traits\Payroll\SyncPayTypesToBatchUserTrait;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use App\Models\Payroll\Batch;
use App\Models\TimeClockEntry;
use App\Models\Payroll\PayType;
use Filament\Resources\Resource;
use App\Models\Payroll\BatchUser;
use App\Settings\PayrollSettings;
use App\Enums\Payroll\PayTypeEnum;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Payroll\BatchResource\Pages;
use App\Filament\Resources\Payroll\BatchResource\RelationManagers;

class BatchResource extends Resource
{
    use SyncPayTypesToBatchUserTrait;
    use HasCalculatedPayrollValuesTrait;

    protected static ?string $model = Batch::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Payroll Batches';

    protected static ?string $navigationGroup = 'Payroll';

    public static function form(Form $form): Form
    {
        $types = PayType::all();

        $payHours = $types
                        ->where('type', PayTypeEnum::Hour)
                        ->map(function (PayType $type) {
                            return TextInput::make('payTypes.' . $type->id)
                                ->label($type->name)
                                ->hiddenOn('create');
                        })->all();

        $payDollars = $types
                        ->where('type', PayTypeEnum::Dollar)
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

        return $form
            ->schema([
                Grid::make(4)
                    ->schema([
                        DatePicker::make('period_ending')
                            ->label(__('Period Ending'))
                            ->displayFormat('Y-m-d')
                            ->default(fn () => Batch::getNextPayrollEndingDate())
                            ->disabledOn('edit'),
                        DatePicker::make('payment_date')
                            ->label(__('Payment Date'))
                            ->displayFormat('Y-m-d')
                            ->default(fn () => Batch::getNextPayrollPaymentDate())
                            ->disabledOn('edit'),
                        Select::make('approved_by')
                            ->relationship('approvedBy', 'name')
                            ->disabledOn('create'),
                        DatePicker::make('approved_at')
                            ->label(__('Approved At'))
                            ->displayFormat('Y-m-d')
                            ->disabledOn('create'),
                        CheckboxList::make('users')
                            ->label('Employees To Pay')
                            ->bulkToggleable()
                            ->columnSpanFull()
                            ->columns(6)
                            ->relationship(
                                'Users', 
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => 
                                    $query->whereHas('roles', 
                                        function(Builder $query) {
                                            return $query->where('name', 'Employee');
                                        }
                                    )
                            ),
                        Repeater::make('batchUsers')
                            ->columnSpanFull()
                            ->columns(2)
                            ->relationship()
                            ->addable(false)
                            ->deletable(false)
                            ->collapsible()
                            ->mutateRelationshipDataBeforeFillUsing(function (array $data, $record): array {
                                foreach ($record->batchUsers->firstWhere('id', $data['id'])->payTypes as $payType) {
                                    $data['payTypes'][$payType->id] = $payType->pivot->value;
                                }

                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (BatchUser $record, array $data): array {
                                static::syncPayTypes($record, $data['payTypes']);
                                return [];
                            })
                            ->schema([
                                Forms\Components\Section::make('Timeclock Entries')
                                    ->collapsed()
                                    ->hiddenOn('create')
                                    ->columnSpanFull()
                                    ->schema([
                                        Forms\Components\Repeater::make('timeClockEntries')
                                            ->label(false)
                                            ->relationship()
                                            ->columns(16)
                                            ->addable(false)
                                            ->deletable(false)
                                            ->schema([
                                                Forms\Components\DateTimePicker::make('clocked_or_approved_time_in')
                                                    ->label(__('Time In'))
                                                    ->readonly()
                                                    ->columnSpan(4),
                                                Forms\Components\DateTimePicker::make('clocked_or_approved_time_in')
                                                    ->label(__('Time Out'))
                                                    ->readonly()
                                                    ->columnSpan(4),
                                                Forms\Components\TextInput::make('minutes_deducted')
                                                    ->label(__('Deduct (Min)'))
                                                    ->live()
                                                    ->numeric()
                                                    ->columnSpan(2),
                                                Forms\Components\TextInput::make('deduction_reason')
                                                    ->label(__('Reason'))
                                                    ->string()
                                                    ->maxLength(255)
                                                    ->columnSpan(3),
                                                Forms\Components\TextInput::make('clocked_or_approved_hours_with_deduction')
                                                    ->label(__('Hours'))
                                                    ->readonly()
                                                    ->columnSpan(2),
                                            ]),
                                    ]),
                                Forms\Components\Section::make('Hours')
                                    ->extraAttributes(['class' => 'items-end-grid'])
                                    ->columnSpanFull()
                                    ->columns(6)
                                    ->hiddenOn('create')
                                    ->schema(array_merge(
                                        [
                                            Forms\Components\Placeholder::make('Hours')
                                                ->label('Hours Clocked')
                                                ->extraAttributes([
                                                    'class' => 'py-1.5 ps-3 pe-3 rounded-lg ring-1 sm:text-sm sm:leading-6 shadow-sm ring-1 bg-white dark:bg-white/5 ring-gray-950/10 dark:ring-white/20 overflow-hidden',
                                                ])
                                                ->content(function (Get $get, Set $set, PayrollSettings $settings) {
            
                                                    $hours = 0;
            
                                                        foreach ($get('timeClockEntries') as $entry) {
                                                        $hours += $entry['clocked_or_approved_hours_with_deduction'];
                                                    }

                                                    $splitHours = static::calculateRegularAndOvertimeHours($hours, $settings->hours_before_overtime);
            
                                                    $set(
                                                        'payTypes.' . $settings->regular_hours_pay_type, 
                                                        number_format($splitHours['regular'], 2)
                                                    );
                                                    $set(
                                                        'payTypes.' . $settings->overtime_hours_pay_type, 
                                                        number_format($splitHours['overtime'], 2)
                                                    );
            
                                                    return number_format($hours, 2);
            
                                                }),
                                        ],
                                        $payHours
                                    )),
                                Forms\Components\Section::make('Dollars')
                                    ->extraAttributes(['class' => 'items-end-grid'])
                                    ->columnSpan(1)
                                    ->columns(3)
                                    ->collapsed()
                                    ->hiddenOn('create')
                                    ->schema($payDollars),
                                Forms\Components\Section::make('Deductions')
                                    ->extraAttributes(['class' => 'items-end-grid'])
                                    ->columnSpan(1)
                                    ->columns(3)
                                    ->collapsed()
                                    ->hiddenOn('create')
                                    ->schema($deductions),
                            ])
                            ->itemLabel(function (array $state): ?string {
                                if (! array_key_exists('user_id', $state)) {
                                    return null;
                                }

                                return User::find($state['user_id'])->name ?? null;
                            }),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('id')
                    ->sortable(),
                Columns\TextColumn::make('period_ending')
                    ->dateTime('d-M-Y')
                    ->sortable(),
                Columns\TextColumn::make('payment_date')
                    ->dateTime('d-M-Y')
                    ->sortable(),
                Columns\TextColumn::make('approvedBy.name'),
                Columns\IconColumn::make('approved_at')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('period_ending', 'desc')
            ->recordClasses(fn (Model $record) => match ($record->deleted_at) {
                null => null,
                default => 'bg-danger-100 hover:bg-danger-200',
            })
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('batchUsers.payTypes');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBatches::route('/'),
            'create' => Pages\CreateBatch::route('/create'),
            'edit' => Pages\EditBatch::route('/{record}/edit'),
        ];
    }
    
}
