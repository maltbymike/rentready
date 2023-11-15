<?php

namespace App\Filament\Resources\Payroll\BatchResource\Pages;

use Closure;
use Filament\Tables\Table;
use App\Models\Payroll\Batch;
use App\Models\Payroll\PayType;
use Filament\Infolists\Infolist;
use App\Models\Payroll\BatchUser;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Resources\Payroll\BatchResource;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ReviewBatch extends Page implements HasForms, HasInfolists, HasTable
{
    use InteractsWithForms;
    use InteractsWithInfolists;
    use InteractsWithTable;

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

    public function table(Table $table): Table
    {
        return $table
            ->query(BatchUser::where('payroll_batch_id', $this->record->id)->with(['user', 'payTypes']))
            ->columns(
                array_merge(
                    [
                        TextColumn::make('user.name'),
                    ], 
                    PayType::all()->map(function (PayType $type) {
                        return TextColumn::make($type->name_label)
                            ->alignment(Alignment::Center)
                            ->label(
                                fn () => $type->details 
                                    ? new HtmlString($type->name.'<br />('.$type->details.')') 
                                    : $type->name
                            )
                            ->state(fn (BatchUser $record) => 
                                $record->payTypes->where('id', $type->id)->first()->pivot->value ?? null
                            );
                    })->toArray(),
                )
            )
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }
    
}
