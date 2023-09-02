<?php

namespace App\Filament\Resources\Payroll\BatchResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Payroll\Details;
use App\Models\Payroll\PayType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->groups([
                'user.name',
                'payType.name',
            ])
            ->defaultGroup('user.name')
            // ->groupsOnly()
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('user.name'),
                Tables\Columns\TextColumn::make('payType.name')
                    ->sortable(['payType.name']),
                Tables\Columns\TextColumn::make('value')
                    ->summarize([
                        Sum::make()->label('Regular Hours'),
                        Sum::make()->label('Overtime Hours'),
                    ]),
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
        return 'payType.name';
    }
 
    protected function getDefaultTableSortDirection(): ?string
    {
        return 'asc';
    }
}
