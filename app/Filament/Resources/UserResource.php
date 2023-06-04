<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('User Information')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('password')
                                            ->password()
                                            ->dehydrateStateUsing(static fn (null|string $state):
                                                null|string => Hash::make($state))
                                            ->dehydrated(static fn (null|string $state):
                                                bool => filled($state))
                                            ->required(fn (string $context): bool => $context === 'create')
                                            ->maxLength(255)
                                            ->label(static fn(Page $livewire): string =>
                                                ($livewire instanceOf EditUser) ? "New Password" : "Password"
                                            ),
                                        Forms\Components\TextInput::make('pin')
                                            ->required()
                                            ->minLength('4')
                                            ->maxLength('10'),
                                    ]),
                            ]),
                        Tabs\Tab::make('Roles')
                            ->schema([
                                CheckboxList::make('roles')
                                    ->relationship('roles', 'name')
                                    ->columns([
                                        'default' => 2,
                                        'sm' => 3,
                                        'md' => 4,
                                        'lg' => 3,
                                        'xl' => 4,
                                        '2xl' => 5,
                                    ])
                                
                            ]),
                        
                    ]),

            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('roles.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime('d-M-Y')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-M-Y')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            // RolesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }    
}
