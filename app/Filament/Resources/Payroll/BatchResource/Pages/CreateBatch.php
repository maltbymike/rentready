<?php

namespace App\Filament\Resources\Payroll\BatchResource\Pages;

use App\Models\Payroll\BatchUser;
use Filament\Pages\Actions;
use App\Models\Payroll\Batch;
use App\Traits\Payroll\SyncPayTypesToBatchUserTrait;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Payroll\BatchResource;

class CreateBatch extends CreateRecord
{
    protected static string $resource = BatchResource::class;

}
