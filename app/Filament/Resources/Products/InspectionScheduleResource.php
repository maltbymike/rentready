<?php

namespace App\Filament\Resources\Products;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Product\Product;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Product\InspectionSchedule;
use App\Models\Product\InspectionProcedure;
use App\Enums\Product\InspectionQuestionTypeEnum;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Products\InspectionScheduleResource\Pages;
use App\Filament\Resources\Products\InspectionScheduleResource\RelationManagers;

class InspectionScheduleResource extends Resource
{
    protected static ?string $model = InspectionSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Products';

    public static function form(Form $form): Form
    {
        return $form
        ->columns(3)
        ->schema([
            Select::make('product_id')
                ->relationship(
                    name: 'product',
                    titleAttribute: 'name',
                )
                ->searchable(Product::searchFields())
                ->preload()
                ->getOptionLabelFromRecordUsing(fn (Product $record): string => $record->searchString())
                ->visibleOn('create'),
            Group::make()
                ->columnSpan(2)
                ->columns(3)
                ->relationship('product')
                ->schema([
                    TextInput::make('reference')
                        ->label('Reference')
                        ->readOnly(),
                    TextInput::make('name')
                        ->columnSpan(2)
                        ->label('Product')
                        ->readOnly(),
                ])
                ->hiddenOn('create'),
            Select::make('procedure_id')
                ->relationship(
                    name: 'procedure',
                    titleAttribute: 'name',
                )
                ->visibleOn('create')
                ->required(),
            Group::make()
                ->relationship('procedure')
                ->schema([
                    TextInput::make('name')
                        ->label('Procedure')
                        ->readOnly(),
                ])
                ->hiddenOn('create'),
            Repeater::make('questions')
                ->columnSpanFull()
                ->collapsible()
                ->collapsed()
                ->relationship()
                ->schema([
                    Select::make('type')
                        ->options(InspectionQuestionTypeEnum::class)
                        ->live(),
                    TextInput::make('question')
                        ->hidden(fn (Get $get): bool => $get('type') === InspectionQuestionTypeEnum::FromTemplate->value),
                    Select::make('options.templateProduct')
                        ->label('Get Inspection Template From')
                        ->options(
                            Product::selectRaw("id, concat(" . Product::searchFieldsAsCommaSeperatedStringWithDelimiter() . ") as searchString")
                                ->whereHas('inspectionSchedules')
                                ->pluck('searchString', 'id')
                        )
                        ->visible(fn (Get $get): bool => $get('type') === InspectionQuestionTypeEnum::FromTemplate->value)
                        ->live(),
                    Select::make('options.templateSchedule')
                        ->label('Inspection Schedule Template')
                        ->options(fn (Get $get): Collection => 
                            InspectionSchedule::where('product_id', $get('options.templateProduct'))
                                ->join('product_inspection_procedures', 'product_inspection_procedures.id', '=', 'product_inspection_schedules.procedure_id')
                                ->pluck('product_inspection_procedures.name', 'product_inspection_schedules.id')
                        )
                        ->visible(fn (Get $get): bool => $get('type') === InspectionQuestionTypeEnum::FromTemplate->value),
                    TextInput::make('options.placeholderText')
                        ->label('Placeholder Text')
                        ->visible(fn (Get $get): bool => $get('type') === InspectionQuestionTypeEnum::Text->value),
                    Toggle::make('options.toggleState')
                        ->label('What is the correct state of this toggle?')
                        ->inline(false)
                        ->onColor('success')
                        ->offColor('danger')
                        ->visible(fn (Get $get): bool => $get('type') === InspectionQuestionTypeEnum::Toggle->value),
                    RichEditor::make('description')
                        ->hidden(fn (Get $get): bool => $get('type') === InspectionQuestionTypeEnum::FromTemplate->value),
                ])
                ->itemLabel(fn (array $state): ?string => $state['question'] ?? null)
                ->orderColumn('order')
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.reference'),
                TextColumn::make('product.name'),
                TextColumn::make('procedure.name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListInspectionSchedules::route('/'),
            'create' => Pages\CreateInspectionSchedule::route('/create'),
            'edit' => Pages\EditInspectionSchedule::route('/{record}/edit'),
        ];
    }
}
