<?php

namespace App\Filament\Resources\Payroll\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Payroll\PayType;
use App\Enums\Payroll\PayTypeEnum;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextInputColumn;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
