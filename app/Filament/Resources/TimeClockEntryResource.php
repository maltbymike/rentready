<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use App\Models\TimeClockEntry;
use Filament\Resources\Resource;
use App\Filament\Tables\Columns\ClockIn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TimeClockEntryResource\Pages;
use App\Filament\Resources\TimeClockEntryResource\RelationManagers;

class TimeClockEntryResource extends Resource
{
    protected static ?string $model = TimeClockEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Payroll';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->columnSpan(2),
                Forms\Components\Fieldset::make('Time In')
                    ->schema([
                        Forms\Components\DateTimePicker::make('clock_in_at')
                            ->label(__('Clock In'))
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('clock_in_requested')
                            ->label(__('Clock In Requested'))
                            ->format('Y-m-d H:i:s')
                            ->weekStartsOnSunday()
                            ->withoutSeconds(),
                        Forms\Components\DateTimePicker::make('clock_in_approved')
                            ->label(__('Clock In Approved'))
                            ->format('Y-m-d H:i:s')
                            ->weekStartsOnSunday()
                            ->withoutSeconds(),            
                    ])
                    ->columns(1)
                    ->columnSpan(1),
                Forms\Components\Fieldset::make('Time Out')
                    ->schema([
                        Forms\Components\DateTimePicker::make('clock_out_at')
                            ->label(__('Clock Out'))
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('clock_out_requested')
                            ->label(__('Clock Out Requested'))
                            ->weekStartsOnSunday()
                            ->withoutSeconds(),
                        Forms\Components\DateTimePicker::make('clock_out_approved')
                            ->label(__('Clock Out Approved'))
                            ->weekStartsOnSunday()
                            ->withoutSeconds(),
                    ])
                    ->columns(1)
                    ->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('user.name')
                        ->sortable()
                        ->searchable(),
                    ClockIn::make('clock_in_at'),
                    ClockIn::make('clock_out_at'),
                    Tables\Columns\TextColumn::make('hours')
                        ->getStateUsing(function (TimeClockEntry $record) {
                            return number_format($record->hours(), 2);
                        })
                        ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Employee')
                    ->multiple()
                    ->relationship('user', 'name')
                    ->default([auth()->user()->id]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    // Tables\Actions\Action::make('approve')
                    //     ->action(fn (TimeClockEntry $record) => $record->setEntryStatus('Approved'))
                    //     ->visible(fn (TimeClockEntry $record): bool => auth()->user()->can('update', $record)),
                    // Tables\Actions\Action::make('reject')
                    //     ->action(fn (TimeClockEntry $record) => $record->setEntryStatus('Rejected'))
                    //     ->visible(fn (TimeClockEntry $record): bool => auth()->user()->can('update', $record)),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\BulkAction::make('approve')
                //     ->action(function (Collection $records) {
                //         $records->each(fn (TimeClockEntry $record) => $record->setEntryStatus('Approved'));
                //     })
                //     ->visible(fn (TimeClockEntry $record): bool => auth()->user()->can('update', $record))
                //     ->deselectRecordsAfterCompletion(),
                // Tables\Actions\BulkAction::make('reject')
                //     ->action(function (Collection $records) {
                //         $records->each(fn (TimeClockEntry $record) => $record->setEntryStatus('Rejected'));
                //     })
                //     ->visible(fn (TimeClockEntry $record): bool => auth()->user()->can('update', $record))
                //     ->deselectRecordsAfterCompletion(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {

        $query = parent::getEloquentQuery()
            ->orderBy('user_id')
            ->orderBy('clock_in_at');

        if (! auth()->user()->can('Manage Timeclock Entries')) {
            $query = $query->where('user_id', auth()->user()->id);
        }

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
