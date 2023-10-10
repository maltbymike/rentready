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
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Fieldset;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Forms\Components\CheckboxList;
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
        // $batch = new Batch;
        
        // $batchUsers = User::whereHas('roles', 
        //     function(Builder $query) {
        //         return $query->where('name', 'Employee');
        //     }
        // )
        // ->get();

        // $payTypes = PayType::all();

        // $batchUserFields = $batchUsers->map(
        //     function (User $user) use ($payTypes) {
        //         return Fieldset::make($user->name)
        //             ->hiddenOn('create')
        //             ->schema(
        //                 $payTypes->map(
        //                     function (PayType $type) use ($user) 
        //                     {
        //                         return TextInput::make('user.' . $user->id . '.payTypes.' . $type->id)
        //                             ->label($type->name);
        //                     }
        //                 )->all()
        //             )
        //             ->columns([
        //                 'xs' => 1,
        //                 'sm' => 3,
        //                 'md' => 4,
        //                 'lg' => 3,
        //                 'xl' => 5,
        //             ]);
                    
        //     }
        // )->all();

        // $batchFormComponents = [
        //     Grid::make(4)
        //         ->schema([
        //             DatePicker::make('period_ending')
        //                 ->label(__('Period Ending'))
        //                 ->displayFormat('Y-m-d')
        //                 ->default(fn () => $batch->getNextPayrollEndingDate())
        //                 ->disabledOn('edit'),
        //             DatePicker::make('payment_date')
        //                 ->label(__('Payment Date'))
        //                 ->displayFormat('Y-m-d')
        //                 ->default(fn () => $batch->getNextPayrollPaymentDate())
        //                 ->disabledOn('edit'),
        //             Select::make('approved_by')
        //                 ->relationship('approvedBy', 'name')
        //                 ->disabledOn('create'),
        //             DatePicker::make('approved_at')
        //                 ->label(__('Approved At'))
        //                 ->displayFormat('Y-m-d')
        //                 ->disabledOn('create'),
        //             CheckboxList::make('users')
        //                 ->label('Employees To Pay')
        //                 ->bulkToggleable()
        //                 ->relationship(
        //                     'Users', 
        //                     titleAttribute: 'name',
        //                     modifyQueryUsing: fn (Builder $query) => 
        //                         $query->whereHas('roles', 
        //                             function(Builder $query) {
        //                                 return $query->where('name', 'Employee');
        //                             }                                )
        //                 ),    
        //         ])
        // ];

        return $form
            ->schema([
                Grid::make(4)
                    ->schema([
                        DatePicker::make('period_ending')
                            ->label(__('Period Ending'))
                            ->displayFormat('Y-m-d')
                            ->default(fn () => Batch::getNextPayrollEndingDate())
                            ->disabledOn('edit'),
                        DatePicker::make('payment_date')
                            ->label(__('Payment Date'))
                            ->displayFormat('Y-m-d')
                            ->default(fn () => Batch::getNextPayrollPaymentDate())
                            ->disabledOn('edit'),
                        Select::make('approved_by')
                            ->relationship('approvedBy', 'name')
                            ->disabledOn('create'),
                        DatePicker::make('approved_at')
                            ->label(__('Approved At'))
                            ->displayFormat('Y-m-d')
                            ->disabledOn('create'),
                        CheckboxList::make('users')
                            ->label('Employees To Pay')
                            ->bulkToggleable()
                            ->relationship(
                                'Users', 
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => 
                                    $query->whereHas('roles', 
                                        function(Builder $query) {
                                            return $query->where('name', 'Employee');
                                        }                                )
                            ),    
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
            ->defaultSort('period_ending', 'desc')
            ->recordClasses(fn (Model $record) => match ($record->deleted_at) {
                null => null,
                default => 'bg-danger-100 hover:bg-danger-200',
            })
            ->filters([
                TrashedFilter::make(),
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
