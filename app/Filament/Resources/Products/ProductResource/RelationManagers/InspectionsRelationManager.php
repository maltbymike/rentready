<?php

namespace App\Filament\Resources\Products\ProductResource\RelationManagers;

use App\Models\Product\Inspections;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Products\InspectionsResource;
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
                TextColumn::make('created_at'),
                TextColumn::make('assignedTo.name'),
                IconColumn::make('started_at')
                    ->label('Started')
                    ->boolean(),
                IconColumn::make('completed_at')
                    ->label('Completed')
                    ->boolean(),
                IconColumn::make('approved_at')
                    ->label('Approved')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Action::make('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (Inspections $inspection): string => InspectionsResource::getUrl('edit', ['record' => $inspection])),
            ])
            ->bulkActions([
                //
            ]);
    }
}
