<?php

namespace App\Filament\Resources\Payroll;

use App\Filament\Resources\Payroll\BatchResource\Pages;
use App\Filament\Resources\Payroll\BatchResource\RelationManagers;
use App\Models\Payroll\Batch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BatchResource extends Resource
{
    protected static ?string $model = Batch::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Payroll Details';

    protected static ?string $navigationGroup = 'Payroll';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\DatePicker::make('period_ending')
                            ->label(__('Period Ending'))
                            ->displayFormat('Y-m-d')
                            ->disabled(),
                        Forms\Components\DatePicker::make('payment_date')
                            ->label(__('Payment Date'))
                            ->displayFormat('Y-m-d')
                            ->disabled(),
                        Forms\Components\Select::make('approved_by')
                            ->relationship('approvedBy', 'name')
                            ->disabled(),
                        Forms\Components\DatePicker::make('approved_at')
                            ->label(__('Approved At'))
                            ->displayFormat('Y-m-d')
                            ->disabled(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('id')
                    ->sortable(),
                Columns\TextColumn::make('period_ending')
                    ->dateTime('d-M-Y')
                    ->sortable(),
                Columns\TextColumn::make('payment_date')
                    ->dateTime('d-M-Y')
                    ->sortable(),
                Columns\TextColumn::make('approvedBy.name'),
                Columns\IconColumn::make('approved_at')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
            RelationManagers\DetailsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBatches::route('/'),
            'create' => Pages\CreateBatch::route('/create'),
            'edit' => Pages\EditBatch::route('/{record}/edit'),
        ];
    }    
}
