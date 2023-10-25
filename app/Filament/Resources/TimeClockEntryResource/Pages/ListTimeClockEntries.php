<?php

namespace App\Filament\Resources\TimeClockEntryResource\Pages;

use App\Models\User;
use Filament\Pages\Actions;
use App\Models\TimeClockEntry;
use Filament\Tables\Actions\Action;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TimeClockEntryResource;

class ListTimeClockEntries extends ListRecords
{
    protected static string $resource = TimeClockEntryResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make()
                ->label('New Time Clock Entry'),
        ];
    }

    public function getTabs(): array {
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'Timeclock User');
        })->get();

        $tabs = $users->flatMap(fn ($user) => [
            $user->name => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', $user->id))
        ]);
       
        return $tabs->merge(['All' => Tab::make()])->toArray();
    }

}
