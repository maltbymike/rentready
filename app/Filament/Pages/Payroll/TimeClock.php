<?php

namespace App\Filament\Pages\Payroll;

use App\Models\User;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Exists;

class TimeClock extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Time Clock';

    protected static ?string $navigationLabel = 'Clock In/Out';

    protected static string $view = 'filament.pages.time-clock';

    protected function getTableQuery(): Builder
    {
        if (auth()->user()->can('Manage Timeclock Entries')) {
            return User::timeclockUsers();
        }

        return User::where('id', auth()->user()->id);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->sortable()
                ->searchable(),
            ViewColumn::make('clocked_in')
                ->label('Clocked In')
                ->view('filament.resources.time-clock-entry-resource.tables.columns.is-user-clocked-in')
                ->alignCenter(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('Clock In')
                ->icon('heroicon-o-arrow-left-on-rectangle')
                ->action(fn (User $record) => $record->clockIn())
                ->visible(fn (User $record): bool => $record->isClockedIn() === false)
                ->form([
                    Forms\Components\TextInput::make('pin')
                        ->autofocus()
                        ->required()
                        ->disableAutocomplete()
                        ->exists(table: User::class, column: 'pin', modifyRuleUsing: function (Exists $rule, User $record) {
                            return $rule->where('id', $record->id);
                        }),

                ]),
            Action::make('Clock Out')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->action(fn (User $record) => $record->clockOut())
                ->visible(fn (User $record): bool => $record->isClockedIn() === true)
                ->form([
                    Forms\Components\TextInput::make('pin')
                        ->autofocus()
                        ->required()
                        ->disableAutocomplete()
                        ->exists(table: User::class, column: 'pin', modifyRuleUsing: function (Exists $rule, User $record) {
                            return $rule->where('id', $record->id);
                        }),
                ]),
        ];
    }

    protected function getTableFilters(): array
    {
        if (auth()->user()->can('Manage Timeclock Entries')) {
            return [
                Filter::make('onlyOwnRecords')
                    ->query(fn (Builder $query): Builder => $query->where('id', auth()->user()->id))
                    ->default(! auth()->user()->can('Manage Timeclock Entries')),
            ];
        }

        return [];
    }
}
