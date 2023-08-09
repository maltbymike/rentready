<?php

namespace App\Filament\Resources\Payroll\BatchResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use App\Models\Payroll\Details;
use App\Models\Payroll\PayType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class DetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';

    protected static ?string $recordTitleAttribute = 'id';

    // protected function getTableQuery(): Builder
    // {
        // return Details::query()
        //     ->select([
        //         'user_id',
        //         'payroll_pay_type_id',
        //         DB::raw('SUM(value) as total_value'),
        //     ])
        //     ->where('payroll_batch_id', $this->resource->getModel()->getKey())
        //     ->groupBy(['user_id', 'payroll_pay_type_id']);
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(['user_id', 'payroll_pay_type_id']),
                Tables\Columns\TextColumn::make('payType.name'),
                Tables\Columns\TextColumn::make('records'),
                Tables\Columns\TextColumn::make('value'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }    

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'user.name';
    }
 
    protected function getDefaultTableSortDirection(): ?string
    {
        return 'asc';
    }
}
