<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use App\Models\Payroll\Batch;
use App\Models\TimeClockEntry;
use Filament\Forms\Components;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->disabled()
                    ->columnSpan(12),
                Forms\Components\Fieldset::make('Time In')
                    ->schema([
                        Forms\Components\Radio::make('approve_clock_in')
                            ->label('Approve')
                            ->options([
                                'clock' => 'Clock Time',
                                'requested' => 'Requested Time',
                                'other' => 'Alternate Time',
                            ])
                            ->inline()
                            ->extraAttributes(['class' => 'pl-6'])
                            ->columnSpan(6),
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
                            ->columnSpan(6),
                        Forms\Components\DateTimePicker::make('clock_in_approved')
                            ->label(__('Clock In Approved'))
                            ->format('Y-m-d H:i:s')
                            ->displayFormat('D Y-m-d h:i A')
                            ->weekStartsOnSunday()
                            ->withoutSeconds()
                            ->columnSpan(6),            
                    ])
                    ->columns(6)
                    ->columnSpan(6),
                Forms\Components\Fieldset::make('Time Out')
                    ->schema([
                        Forms\Components\Radio::make('approve_clock_out')
                        ->label('Approve')
                        ->options([
                            'clock' => 'Clock Time',
                            'requested' => 'Requested Time',
                            'other' => 'Alternate Time',
                        ])
                        ->inline()
                        ->extraAttributes(['class' => 'pl-6'])
                        ->columnSpan(6),
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
                            ->columnSpan(6),
                        Forms\Components\DateTimePicker::make('clock_out_approved')
                            ->label(__('Clock Out Approved'))
                            ->format('Y-m-d H:i:s')
                            ->displayFormat('D Y-m-d h:i A')
                            ->weekStartsOnSunday()
                            ->withoutSeconds()
                            ->columnSpan(6),
                    ])
                    ->columns(6)
                    ->columnSpan(6),
            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('user.name')
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
                Tables\Columns\TextColumn::make('hours_clocked')
                    ->label('Hours')
                    ->alignRight()
                    ->summarize(Sum::make()
                        ->label(false)
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Employee')
                    ->multiple()
                    ->relationship('user', 'name'),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                ]),
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

                    }),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {

        $query = parent::getEloquentQuery();

        if (! auth()->user()->can('Manage Timeclock Entries')) {
            $query = $query->where('user_id', auth()->user()->id);
        }

        $query = $query->select(['*', \DB::raw(TimeClockEntry::getClockedHoursAsRawSqlString())]);

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTimeClockEntries::route('/'),
            'create' => Pages\CreateTimeClockEntry::route('/create'),
            'edit' => Pages\EditTimeClockEntry::route('/{record}/edit'),
        ];
    }    

}
