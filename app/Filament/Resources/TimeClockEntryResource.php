<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TimeClockEntryResource\Pages;
use App\Filament\Resources\TimeClockEntryResource\RelationManagers;
use App\Models\TimeClockEntry;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
