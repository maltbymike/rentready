<?php

namespace App\Filament\Resources\Payroll;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Payroll\PayType;
use Filament\Resources\Resource;
use App\Settings\PayrollSettings;
use App\Enums\Payroll\PayTypeEnum;
use Filament\Forms\Components\Grid;
use Filament\Panel\Concerns\HasSidebar;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Traits\Payroll\HasSidebarPayrollSettingsTrait;
use App\Filament\Resources\Payroll\EmployeeResource\Pages;
use App\Filament\Resources\Payroll\EmployeeResource\RelationManagers;

class EmployeeResource extends Resource
{
    use HasSidebarPayrollSettingsTrait;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Employee';

    protected static bool $shouldRegisterNavigation = false;

    public static function canCreate(): bool {
       return false;
    }

    public static function canDelete(Model $record): bool {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->disabled(),
                TextInput::make('email')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
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
    
    public static function getEloquentQuery(): Builder {
        return parent::getEloquentQuery()->employees();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    
    public static function getRelations(): array
    {
        return [
            RelationManagers\DefaultPayTypesRelationManager::class,
        ];
    }
}
