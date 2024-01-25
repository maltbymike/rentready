<?php

namespace App\Filament\Resources\Products;

use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Product\Product;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Products\ProductResource\Pages;
use App\Filament\Resources\Products\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

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
                Select::make('parent')
                    ->label('Parent Item')
                    ->relationship(name: 'parent', titleAttribute: 'name')
                    ->searchable()
                    ->preload(),
                Tabs::make('Tabs')
                    ->columnSpanFull()
                    ->columns(3)
                    ->tabs([
                        Tab::make('Product Information')
                            ->schema([
                                Select::make('manufacturer')
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
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference'),
                TextColumn::make('name'),
                TextColumn::make('manufacturer.name'),
                TextColumn::make('model'),
                TextColumn::make('serial_number')
                    ->label('Serial Number'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
