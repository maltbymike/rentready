<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use App\Models\TimeClockEntry;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
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
                Forms\Components\DateTimePicker::make('clock_in_at')
                    ->label(__('Clock In'))
                    ->weekStartsOnSunday()
                    ->withoutSeconds(),
                Forms\Components\DateTimePicker::make('clock_out_at')
                    ->label(__('Clock Out'))
                    ->weekStartsOnSunday()
                    ->withoutSeconds(),
                Forms\Components\Select::make('approved_by')
                    ->label(__('Approved By'))
                    ->relationship('user', 'name')
                    ->default(auth()->user()->id),
                Forms\Components\DateTimePicker::make('approved_at')
                    ->label(__('Approved At'))
                    ->weekStartsOnSunday()
                    ->withoutSeconds(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('clock_in_at')->dateTime(),
                Tables\Columns\TextColumn::make('clock_out_at')->dateTime(),
                Tables\Columns\TextColumn::make('approved_at')->dateTime(),
            ])
            ->filters([
                Tables\Filters\Filter::make('onlyOwnRecords')
                    ->label('Only My Records')
                    ->query(fn (Builder $query): Builder => $query->where('user_id', auth()->user()->id))
                    ->default(),
                Tables\Filters\TernaryFilter::make('status')
                    ->attribute('approved_at')
                    ->trueLabel('Approved')
                    ->falseLabel('Unapproved')
                    ->default(false)
                    ->nullable(),
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
        if (! auth()->user()->can('Manage Timeclock Entries')) {
            return parent::getEloquentQuery()->where('user_id', auth()->user()->id);   
        }

        return parent::getEloquentQuery();
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
