<?php

namespace App\Filament\Resources\Products\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Product\Inspections;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Products\InspectionsResource;
use Filament\Resources\RelationManagers\RelationManager;

class InspectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'inspections';

    protected static ?string $title = 'Inspection History';

    public function form(Form $form): Form
    {
        return InspectionsResource::form($form);
    }

    public function table(Table $table): Table
    {
        return InspectionsResource::table($table)
            ->filters([
                TernaryFilter::make('completed_at')
                    ->nullable()
                    ->label('Completed'),
            ]);
    }
}
