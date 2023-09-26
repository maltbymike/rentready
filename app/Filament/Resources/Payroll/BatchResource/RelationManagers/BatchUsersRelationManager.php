<?php

namespace App\Filament\Resources\Payroll\BatchResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Payroll\PayType;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class BatchUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'batchUsers';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        $types = PayType::all()->map(function (PayType $type) {
            return TextInput::make('payTypes.' . $type->id)
                ->label($type->name);
        })->all();

        return $form
            ->schema(
                array_merge([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Employee')
                        ->disabledOn('edit'),
                ],
                $types
            ));
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('payTypes'))
            ->columns([
                Tables\Columns\TextColumn::make('user_id'),
                Tables\Columns\TextColumn::make('user.email'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (Model $record, array $data): array {
                        foreach ($record->payTypes as $payType) {
                            $data['payTypes'][$payType->id] = $payType->pivot->value;
                        }

                        return $data;
                    })
                    ->using(function (Model $record, array $data): Model {
                        return $this->syncPayTypes($record, $data['payTypes']);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->inverseRelationship('payrollBatches');
    }

    protected function syncPayTypes (Model $record, array $payTypes): Model {
        $payTypes = collect($payTypes)
                    ->filter()
                    ->map(function ($value, $key) {
                        return ['value' => $value];
                    })
                    ->toArray();
        $record->payTypes()->sync($payTypes);
        return $record;
    }

}
