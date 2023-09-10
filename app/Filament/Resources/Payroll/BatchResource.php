<?php

namespace App\Filament\Resources\Payroll;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns;
use App\Models\Payroll\Batch;
use App\Models\Payroll\PayType;
use Filament\Resources\Resource;
use App\Models\Payroll\BatchUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Payroll\BatchResource\Pages;
use App\Filament\Resources\Payroll\BatchResource\RelationManagers;

class BatchResource extends Resource
{
    protected static ?string $model = Batch::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Payroll Batches';

    protected static ?string $navigationGroup = 'Payroll';

    public static function form(Form $form): Form
    {
        $batch = new Batch;
        
        return $form
            ->schema([
                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\DatePicker::make('period_ending')
                            ->label(__('Period Ending'))
                            ->displayFormat('Y-m-d')
                            ->default(fn () => $batch->getNextPayrollEndingDate())
                            ->disabledOn('edit'),
                        Forms\Components\DatePicker::make('payment_date')
                            ->label(__('Payment Date'))
                            ->displayFormat('Y-m-d')
                            ->default(fn () => $batch->getNextPayrollPaymentDate())
                            ->disabledOn('edit'),
                        Forms\Components\Select::make('approved_by')
                            ->relationship('approvedBy', 'name')
                            ->disabledOn('create'),
                        Forms\Components\DatePicker::make('approved_at')
                            ->label(__('Approved At'))
                            ->displayFormat('Y-m-d')
                            ->disabledOn('create'),
                        Forms\Components\Repeater::make('batchUsers')
                            ->columnSpanFull()
                            ->relationship()
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema(function () {
                                        $users = User::all()->pluck('name', 'id');
                                        $payTypes = PayType::all()->pluck('name', 'id');

                                        $returnUsersFields = [];

                                        $users->each(function (string $userName, int $userKey) use (&$returnUsersFields, $payTypes) {

                                            array_push(
                                                $returnUsersFields, 
                                                    Forms\Components\Select::make($userKey. '.user')
                                                        ->relationship('user', 'name')
                                                        ->disabledOn('edit')
                                                        ->default($userKey),
                                                    Forms\Components\Repeater::make('payTypes')
                                                        ->relationship()
                                                        ->schema(function () use ($payTypes, $userKey) {

                                                            $returnPayTypesFields = [];

                                                            $payTypes->each(function (string $payTypeValue, int $payTypeKey) use (&$returnPayTypesFields, $userKey) {
                                                                
                                                                array_push(
                                                                    $returnPayTypesFields,
                                                                    Forms\Components\Select::make($userKey . '.' . $payTypeKey . '.payType')
                                                                        ->options(PayType::all()->pluck('name', 'id')->toArray())
                                                                        ->default($payTypeKey),        
                                                                    Forms\Components\TextInput::make('value'),
                                                                );
                                                            });

                                                            return $returnPayTypesFields;
                                                        })
                                            );
                                            
                                        });

                                        return $returnUsersFields;
                                    }),
                            ])
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
            RelationManagers\BatchUsersRelationManager::class,
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
