<?php

namespace App\Filament\Resources\Products\ProductResource\RelationManagers;

use App\Filament\Resources\Products\InspectionProcedureResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
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
            ->headerActions([
                Tables\Actions\AttachAction::make(),
            ]);
    }
}
