<?php

namespace App\Filament\Resources\Payroll\TimeClockEntryResource\Pages;

use App\Models\User;
use Filament\Pages\Actions;
use App\Models\Payroll\Batch;
use App\Models\Payroll\TimeClockEntry;
use Illuminate\Support\Collection;
use Filament\Tables\Actions\Action;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Payroll\TimeClockEntryResource;

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

        if (! auth()->user()->can('Manage Timeclock Entries')) {
            return [];
        }
        
        $users = User::whereHas('roles', function($q) {
            $q->where('name', 'Timeclock User');
        })->get();

        $timeClockEntriesByUserIds = TimeClockEntry::select('user_id', \DB::raw('count(*) as user_entries_count'))
            ->where('payroll_batch_user_id', null)
            ->where('clock_out_at', '<=', Batch::getNextPayrollEndingDate())
            ->groupBy('user_id')
            ->pluck('user_entries_count', 'user_id');

        $tabs = $users->flatMap(fn ($user) => [
            $user->name => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', $user->id))
                ->badge($timeClockEntriesByUserIds[$user->id] ?? 0)
        ]);
       
        return $tabs->merge([
                'All' => Tab::make()
                    ->badge($timeClockEntriesByUserIds->sum() ?? 0)
            ])
            ->toArray();
    }

}
