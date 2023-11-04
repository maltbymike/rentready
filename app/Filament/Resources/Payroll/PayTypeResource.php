<?php

namespace App\Filament\Resources\Payroll;

use App\Enums\Payroll\PayTypeEnum;
use App\Filament\Resources\Payroll\PayTypeResource\Pages;
use App\Filament\Resources\Payroll\PayTypeResource\RelationManagers;
use App\Models\Payroll\PayType;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayTypeResource extends Resource
{
    protected static ?string $model = PayType::class;

    protected static ?string $navigationGroup = 'Payroll';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Manage Pay Types';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                Select::make('type')
                    ->options(PayTypeEnum::class),
                Toggle::make('is_used_for_stat_pay')
                    ->label(__('Include in Stat Pay Calculation')),
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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
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
