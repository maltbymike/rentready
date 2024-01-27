<?php

namespace App\Filament\Resources\Products;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Product\Product;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Products\ProductResource\Pages;
use App\Filament\Resources\Products\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Products';
    
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->columnSpanFull()
                    ->label('Product Name')
                    ->string()
                    ->autofocus()
                    ->required(),
                TextInput::make('reference')
                    ->label('Reference')
                    ->string()
                    ->unique(ignoreRecord: true)
                    ->required(),
                Select::make('parent_id')
                    ->label('Parent Item')
                    ->relationship(
                        name: 'parent', 
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('is_header', true),
                    )
                    ->searchable(['name', 'reference'])
                    ->getOptionLabelFromRecordUsing(fn (Product $record) => "{$record->reference} - {$record->name}")
                    ->preload(),
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->columns(3)
                    ->tabs([
                        Tab::make('Product Information')
                            ->schema([
                                Select::make('manufacturer_id')
                                    ->label('Manufacturer')
                                    ->relationship(name: 'manufacturer', titleAttribute: 'name')
                                    ->searchable()
                                    ->preload()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->autofocus()
                                            ->required(),
                                    ]),
                                TextInput::make('model')
                                    ->label('Model')
                                    ->string(),
                                TextInput::make('model_type')
                                    ->label('Model Type')
                                    ->string(),
                                TextInput::make('serial_number')
                                    ->label('Serial Number')
                                    ->string(),
                                TextInput::make('model_year')
                                    ->label('Model Year')
                                    ->numeric(),
                            ]),
                        Tab::make('Product Configuration')
                            ->schema([
                                Toggle::make('is_header')
                                    ->label('Header Item'),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultGroup('parent.name')
            ->groups([
                Group::make('parent.name')
                    ->titlePrefixedWithLabel(false),
            ])
            ->columns([
                TextColumn::make('reference'),
                TextColumn::make('name'),
                TextColumn::make('manufacturer.name'),
                TextColumn::make('model'),
                TextColumn::make('serial_number')
                    ->label('Serial Number'),
            ])
            ->filters([
                Filter::make('is_header')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->where('is_header', false))
                    ->label('Hide Header Items')
                    ->default(),
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
            RelationManagers\ProceduresRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
