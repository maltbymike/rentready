<?php

namespace App\Filament\Resources\Payroll;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Payroll\PayType;
use Filament\Resources\Resource;
use App\Enums\Payroll\PayTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Traits\Payroll\HasSidebarPayrollSettingsTrait;
use App\Filament\Resources\Payroll\PayTypeResource\Pages;
use App\Filament\Resources\Payroll\PayTypeResource\RelationManagers;

class PayTypeResource extends Resource
{
    use HasSidebarPayrollSettingsTrait;
    
    protected static ?string $model = PayType::class;

    protected static ?string $navigationGroup = 'Payroll';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Manage Pay Types';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('details'),
                Select::make('type')
                    ->options(PayTypeEnum::class),
                Toggle::make('is_used_for_stat_pay')
                    ->label('Include in Stat Pay Calculation')
                    ->translateLabel()
                    ->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                'type',
                'name',
            ])
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('details')
                    ->sortable(),
                TextColumn::make('type')
                    ->sortable(),
                IconColumn::make('is_used_for_stat_pay')
                    ->label(__('Stat Pay Input'))
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('name')
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver(),
                Tables\Actions\DeleteAction::make()
                    ->label(__('Mark Inactive')),
                Tables\Actions\RestoreAction::make()
                    ->label(__('Mark Active')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePayTypes::route('/'),
        ];
    }    
}
