<?php

namespace App\Filament\Tables\Columns;

use App\Models\TimeClockEntry;
use Filament\Tables\Columns\Column;

class ClockIn extends Column
{
    protected string $view = 'filament.tables.columns.clock-in';
}
