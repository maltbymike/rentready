<?php

namespace App\Enums\Product;

use Filament\Support\Contracts\HasLabel;

enum InspectionQuestionTypeEnum: string implements HasLabel
{

    case FromTemplate = 'template';
    case Text = 'text';
    case Toggle = 'toggle';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::FromTemplate => 'From Template',
            self::Text => 'Text',
            self::Toggle => 'Toggle',
        };
    }
}
