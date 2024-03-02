<?php

namespace App\Enums\Product;

use Filament\Support\Contracts\HasLabel;

enum InspectionQuestionTypeEnum: string implements HasLabel
{
    case Text = 'text';
    case Toggle = 'toggle';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Text => 'Text',
            self::Toggle => 'Toggle',
        };
    }
}
