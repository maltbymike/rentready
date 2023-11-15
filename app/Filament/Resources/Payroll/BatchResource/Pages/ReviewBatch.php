<?php

namespace App\Filament\Resources\Payroll\BatchResource\Pages;

use App\Models\Payroll\Batch;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Filament\Resources\Payroll\BatchResource;
use Filament\Infolists\Concerns\InteractsWithInfolists;

class ReviewBatch extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public Batch $record;

    protected static string $resource = BatchResource::class;

    protected static string $view = 'filament.resources.payroll.batch-resource.pages.review-batch';

    public function infolist(Infolist $infolist): Infolist {
        
        return $infolist
            ->record($this->record)
            ->schema([
                Section::make()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('period_ending')
                            ->date(),
                        TextEntry::make('payment_date')
                            ->date(),
                        TextEntry::make('approvedBy.name'),
                        TextEntry::make('approved_at')
                            ->date(),
                    ])
            ]);
    }
}
