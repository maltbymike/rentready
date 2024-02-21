<?php

namespace App\Filament\Resources\Products\ProductResource\RelationManagers;

use App\Filament\Resources\Products\InspectionProcedureResource;
use App\Filament\Resources\Products\InspectionsResource;
use App\Models\Product\InspectionProcedure;
use App\Models\Product\Inspections;
use App\Models\Product\InspectionSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InspectionSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'inspectionSchedules';

    public function form(Form $form): Form
    {
        return InspectionProcedureResource::form($form);
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
