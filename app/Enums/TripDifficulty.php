<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum TripDifficulty: string implements HasColor, HasLabel
{
    case Easy = 'easy';
    case Moderate = 'moderate';
    case Challenging = 'challenging';
    case Strenuous = 'strenuous';

    public function getLabel(): string
    {
        return match ($this) {
            self::Easy => 'Easy',
            self::Moderate => 'Moderate',
            self::Challenging => 'Challenging',
            self::Strenuous => 'Strenuous',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Easy => 'success',
            self::Moderate => 'info',
            self::Challenging => 'warning',
            self::Strenuous => 'danger',
        };
    }
}
