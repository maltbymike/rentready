<?php

namespace App\Filament\Resources\Products\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InspectionSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'inspectionSchedules';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('procedure_id')
                    ->relationship(
                        name: 'procedure',
                        titleAttribute: 'name',
                    )
                    ->required(),
                Repeater::make('questions')
                    ->relationship()
                    ->schema([
                        TextInput::make('question'),
                        RichEditor::make('description'),

                    ])
                    ->orderColumn('order')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('procedure.name')
            ->columns([
                Tables\Columns\TextColumn::make('procedure.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
