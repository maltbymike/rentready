<?php

namespace App\Filament\Resources\Products;

use Closure;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Http\Request;
use Filament\Resources\Resource;
use App\Models\Product\Inspections;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use phpDocumentor\Reflection\Types\Void_;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Products\InspectionsResource\Pages;
use App\Filament\Resources\Products\InspectionsResource\RelationManagers;

class InspectionsResource extends Resource
{
    protected static ?string $model = Inspections::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Products';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('product')
                    ->content(fn (Inspections $record): string => $record->product->name),
                Placeholder::make('procedure')
                    ->content(fn (Inspections $record): string => $record->procedure->name),
                // DateTimePicker::make('started_at')
                //     ->hidden(fn (Get $get) => $get('started_at') === null)
                //     ->reactive()
                //     ->readonly(),
                Placeholder::make('started_at')
                    ->label('Started At')
                    ->content(fn (Inspections $record): string => $record->started_at),
                Placeholder::make('inspected_by')
                    ->label('Inspected By')
                    ->content(fn (Inspections $record): string => $record->completedBy->name)
                    ->hidden(fn (Get $get) => $get('started_at') === null),
                Actions::make([
                    Action::make('startInspection')
                        ->label('Start Inspection')
                        ->extraAttributes([
                            'class' => 'w-full',
                        ])
                        ->size(ActionSize::ExtraLarge)
                        ->hidden(fn (Get $get) => $get('started_at') !== null)
                        ->fillForm(fn (Inspections $record): array => [
                            'completedBy' => Auth::user()->id,
                        ])
                        ->form([
                            Select::make('completedBy')
                                ->label('Inspected By')
                                ->options(User::query()->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function (array $data, Inspections $record, Request $request): void {
                            $record->started_at = now()->format('Y-m-d H:i:s');
                            $record->completedBy()->associate($data['completedBy']);
                            $record->save();
                            redirect(InspectionsResource::getUrl('edit', ['record' => $record]));
                        })
                ])
                ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name'),
                TextColumn::make('procedure.name'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInspections::route('/'),
            'create' => Pages\CreateInspections::route('/create'),
            'edit' => Pages\EditInspections::route('/{record}/edit'),
        ];
    }
}
