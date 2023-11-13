<?php

namespace App\Filament\Resources\Payroll\EmployeeResource\RelationManagers;

use App\Enums\Payroll\PayTypeEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DefaultPayTypesRelationManager extends RelationManager
{
    protected bool $allowsDuplicates = true;

    protected static string $relationship = 'defaultPayTypes';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->options(PayTypeEnum::class)
                    ->disabled(),
                TextInput::make('details'),
                TextInput::make('default_value')
                    ->label('Default Value')
                    ->translateLabel(),
                DatePicker::make('effective_date')
                    ->label('Effective Date')
                    ->translateLabel(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->allowDuplicates()
            ->defaultGroup('type')
            ->groups([
                'type',
            ])
            ->recordTitleAttribute('name_label')
            ->columns([
                TextColumn::make('name_label'),
                TextColumn::make('default_value'),
                TextColumn::make('effective_date')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->label('Add')
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->required(),
                        TextInput::make('default_value')
                            ->required()
                            ->numeric(),
                        DatePicker::make('effective_date')
                            ->default(now())
                            ->required(),
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
