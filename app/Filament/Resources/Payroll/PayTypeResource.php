<?php

namespace App\Filament\Resources\Payroll;

use App\Enums\Payroll\PayTypeEnum;
use App\Filament\Resources\Payroll\PayTypeResource\Pages;
use App\Models\Payroll\PayType;
use App\Traits\Payroll\HasSidebarPayrollSettingsTrait;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

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
            ->defaultGroup('type')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('details')
                    ->sortable(),
                TextColumn::make('type')
                    ->sortable(),
                ToggleColumn::make('is_used_for_stat_pay')
                    ->label(__('Stat Pay Input'))
                    ->alignCenter()
                    ->sortable(),
                ToggleColumn::make('is_visible_on_batch_review')
                    ->label('Visible On Review')
                    ->alignCenter()
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
