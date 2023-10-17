<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use App\Models\Payroll\Batch;
use App\Models\TimeClockEntry;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Alignment;
use App\Filament\Tables\Columns\ClockIn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TimeClockEntryResource\Pages;
use App\Filament\Resources\TimeClockEntryResource\RelationManagers;

class TimeClockEntryResource extends Resource
{
    protected static ?string $model = TimeClockEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Payroll';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->disabled()
                    ->columnSpan(12),
                Forms\Components\Fieldset::make('Time In')
                    ->schema([
                        Forms\Components\DateTimePicker::make('clock_in_at')
                            ->label(__('Clock In'))
                            ->displayFormat('D Y-m-d h:i A')
                            ->withoutSeconds()
                            ->disabled()
                            ->columnSpan(6),
                        Forms\Components\DateTimePicker::make('clock_in_requested')
                            ->label(__('Clock In Requested'))
                            ->format('Y-m-d H:i:s')
                            ->displayFormat('D Y-m-d h:i A')
                            ->weekStartsOnSunday()
                            ->withoutSeconds()
                            ->columnSpan(4),
                        Forms\Components\Select::make('approve_requested_clock_in')
                            ->options(['1' => 'Approve', '0' => 'Reject'])
                            ->label(__('Approve'))
                            ->placeholder('')
                            ->hidden(function (Get $get) {
                                if (! auth()->user()->can('Manage Timeclock Entries')) {
                                    return true;
                                }

                                return $get('clock_in_at') === $get('clock_in_requested');
                            })
                            ->columnSpan(2),
                    ])
                    ->columns(6)
                    ->columnSpan(4),
                Forms\Components\Fieldset::make('Time Out')
                    ->schema([
                        Forms\Components\DateTimePicker::make('clock_out_at')
                            ->label(__('Clock Out'))
                            ->displayFormat('D Y-m-d h:i A')
                            ->withoutSeconds()
                            ->disabled()
                            ->columnSpan(6),
                        Forms\Components\DateTimePicker::make('clock_out_requested')
                            ->label(__('Clock Out Requested'))
                            ->format('Y-m-d H:i:s')
                            ->displayFormat('D Y-m-d h:i A')
                            ->weekStartsOnSunday()
                            ->withoutSeconds()
                            ->columnSpan(4),
                        Forms\Components\Select::make('approve_requested_clock_out')
                            ->options(['1' => 'Approve', '0' => 'Reject'])
                            ->label(__('Approve'))
                            ->placeholder('')
                            ->hidden(function (Get $get) {
                                if (! auth()->user()->can('Manage Timeclock Entries')) {
                                    return true;
                                }

                                return $get('clock_out_at') === $get('clock_out_requested');
                            })
                            ->columnSpan(2),
                    ])
                    ->columns(6)
                    ->columnSpan(4),
                Forms\Components\Fieldset::make('Deductions')
                    ->schema([
                        Forms\Components\TextInput::make('minutes_deducted')
                            ->numeric(),
                        Forms\Components\TextInput::make('deduction_reason')
                            ->string()
                            ->maxLength(255),
                    ])
                    ->columnSpan(4),
            ])
            ->columns(12);
    }

    public static function table(Table $table): Table {
        return $table
            ->defaultGroup('user.name')
            ->groups([
                'user.name',
                Group::make('clock_out_at')
                    ->label('Date')
                    ->getTitleFromRecordUsing(
                        function (TimeClockEntry $record): string {
                            return $record->clock_out_at->format('D n/j');
                        }
                    ),
            ])
            ->columns([
                Columns\TextColumn::make('payrollBatch.period_ending')
                    ->label('Period Ending')
                    ->sortable()
                    ->date(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                ClockIn::make('clock_in_at')
                    ->alignCenter(),
                ClockIn::make('clock_out_at')
                    ->alignCenter(),
                Tables\Columns\TextInputColumn::make('minutes_deducted')
                    ->alignCenter()
                    ->inputMode('numeric')
                    ->rules(['integer', 'required']),
                Tables\Columns\TextInputColumn::make('deduction_reason')
                    ->rules(['string', 'nullable', 'max:255']),
                Tables\Columns\TextColumn::make('hours_clocked_with_deduction')
                    ->label('Hours')
                    ->alignRight()
                    ->summarize(Sum::make()
                        ->label(false)
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Employee')
                    ->multiple()
                    ->relationship('user', 'name')
                    ->preload(),
                Tables\Filters\SelectFilter::make('Period Ending')
                    ->relationship('payrollBatch', 'period_ending'),
                Tables\Filters\TernaryFilter::make('pending')
                    ->nullable()
                    ->attribute('payroll_batch_user_id')
                    ->placeholder('Show All')
                    ->trueLabel('No')
                    ->falseLabel('Yes')
                    ->default(false),
                Tables\Filters\Filter::make('cutoff_date')
                    ->form([
                        DatePicker::make('cutoff_date')
                            ->default(Batch::getNextPayrollEndingDate()),
                    ])
                    ->indicateUsing(function (array $data): ?string {
                        if (! $data['cutoff_date']) {
                            return null;
                        }

                        return 'Cutoff Date: ' . $data['cutoff_date'];
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['cutoff_date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('clock_out_at', '<=', $date),
                            );
                    })
            ], 
            layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->actions([
                
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('assignToPayrollBatch')
                    ->form([
                        Select::make('periodEnding')
                            ->options(function () {
                                return Batch::query()
                                    ->get()
                                    ->mapWithKeys(function ($batch, $key) {
                                        $dateString = $batch->period_ending->toDateString();
                                        return [$dateString => $batch->period_ending->toDateString()];
                                    })
                                    ->reverse()
                                    ->toArray();
                            })
                            ->placeholder('Create New: ' . Batch::getNextPayrollEndingDate()->toDateString())
                            ->rules(['dateformat:Y-m-d']),
                    ])
                    ->action(function (array $data, Collection $records): void {                        
                        $batch = Batch::firstOrCreate(
                            ['period_ending' => $data['periodEnding'] ?? Batch::getNextPayrollEndingDate()],
                            ['payment_date' => Batch::getNextPayrollPaymentDate()]
                        );

                        $batch->users()->syncWithoutDetaching($records->pluck('user_id')->unique()->toArray());

                        $batch->load('users');

                        $records->each(function (TimeClockEntry $record) use (&$batch) {
                            $record->batchUser()->associate($batch->users->find($record->user_id)->pivot->id);
                            $record->save();
                        });
                    })
                    ->visible(auth()->user()->can('Manage Timeclock Entries')),
                Tables\Actions\BulkAction::make('removeFromPayrollBatch')
                    ->action(function (array $data, Collection $records): void {                        
                        $records->each(function (TimeClockEntry $record) {
                            $record->batchUser()->disassociate();
                            $record->save();
                        });
                    })
                    ->visible(auth()->user()->can('Manage Timeclock Entries'))
                    ->requiresConfirmation(),
            ]);
    }

    public static function getEloquentQuery(): Builder {

        $query = parent::getEloquentQuery();

        if (! auth()->user()->can('Manage Timeclock Entries')) {
            $query = $query->where('user_id', auth()->user()->id);
        }

        $query = $query->select(['*', \DB::raw(TimeClockEntry::getClockedOrApprovedHoursWithDeductionAsRawSqlString())]);

        return $query;
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }
    
    public static function getPages(): array {
        return [
            'index' => Pages\ListTimeClockEntries::route('/'),
            'create' => Pages\CreateTimeClockEntry::route('/create'),
            'edit' => Pages\EditTimeClockEntry::route('/{record}/edit'),
        ];
    }    

}
