<?php

namespace App\Filament\Resources\Products\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class InspectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'inspections';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('schedule_id')
                    ->relationship(name: 'procedure', titleAttribute: 'name')
                    ->disabled(),
                Select::make('status_id')
                    ->relationship(name: 'status', titleAttribute: 'name'),
                DateTimePicker::make('created_at')
                    ->disabled()
                    ->hiddenOn('create'),
                Select::make('assigned_to_id')
                    ->relationship(name: 'assignedTo', titleAttribute: 'name'),
                DateTimePicker::make('started_at')
                    ->hiddenOn('create'),
                Select::make('completed_by_id')
                    ->relationship(name: 'completedBy', titleAttribute: 'name')
                    ->hiddenOn('create'),
                DateTimePicker::make('completed_at')
                    ->hiddenOn('create'),
                Select::make('approved_by_id')
                    ->relationship(name: 'approvedBy', titleAttribute: 'name')
                    ->hiddenOn('create'),
                DateTimePicker::make('approved_at')
                    ->hiddenOn('create'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('completed_at')
            ->columns([
                TextColumn::make('schedule.procedure.name'),
                TextColumn::make('status.name'),
                TextColumn::make('created_at'),
                TextColumn::make('assignedTo.name'),
                TextColumn::make('started_at'),
                TextColumn::make('completedBy.name'),
                TextColumn::make('completed_at'),
                TextColumn::make('approvedBy.name'),
                TextColumn::make('approved_at')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
