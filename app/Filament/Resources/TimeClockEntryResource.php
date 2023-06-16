<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use App\Models\TimeClockEntry;
use App\Models\TimeClockStatus;
use Filament\Resources\Resource;
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
                    ->relationship('user', 'name'),
                Forms\Components\Select::make('status')
                    ->relationship('status', 'name'),
                Forms\Components\DateTimePicker::make('clock_in_at')
                    ->label(__('Clock In'))
                    ->weekStartsOnSunday()
                    ->withoutSeconds(),
                Forms\Components\DateTimePicker::make('clock_out_at')
                    ->label(__('Clock Out'))
                    ->weekStartsOnSunday()
                    ->withoutSeconds(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make([
                    Tables\Columns\TextColumn::make('user.name')
                        ->sortable()
                        ->searchable(),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('clock_in_at')
                            ->alignCenter()
                            ->dateTime('D Y-m-d'),
                        Tables\Columns\TextColumn::make('clock_in_at')
                            ->alignCenter()
                            ->dateTime('h:i:s A'),
                    ]),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('clock_out_at')
                            ->alignCenter()
                            ->dateTime('D Y-m-d'),
                        Tables\Columns\TextColumn::make('clock_out_at')
                            ->alignCenter()
                            ->dateTime('h:i:s A'),
                    ]),
                    Tables\Columns\TextColumn::make('hours')
                        ->getStateUsing(function (TimeClockEntry $record) {
                            return number_format($record->hours(), 2);
                        })
                        ->alignCenter(),
                    Tables\Columns\TextColumn::make('status.name')
                        ->alignCenter(),
                ])
            ])
            ->filters([
                Tables\Filters\Filter::make('onlyOwnRecords')
                    ->label('Only My Records')
                    ->query(fn (Builder $query): Builder => $query->where('user_id', auth()->user()->id))
                    ->default(),
                Tables\Filters\SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->multiple(),
                Tables\Filters\SelectFilter::make('employee')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve')
                        ->action(fn (TimeClockEntry $record) => $record->setEntryStatus('Approved'))
                        ->visible(fn (TimeClockEntry $record): bool => auth()->user()->can('update', $record)),
                    Tables\Actions\Action::make('reject')
                        ->action(fn (TimeClockEntry $record) => $record->setEntryStatus('Rejected'))
                        ->visible(fn (TimeClockEntry $record): bool => auth()->user()->can('update', $record)),
                    Tables\Actions\ReplicateAction::make()
                        ->label('Request Change')
                        ->modalHeading('Request Change to Time Clock Entry')
                        ->modalButton('Make Request')
                        ->form([
                            Forms\Components\DateTimePicker::make('clock_in_at')
                                ->label(__('Clock In'))
                                ->weekStartsOnSunday()
                                ->withoutSeconds(),
                            Forms\Components\DateTimePicker::make('clock_out_at')
                                ->label(__('Clock Out'))
                                ->weekStartsOnSunday()
                                ->withoutSeconds(),
                        ])
                        ->beforeReplicaSaved(function (TimeClockEntry $replica, TimeClockEntry $record, array $data): void {
                            $replica->fill($data);
                            $replica->setEntryStatus('Requested', false);
                        })
                        ->afterReplicaSaved(function (TimeClockEntry $replica, TimeClockEntry $record): void {
                            $record->alternates()->attach($replica);
                        })
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('approve')
                    ->action(function (Collection $records) {
                        $records->each(fn (TimeClockEntry $record) => $record->setEntryStatus('Approved'));
                    })
                    ->visible(fn (TimeClockEntry $record): bool => auth()->user()->can('update', $record))
                    ->deselectRecordsAfterCompletion(),
                Tables\Actions\BulkAction::make('reject')
                    ->action(function (Collection $records) {
                        $records->each(fn (TimeClockEntry $record) => $record->setEntryStatus('Rejected'));
                    })
                    ->visible(fn (TimeClockEntry $record): bool => auth()->user()->can('update', $record))
                    ->deselectRecordsAfterCompletion(),
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
