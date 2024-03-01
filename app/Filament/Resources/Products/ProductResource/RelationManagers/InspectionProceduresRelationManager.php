<?php

namespace App\Filament\Resources\Products\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Product\Inspections;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Product\InspectionSchedule;
use App\Models\Product\InspectionProcedure;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Products\InspectionsResource;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\Products\InspectionProcedureResource;

class InspectionProceduresRelationManager extends RelationManager
{
    protected static string $relationship = 'inspectionProcedures';

    protected static ?string $title = 'Inspection Procedures';

    public function form(Form $form): Form
    {
        // return InspectionProcedureResource::form($form);
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('test'),
            ]);
    }

    public function table(Table $table): Table
    {
        return InspectionProcedureResource::table($table)
            ->recordTitleAttribute('name')
            ->actions([
                Action::make('Create Inspection')
                    ->label('Create Inspection')
                    ->action(function (InspectionProcedure $record) {
                        $inspection = Inspections::create([
                            'schedule_id' => $record->pivot_id,
                        ]);
                        redirect(InspectionsResource::getUrl('edit', ['record' => $inspection]));
                    }),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ]);
    }
}
