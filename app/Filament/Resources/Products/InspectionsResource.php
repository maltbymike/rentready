<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\ProductResource\RelationManagers\InspectionsRelationManager;
use App\Models\Product\Product;
use Closure;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
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
                    ->content(fn (Inspections $record): string => $record->product->name)
                    ->visibleOn('edit'),
                Placeholder::make('procedure')
                    ->content(fn (Inspections $record): string => $record->procedure->name)
                    ->visibleOn('edit'),
                Placeholder::make('started_at')
                    ->label('Started At')
                    ->content(fn (Inspections $record): ?string => $record->started_at)
                    ->hidden(fn (Get $get) => $get('started_at') === null)
                    ->visibleOn('edit'),
                Placeholder::make('assigned_to')
                    ->label('Assigned To')
                    ->content(fn (Inspections $record): ?string => $record->assignedTo->name ?? '')
                    ->hidden(fn (Get $get) => $get('started_at') === null)
                    ->visibleOn('edit'),
                Actions::make([
                    Action::make('startInspection')
                        ->label('Start Inspection')
                        ->extraAttributes([
                            'class' => 'w-full',
                        ])
                        ->size(ActionSize::ExtraLarge)
                        ->hidden(fn (Get $get) => $get('started_at') !== null)
                        ->fillForm(fn (Inspections $record): array => [
                            'assignedTo' => Auth::user()->id,
                        ])
                        ->form([
                            Select::make('assignedTo')
                                ->label('Inspected By')
                                ->options(User::query()->pluck('name', 'id'))
                                ->required(),
                        ])
                        ->action(function (array $data, Inspections $record, Request $request): void {
                            $record->started_at = now()->format('Y-m-d H:i:s');
                            $record->assignedTo()->associate($data['assignedTo']);
                            $record->save();
                            redirect(InspectionsResource::getUrl('edit', ['record' => $record]));
                        })
                ])
                ->columnSpanFull(),
                Placeholder::make('completed_at')
                    ->label('Completed At')
                    ->content(fn (Inspections $record): ?string => $record->completed_at)
                    ->hidden(fn (Get $get) => $get('completed_at') === null),
                Placeholder::make('completed_by')
                    ->label('Completed By')
                    ->content(fn (Inspections $record): string => $record->completedBy->name ?? '')
                    ->hidden(fn (Get $get) => $get('completed_at') === null),
                Actions::make([
                    Action::make('completeInspection')
                        ->label('Complete Inspection')
                        ->extraAttributes([
                            'class' => 'w-full',
                        ])
                        ->size(ActionSize::ExtraLarge)
                        ->hidden(fn (Get $get) => $get('completed_at') !== null || $get('started_at') === null)
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
                            $record->completed_at = now()->format('Y-m-d H:i:s');
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
                TextColumn::make('product.name')
                    ->hiddenOn(InspectionsRelationManager::class),
                TextColumn::make('procedure.name'),
                TextColumn::make('created_at'),
                TextColumn::make('assignedTo.name')
                    ->label('Assigned To'),
                IconColumn::make('started_at')
                    ->label('Started')
                    ->boolean(),
                IconColumn::make('completed_at')
                    ->label('Completed')
                    ->boolean(),
                IconColumn::make('approved_at')
                    ->label('Approved')
                    ->boolean(),
                IconColumn::make('approved_at')
                    ->label('Approved')
                    ->boolean(),
            ])
            ->filters([
                TernaryFilter::make('completed_at')
                    ->nullable()
                    ->label('Completed')
                    ->default(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
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
